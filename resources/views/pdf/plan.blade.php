<!DOCTYPE html>
<html lang="uk">

<head>
    <!-- <meta name=" viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('pdf.css') }}" type="text/css">
</head>

<body>
    <table class="table plan-title-table">
        <tbody>
            <tr class="table-title">
                <td colspan="{{$fullColspanTitle * 2}}" align="center">СУМСЬКИЙ ДЕРЖАВНИЙ УНІВЕРСИТЕТ</td>
            </tr>
            <tr class="table-faculty">
                <td colspan="{{$fullColspanTitle *2}}">
                    {{ $plan->facultyName }}
                </td>
            </tr>
            <tr class="table-text">
                <td colspan="{{25 * 2}}">Затверджено рішенням вченої ради.</td>
            </tr>
            <tr class="table-text">
                <td colspan="{{25 * 2}}">Протокол від ____._____________._____р.№____</td>
            </tr>
            <tr class="table-text">
                <td colspan="{{25 * 2}}">Голова ради ________________ Анатолій ВАСИЛЬЄВ</td>
            </tr>
            <tr class="table-text">
                <td colspan="25" style="text-align: center">(підпис)</td>
            </tr>
            <tr class="table-text">
                <td colspan="{{25 * 2}}">______ ________________________ ________ р.</td>
            </tr>
            <tr class="table-text">
                <td style="text-align: center" colspan="25">М.П.</td>
            </tr>
            <tr></tr>

            <tr>
                <td colspan="100%" align="center" class="table-title">НАВЧАЛЬНИЙ ПЛАН</td>
            </tr>
            <tr></tr>

            @foreach ($professions as $index => $td)
            <tr>
                @foreach ($td as $i => $item)
                <td @if (isset($item['colspan'])) colspan="{{ $item['colspan'] *2 }}" @elseif (isset($item['acolspan'])) colspan="{{ (($fullColspanTitle - $item['acolspan'] * 2) / 2) *2 }}" @endif @class([ 'table-profession-title'=> isset($item['title']), 'table-profession-text' => isset($item['key']) ])>

                    @if ( isset($item['title']) )
                    {{$item['title']}}
                    @elseif (isset($item['key']))
                    {{$item['key']}}
                    @else
                    <div style="border-bottom: 1px solid black; height: 10pt;"></div>

                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <tr>
                <td colspan="100%">&nbsp;</td>
            </tr>
            <!-- end header document -->
        </tbody>
    </table>
    <table class="table border-table">
        <caption class="table-subtitle">І . ГРАФІК НАВЧАЛЬНОГО ПРОЦЕСУ, тижні</caption>

        <x-education.month :month=$header />
        <x-education.weeks :weeks=$weeks />

        @foreach ($courses as $key => $course)
        <tr class="table-month text-center">
            <td class="border-table" colspan="2">
                {{ $key + 1 }}
            </td>
            @foreach ($course as $item)
            <td class="table-week border-table" colspan="2">
                {{ $item['val'] }}
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>

    <p class="table-sing">ПОЗНАЧЕННЯ: {{ $notes }}</p>
    <!-- HEADER END -->
    <table class="collapsed border-table">
        <tbody>
            @php
            $course = 20;
            $theoreticalTraining = 15;
            $examSession = 20;
            $practicalTraining = 15;
            $vacation = 50;
            $all = 40;
            $qualifyingWork = 40;
            $qualifyingExams = 40;
            $attestation = $qualifyingWork + $qualifyingExams;
            $header1 = $theoreticalTraining + $examSession + $course + $practicalTraining + $vacation + $all + $attestation;

            $name = 20; $semester = 30; $countWeeks = 20; $countCredits = 20; $courseHeader = 40;
            $header2 = $course + $name + $semester + $countWeeks + $countCredits;

            $count = 20; $examsTitle = 20; $examsSemester = 20;
            $header3 = $count + $examsTitle + $examsSemester;

            @endphp
            <tr class="table-subtitle">
                <td colspan="{{$header1}}" class="border-table">ІІ. ЗВЕДЕНІ ДАНІ ПРО БЮДЖЕТ ЧАСУ, тижні</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="{{ $header2 }}" class="border-table">ІІІ. ПРАКТИЧНА ПІДГОТОВКА</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="{{ $header3 }}" class="border-table">ІV. АТЕСТАЦІЯ</td>
            </tr>


            <tr class="subtable">
                <td rowspan="2" colspan="{{ $course }}" class="border-table">Курс</td>
                <td rowspan="2" colspan="{{ $theoreticalTraining }}" class="border-table">Теоретична підготовка</td>
                <td rowspan="2" colspan="{{ $examSession }}" class="border-table">Екзаменаційна сесія</td>
                <td rowspan="2" colspan="{{ $practicalTraining }}" class="border-table">Практична підготовка</td>
                <td colspan="{{$attestation}}" class="border-table">Атестація</td>
                <td rowspan="2" colspan="{{$vacation}}" class="border-table">Канікули</td>
                <td rowspan="2" colspan="{{$all}}" class="border-table">Усього</td>

                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>

                <td colspan="{{ $course }}" rowspan="2" class="border-table">№</td>
                <td colspan="{{ $name }}" rowspan="2" class="border-table">Назва</td>
                <td colspan="{{ $semester }}" rowspan="2" class="border-table">Семестр</td>
                <td colspan="{{ $countWeeks }}" rowspan="2" class="border-table">Число тижнів</td>
                <td colspan="{{ $countCredits }}" rowspan="2" class="border-table">Число кредитів</td>

                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>

                <td colspan="{{ $count }}" rowspan="2" class="border-table">№</td>
                <td colspan="{{ $examsTitle }}" rowspan="2" class="border-table">Форма</td>
                <td colspan="{{ $examsSemester }}" rowspan="2" class="border-table">Семестр</td>
            </tr>
            <tr class="subtable">
                <td colspan="{{$qualifyingWork}}" class="border-table">Кваліфікаційна робота</td>
                <td colspan="{{$qualifyingExams}}" class="border-table">Кваліфікаційні (атестаційні) іспити</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            @php
            // Assuming $plan data is available from the controller
            $maxRows = max(
            count($exams_table),
            count($summary_data_budget_time),
            count($practical_training),
            ceil($number_semesters / 2)
            );
            @endphp
            <tr>
                @for ($i = 0; $i < $maxRows; $i++) @if ($summary_data_budget_time[$i]) <td colspan="{{$course}}" class="border-table text-center">
                    {{$summary_data_budget_time[$i]['course'] }}
                    </td>
                    <td colspan="{{$theoreticalTraining}}" class="border-table text-center">
                        {{$summary_data_budget_time[$i]['theoretical_training'] }}
                    </td>
                    <td colspan="{{$examSession}}" class="border-table text-center">
                        {{$summary_data_budget_time[$i]['exam_session'] }}
                    </td>
                    <td colspan="{{$practicalTraining}}" class="border-table text-center">
                        {{$summary_data_budget_time[$i]['practical_training']}}
                    </td>
                    <td colspan="{{$qualifyingWork}}" class="border-table text-center">
                        {{$summary_data_budget_time[$i]['bachelor_qualifying_work'] }}
                    </td>
                    <td colspan="{{$qualifyingExams}}" class="border-table text-center">
                        {{$summary_data_budget_time[$i]['attestation_qualifying_exams'] }}
                    </td>
                    <td colspan="{{$vacation}}" class="border-table text-center">
                        {{$summary_data_budget_time[$i]['vacation'] }}
                    </td>
                    <td colspan="{{$all}}" class="border-table text-center">
                        {{$summary_data_budget_time[$i]['all'] }}
                    </td>

                    @else

                    <td colspan="{{ $course }}" class="border-table {{ $study_term['course'] > $i ? 'border-table' : '' }}">
                        {{ $study_term['course'] > $i ? $i + 1 : '' }}
                    </td>
                    <td colspan="{{ $course }}" class="border-table {{ $plan->study_term['course'] > $i ? 'border-table' : '' }}">&nbsp;</td>
                    <td colspan="{{$name}}" class="border-table {{ $plan->study_term['course'] > $i ? 'border-table' : '' }}">&nbsp;</td>
                    <td colspan="{{$semester}}" class="border-table {{ $plan->study_term['course'] > $i ? 'border-table' : '' }}">&nbsp;</td>
                    <td colspan="{{5 * 2}}" class="border-table {{ $plan->study_term['course'] > $i ? 'border-table' : '' }}">&nbsp;</td>
                    <td colspan="{{4 * 2}}" class="border-table {{ $plan->study_term['course'] > $i ? 'border-table' : '' }}">&nbsp;</td>
                    <td colspan="{{ $countWeeks }}" class="border-table {{ $plan->study_term['course'] > $i ? 'border-table' : '' }}">&nbsp;</td>
                    <td colspan="{{ $countCredits }}" class="border-table {{ $plan->study_term['course'] > $i ? 'border-table' : '' }}">&nbsp;</td>
                    @endif

                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>

                    @if (isset($practical_training[$i]))
                    <td colspan="{{ $course }}" class="border-table">{{ $i + 1 }}</td>
                    <td colspan="{{ $name }}" class="border-table">{{ $practical_training[$i]['name'] }}</td>
                    <td colspan="{{ $semester }}" class="border-table">{{ $practical_training[$i]['semester'] }}</td>
                    <td colspan="{{ $countWeeks }}" class="border-table">{{ $practical_training[$i]['week'] }}</td>
                    <td colspan="{{ $countCredits }}" class="border-table">{{ $practical_training[$i]['credit'] }}</td>
                    @else
                    <td colspan="{{ $course }}">&nbsp;</td>
                    <td colspan="{{ $name }}">&nbsp;</td>
                    <td colspan="{{ $semester }}">&nbsp;</td>
                    <td colspan="{{ $countWeeks }}">&nbsp;</td>
                    <td colspan="{{ $countCredits }}">&nbsp;</td>
                    @endif

                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>

                    @if (isset($exams_table[$i]))
                    <td colspan="{{ $count }}" class="border-table">{{ $exams_table[$i] ? $i + 1 : '' }}</td>
                    <td colspan="{{ $examsTitle }}" class="border-table">
                        {{ $exams_table[$i] ? $exams_table[$i]['title'] : '' }}
                    </td>
                    <td colspan="{{ $examsSemester }}" class="border-table">
                        {{ $exams_table[$i] ? $exams_table[$i]['semester'] : '' }}
                    </td>
                    @else
                    <td colspan="{{$count}}">&nbsp;&nbsp;</td>
                    <td colspan="{{$examsTitle}}">&nbsp;&nbsp;</td>
                    <td colspan="{{$examsSemester}}">&nbsp;&nbsp;</td>
                    @endif

            </tr>
            @endfor

        </tbody>
    </table>

    @php
    //dd('end');
    @endphp
    {{--
    <table class="table table-plan page-break-before" width="100%">
        @if ($plan->form_organization_id == $FORM_ORGANIZATIONS['modular_cyclic'])
        <x-modular-cyclic-header-table :plan="$plan" />
        @endif

        @if ($plan->form_organization_id == $FORM_ORGANIZATIONS['semester'])
        <x-semester-header-table :plan="$plan" />
        @endif
        <tbody>
            <x-cycle-table :cycles="$cycles" :plan="$plan" :const="$FORM_ORGANIZATIONS_TABLE" />

            <tr class="table-bold">
                <td class="border-table">&nbsp;</td>
                <td colspan="1" class="text-left border-table text-bold">Загальна кількість</td>
                <td class="border-table text-bold" colspan="{{ 3 }}"></td>
    <td class="border-table text-bold">{{ $totalPlan['credits'] }}</td>
    <td class="border-table text-bold">{{ $totalPlan['total_volume_hour'] }}</td>
    <td class="border-table text-bold">{{ $totalPlan['total_classroom'] }}</td>
    <td class="border-table text-bold">{{ $totalPlan['hours'] }}</td>
    <td class="border-table text-bold">{{ $totalPlan['practices'] }}</td>
    <td class="border-table text-bold">{{ $totalPlan['laboratories'] }}</td>
    <td class="border-table text-bold">{{ $totalPlan['individual_work'] }}</td>
    @php

    $totalSemesters = $plan->studyTerm->semesters;
    $lecturesPerSemester = count($totalPlan['hours_modules']) > 0 ?
    array_sum($totalPlan['hours_modules']) / $totalSemesters :
    $totalSemesters * $plan->form_organization_id;
    @endphp

    @foreach($totalPlan['hours_modules'] as $total)
    <td class="border-table text-bold">
        {{ $total }}
    </td>
    @endforeach
    </tr>

    <tr class="table-bold">
        <td class="border-table">&nbsp;</td>
        <td colspan="11" class="text-left border-table text-bold">Кількість годин на тиждень</td>

        @php
        $hoursModules = !empty($totalPlan['hours_modules']) ? $totalPlan['hours_modules'] : array_fill(0, $plan->studyTerm->semesters, $FORM_ORGANIZATIONS_TABLE[$plan->form_organization_id]);
        @endphp

        @foreach($hoursModules as $idx => $hour)
        <td class="border-table text-bold">{{ !empty($totalPlan['hours_modules']) ? $hour : 0 }}</td>
        @endforeach
    </tr>
    <tr class="table-bold">
        <td class="border-table">&nbsp;</td>
        <td colspan="11" class="text-left border-table text-bold">Кількість екзаменів</td>

        @foreach($count_exams as $td)
        <td class="border-table text-bold">{{ $td }}</td>
        @endforeach
    </tr>
    <tr class="table-bold">
        <td class="border-table">&nbsp;</td>
        <td colspan="11" class="text-left border-table text-bold">Кількість заліків</td>

        @foreach($count_tests as $td)
        <td class="border-table text-bold">{{ $td }}</td>
        @endforeach
    </tr>

    <tr class="table-bold">
        <td class="border-table">&nbsp;</td>
        <td colspan="11" class="text-left border-table text-bold">Кількість курсових робіт</td>
        @foreach($count_coursework as $td)
        <td class="border-table text-bold">
            {{ $td }}
        </td>
        @endforeach
    </tr>

    <tr>
        <td colspan="11" class="text-left">
            * у кожному семестрі з каталога обирається 1 навчальна дисципліна обсягом 5 кредитів ЄКТС
        </td>
        @foreach($shortColspanPlan as $td)
        <td>&nbsp;</td>
        @endforeach
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>

    @foreach($plan->signatures as $signature)
    @if($signature->position->agreed)
    <tr class="text-left signature-position">
        <td class="text-left approved" colspan="5">ПОГОДЖЕНО:</td>
    </tr>
    @endif

    <tr>
        <td colspan="7" rowspan="3" class="text-left signature-position">
            {{ $signature->position->position }} {{ $signature->manual_position }}
        </td>
        <td rowspan="3">&nbsp;</td>
        <td rowspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td class="signature">________________</td>
        <td>&nbsp;</td>
        <td colspan="7" class="text-left signature-position name"> {{ $signature->name }}
            <span class="second">{{ $signature->surname }}</span>
        </td>
    </tr>

    <tr>
        <td class="text-center">(підпис)</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>&nbsp;</tr>
    @endforeach
    --}}
    </tbody>
    </table>
</body>

</html>