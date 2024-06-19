<!-- resources/views/components/semester-header-table.blade.php -->
<thead>

    @php

    $hoursWeeksSemesters = json_decode($plan->hours_weeks_semesters, JSON_OBJECT_AS_ARRAY);

    function getMaxHour($semester, $obj) {
    foreach ($obj as $item) {
    if ($item['semester'] === $semester) {
    return $item;
    }
    }
    return null;
    }

    $courseArray = range(1,$plan->studyTerm['course']);
    $semesterArray = range(1, $plan->studyTerm['semesters']);
    @endphp

    <tr class="table-subtitle">
        <td class="border-table table-subtitle" colspan="100%">
            V. ПЛАН НАВЧАЛЬНОГО ПРОЦЕСУ
        </td>
    </tr>
    <tr>
        <td class="border-table" rowspan="6">№</td>
        <td class="border-table" rowspan="6">Назви навчальних дисциплін</td>
        <td class="border-table" rowspan="1" colspan="3">Розподіл контрольних заходів за семестрами</td>
        <td class="border-table" rowspan="6">Кількість кредитів ЄКТС</td>
        <td class="border-table" rowspan="1" colspan="6">Кількість годин</td>
        <td class="border-table" rowspan="1" colspan="{{ $plan->studyTerm['semesters'] }}">
            Розподіл годин на тиждень за курсами, семестрами і модульними атестаційними циклами
        </td>
    </tr>
    <tr>
        <td class="border-table" rowspan="5">Екзамени</td>
        <td class="border-table" rowspan="5">Заліки</td>
        <td class="border-table" rowspan="5">Індивідуальні завдання</td>
        <td class="border-table" rowspan="5">загальний обсяг</td>
        <td class="border-table" rowspan="1" colspan="4">аудиторних</td>
        <td class="border-table" rowspan="5">самостійна робота</td>

        @foreach($courseArray as $course)
        <td class="border-table" colspan="{{ $plan->studyTerm['semesters'] % count($courseArray) > 0 && $course === end($$courseArray) ? 1 : 2 }}">
            {{ $course }} курс
        </td>
        @endforeach
    </tr>
    <tr>
        <td class="border-table" rowspan="4">всього</td>
        <td class="border-table" rowspan="1" colspan="3">у тому числі:</td>
        <td class="border-table" rowspan="1" colspan="{{ $plan->studyTerm['semesters'] }}">Семестри</td>
    </tr>
    <tr>
        <td class="border-table" colspan="3">&nbsp;</td>
        @foreach($semesterArray as $semester)
        <td class="border-table">{{ $semester }}</td>
        @endforeach
    </tr>
    <tr>
        <td class="border-table" rowspan="2">лекції</td>
        <td class="border-table" rowspan="2">практичні, семінарські</td>
        <td class="border-table" rowspan="2">лабораторні</td>
        <td class="border-table" colspan="{{ $plan->studyTerm['semesters'] }}">
            Кількість тижнів теоретичної підготовки в модульному циклі
        </td>
    </tr>
    <tr>
        @foreach($semesterArray as $semester)
        <td class="border-table">
            @if($maxHour = getMaxHour($semester, $hoursWeeksSemesters))
            {{ $maxHour['week'] }}
            @endif
        </td>
        @endforeach
    </tr>
    <tr>
        @for($item = 1; $item <= 12; $item++) <td class="border-table">{{ $item }}</td>
            @endfor
            @for($item = 1; $item <= $plan->studyTerm['semesters']; $item++) <td class="border-table">{{ $item + 12 }}</td>
                @endfor
    </tr>
</thead>