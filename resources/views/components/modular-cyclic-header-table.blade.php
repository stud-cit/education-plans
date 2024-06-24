<thead>
    @php
    if (!function_exists('getMaxHour')) {
    function getMaxHour($semester, $index, $obj) {
    foreach ($obj as $item) {
    if ($item['semester'] === $semester && $item['index'] === $index) {
    return $item;
    }
    }
    return null;
    }
    }

    $courseArray = range(1,$plan->studyTerm['course']);
    $semesterArray = range(1, $plan->studyTerm['semesters']);
    @endphp
    <tr class="table-subtitle">
        <td class="border-table table-subtitle" colspan="100%">V. ПЛАН НАВЧАЛЬНОГО ПРОЦЕСУ</td>
    </tr>
    <tr>
        <td class="border-table" rowspan="8" width="20">№</td>
        <td class="border-table" rowspan="8" width="180">Назви навчальних дисциплін</td>
        <td class="border-table" rowspan="1" colspan="3" width="200">Розподіл контрольних заходів за семестрами</td>
        <td class="border-table" rowspan="8" width="50">Кількість кредитів ЄКТС</td>
        <td class="border-table" rowspan="1" colspan="6">Кількість годин</td>
        <td class="border-table" rowspan="1" colspan="{{ $plan->studyTerm['semesters'] * 2 }}">
            Розподіл годин на тиждень за курсами, семестрами і модульними атестаційними циклами
        </td>
    </tr>
    <tr>
        <td class="border-table" rowspan="7">Екзамени</td>
        <td class="border-table" rowspan="7">Заліки</td>
        <td class="border-table" rowspan="7">Індивідуальні завдання</td>
        <td class="border-table" rowspan="7">загальний обсяг</td>
        <td class="border-table" rowspan="1" colspan="4">аудиторних</td>
        <td class="border-table" rowspan="7">самостійна робота</td>

        @foreach($courseArray as $course)
        <td class="border-table" colspan="{{ $plan->studyTerm['semesters'] % count($courseArray) > 0 && $course === end($courseArray) ? 2 : 4 }}">
            {{ $course }} курс
        </td>
        @endforeach
    </tr>
    <tr>
        <td class="border-table" rowspan="6">всього</td>
        <td class="border-table" rowspan="1" colspan="3">у тому числі:</td>
        <td class="border-table" rowspan="1" colspan="{{ $plan->studyTerm['semesters'] * 2 }}">Семестри</td>
    </tr>
    <tr>
        <td class="border-table" colspan="3" rowspan="1">&nbsp;</td>
        @foreach($semesterArray as $semester)
        <td class="border-table" colspan="2">{{ $semester }}</td>
        @endforeach
    </tr>
    <tr>
        <td class="border-table" rowspan="4">лекції</td>
        <td class="border-table" rowspan="4">практичні, семінарські</td>
        <td class="border-table" rowspan="4">лабораторні</td>
        <td class="border-table" rowspan="1" colspan="{{ $plan->studyTerm['semesters'] * 2 }}">Модульні атестаційні цикли</td>
    </tr>
    <tr>
        @foreach($courseArray as $course)
        <td class="border-table" rowspan="1">I</td>
        <td class="border-table" rowspan="1">II</td>
        @if($plan->studyTerm['semesters'] % count($courseArray) == 0 || ($plan->studyTerm['semesters'] % count($courseArray) > 0 && $course !== end($courseArray)))
        <td class="border-table" rowspan="1">III</td>
        <td class="border-table" rowspan="1">IV</td>
        @endif
        @endforeach
    </tr>
    <tr>
        <td class="border-table" rowspan="1" colspan="{{ $plan->studyTerm['semesters'] * 2 }}">
            Кількість тижнів теоретичної підготовки в модульному циклі
        </td>
    </tr>

    <tr>
        @foreach($semesterArray as $semester)
        @for($index = 1; $index <= 2; $index++) <td class="border-table" rowspan="1" colspan="1">
            @php
            $maxHour = getMaxHour($semester, $index, $hoursWeeksSemesters);
            @endphp
            @if($maxHour)
            {{ $maxHour['week'] }}
            @endif
            </td>
            @endfor
            @endforeach
    </tr>
    <tr>
        @for($item = 1; $item <= 12; $item++) <td class="border-table">{{ $item }}</td>
            @endfor

            @for($item = 1; $item <= $plan->studyTerm['semesters'] * 2; $item++)
                <td class="border-table">{{ $item + 12 }}</td>
                @endfor
    </tr>

</thead>