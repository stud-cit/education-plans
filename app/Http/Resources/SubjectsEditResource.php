<?php

namespace App\Http\Resources;

use App\Http\Constant;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class SubjectsEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "cycle_id" => $this->cycle_id,
            "selective_discipline_id" => $this->selective_discipline_id,
            "asu_id" => $this->asu_id,
            "credits" => $this->credits,
            "hours" => $this->hours,
            "practices" => $this->practices,
            "laboratories" => $this->laboratories,
            "verification" => $this->verification,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            "note" => $this->note,
            "title" => $this->title,
            "selective_discipline" => $this->whenLoaded('selectiveDiscipline'),
            "semesters_credits" => $this->whenLoaded('semestersCredits'),
            "hours_modules" => $this->whenLoaded('hoursModules'),
            'checkCountHoursModules' => $this->checkCountHoursModules(),
            'checkCountHours' => $this->checkCountHours(),
            'checkLastHourModule' => $this->checkLastHourModule(),
            'checkCountHoursSemester' => $this->checkCountHoursSemester(),
            'checkHasCreditsSemester' => $this->checkHasCreditsSemester()
        ];
    }


    function checkCountHoursModules()
    {
        $sumHoursModules = 0;
        $sumHours = $this->hours + $this->practices + $this->laboratories; // беремо суму годин практичних, лекцій, лабораторних
        $hours_modules = $this->whenLoaded('hoursModules'); // тянемо розподілені години за модулями
        $hours_weeks_semesters = json_decode($this->cycle->plan->hours_weeks_semesters); // тянемо кількість тижнів у модульному атестаційному циклі (загальна інформація нижня таблиця)
        foreach ($hours_modules as $key => $value) { // пробігаємося по масику тижнів
            $sumHoursModules += $hours_weeks_semesters[$key]->week * $value->hour; // перемножаємо кількість тижнів на години для кожного модуля і сумуємо результат
        }
        return $sumHoursModules == $sumHours;
    }

    function checkCountHours()
    {
        $sumHours = $this->hours + $this->practices + $this->laboratories; // беремо суму годин практичних, лекцій, лабораторних
        return ($this->credits * 30 * ($this->getOptions('min-classroom-load') / 100) > $sumHours || // кредити дисципліни множимо на 30 (це константа) і множимо на мінімальне аудиторне навантаження по дисципліні у відсотках (налаштування -> загальні обмеження), ділимо на 100 і перевіряємо за загальним навантаженням. результат має бути такий що відсоток мінімального навантаження не повинен бути більше кількості годин
            $this->credits * 30 * ($this->getOptions('max-classroom-load') / 100) < $sumHours // теж саме тільки з максимальним відсотком навантаження
        ); // суть така що години повинні бути в межах цих відсотків обрахованих формулою
    }

    function checkLastHourModule()
    {
        $res = null;
        $semesters_credits = $this->whenLoaded('semestersCredits')->toArray();
        $hours_modules = $this->whenLoaded('hoursModules')->toArray();
        $semestersCredits = array_filter($semesters_credits, function ($item) {
            return isset($item['credit']) && $item['credit'];
        });
        $lastSemestersCredits = end($semestersCredits);
        if ($lastSemestersCredits) {
            $hoursModules = array_filter($hours_modules, function ($item) use ($lastSemestersCredits) {
                return $item['semester'] == $lastSemestersCredits['semester'];
            });
            $lastItem = end($hoursModules);

            if ($lastItem && $lastItem['form_control_id'] == 10) {
                $res = array_search($lastItem, $hours_modules);
            }
        }
        return $res;
    }

    function checkCountHoursSemester()
    {
        $res = [];
        $semesters_credits = $this->whenLoaded('semestersCredits'); // беремо розподіл кредитів на вивчення за семестрами (нижня таблиця в дисципліні)
        $hours_weeks_semesters = json_decode($this->cycle->plan->hours_weeks_semesters); // тянемо кількість тижнів у модульному атестаційному циклі (загальна інформація нижня таблиця) 
        $hours_modules = $this->whenLoaded('hoursModules'); // години за модулями
        for ($index = 0; $index < count($semesters_credits); $index++) {
            $semesterItem = $semesters_credits[$index];

            // тут логіка приблизно така як і в checkCountHoursModules та checkCountHours тільки ми пробігаємось по кожному семестру і сумуємо години в межах кожного семестру (якщо наприклад у семестрі 2 модулі то години потрібно помножити на кількість тижнів і додати) та перевіряємо по формулі чи відповідає вказаному діапапону у відсотках

            $modules = array_map(function ($item, $index) use ($hours_weeks_semesters) {
                $item['checkHour'] = $item['hour'] * $hours_weeks_semesters[$index]->week; // перемножаємо кількість тижнів на години для кожного модуля і сумуємо результат
                return $item;
            }, $hours_modules->toArray(), array_keys($hours_modules->toArray()));

            $modules = array_filter($modules, function ($elem) use ($semesterItem) {
                return $elem['semester'] == $semesterItem['semester'];
            });

            $sumHoursModules = array_reduce(array_map(function ($item) use ($modules) {
                return $item['checkHour'];
            }, $modules), function ($prev, $curr) {
                return $prev + $curr;
            }, 0); // сумуємо години

            if ($semesterItem->credit * 30 * ($this->getOptions('min-classroom-load') / 100) > $sumHoursModules || $semesterItem->credit * 30 * ($this->getOptions('max-classroom-load') / 100) < $sumHoursModules) { // перевіряємо діапазон
                $res[] = $semesterItem->semester;
            }
        }
        return $res; // в результаті повертає номери семестрів які не відповідають критеріям
    }

    function checkHasCreditsSemester()
    {
        return count($this->semestersCredits->where('credit', '!=', 0)) > 0 ? true : false; // перевіряємо ци є кредити хоча б в одному семестрі
    }

    function getOptions($key)
    {
        // TODO: set cache options;
        $options = Setting::select('id', 'key', 'value')->pluck('value', 'key');
        // TODO: KEY EXIST?
        return $options[$key];
    }
}
