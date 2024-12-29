<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExternalServices\Asu\Worker;
use App\ExternalServices\Asu\Schedule;
use App\ExternalServices\Asu\Subjects;
use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Profession;
use App\Http\Resources\FacultiesResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Asu\WorkerResource;
use App\Http\Resources\Asu\SchedulResource;
use App\Http\Resources\Asu\SubjectResource;
use App\Http\Resources\DepartmentsResource;
use App\Http\Resources\ProfessionsResource;
use App\Http\Resources\Asu\DepartmentResource;

class AsuController extends Controller
{
    public function faculties()
    {
        $department = new Department();

        return FacultiesResource::collection($department->getFaculties());
    }

    public function departmentById(Request $request)
    {
        $department = new Department();

        $data = $department->getDepartmentsByStructuralId($request->id);

        return DepartmentsResource::collection($data);
    }

    public function getSpecializations(Request $request, int $id)
    {
        $professions = new Profession();

        return ProfessionsResource::collection($professions->getSpecializations($id));
    }

    public function getSpecialities(Request $request, $id)
    {
        $professions = new Profession();

        return ProfessionsResource::collection($professions->getSpecialties($id));
    }

    public function getAllSpecialities()
    {
        $professions = new Profession();

        return ProfessionsResource::collection($professions->getAllSpecialties());
    }

    public function getEducationPrograms(Request $request, $id)
    {
        $professions = new Profession();
        $result = $professions->getAllEducationPrograms()->filter(fn($profession) => $profession['speciality_id'] == $id);


        return ProfessionsResource::collection($result);
    }

    public function getAllEducationPrograms()
    {
        $professions = new Profession();

        return ProfessionsResource::collection($professions->getAllEducationPrograms());
    }

    public function getSubjects()
    {
        $subjects = new Subjects();
        return SubjectResource::collection($subjects->getSubjects());
    }

    public function getDepartments()
    {
        $subject = new Department();
        return DepartmentResource::collection($subject->getStructuralDepartment());
    }

    public function getWorkers()
    {
        $worker = new Worker();
        // ->sortBy('full_name')->values()
        return WorkerResource::collection($worker->getAllWorkers());
    }

    public function getSchedules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'education_level_id' => 'required|integer',
            'study_term_id' => 'required|integer',
        ])->validate();

        $schedule = new Schedule();

        return SchedulResource::collection($schedule->list($validator['education_level_id'], $validator['study_term_id']));
    }

    public function getScheduleById(Request $request, int $id)
    {
        $schedule = new Schedule();

        return $schedule->getItem($id)->groupBy('course')->values();
    }
}
