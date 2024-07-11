@foreach ($cycles as $index => $cycle)
@if (array_key_exists('cycle_id', $cycle) && is_null($cycle['cycle_id']))
<tr class="table-subtitle">
    <td class="border-table" colspan="100%">
        {{ $cycle['title'] }}
    </td>
</tr>
@endif
@if (isset($cycle['asu_id']) || isset($cycle['selective_discipline_id']))
<tr>
    <td class="border-table">{{ $cycle['index'] }}</td>
    <td class="border-table">
        {{ $cycle['asu_id'] ? $cycle['title'] : $cycle['selective_discipline']['title'] }}
        @if (!is_null($cycle['note']))
        <sup>
            {{ array_search($cycle['id'], array_column($subjectNotes, 'id')) + 1 }}
        </sup>
        @endif
    </td>

    <td class="border-table">{{ $cycle['exams_count'] }}</td>
    <td class="border-table">{{ $cycle['test_count'] }} </td>
    <!--Заліки-->
    <td class="border-table">{{ $cycle['individual_tasks']}}</td>
    <td class="border-table">{{ $cycle['credits'] > 0 ? $cycle['credits'] : '' }}</td>
    <td class="border-table">{{ $cycle['total_volume_hour'] > 0 ? $cycle['total_volume_hour'] : '' }}</td>
    <td class="border-table">{{ $cycle['total_classroom'] > 0 ? $cycle['total_classroom'] : '' }}</td>
    <td class="border-table">{{ $cycle['hours'] > 0 ? $cycle['hours'] : '' }}</td>
    <td class="border-table">{{ $cycle['practices'] > 0 ? $cycle['practices'] : '' }}</td>
    <td class="border-table">{{ $cycle['laboratories'] > 0 ? $cycle['laboratories'] : '' }}</td>
    <td class="border-table">{{ $cycle['individual_work'] > 0 ? $cycle['individual_work'] : '' }}</td>

    @foreach ($cycle['hours_modules'] ?? range(1, $plan['study_term']['semesters'] * $FORM_ORGANIZATIONS_TABLE[$plan['form_organization_id']]) as $hour)
    <td class="border-table">
        @if (isset($hour['hour']))
        {{ $hour['hour'] > 0 ? $hour['hour'] : '' }}
        @endif
    </td>
    @endforeach

</tr>

@if (isset($cycle['asu_id']) || isset($cycle['selective_discipline_id']) && count($cycle['subjects']) > 0)
@foreach ($cycle['subjects'] as $subjectIndex => $subject)
<tr>
    <td class="border-table">{{ $cycle['index'] }}.{{ $subjectIndex + 1 }}</td>
    <td class="border-table">
        {{ $subject['asu_id'] ? $subject['title'] : $subject['selective_discipline']['title'] }}
        @if ($subject['note'])
        <sup>
            {{ array_search($subject['id'], array_column($plan['subject_notes'], 'id')) + 1 }}
        </sup>
        @endif
    </td>
    <td class="border-table">{{ $subject['exams_count'] }}</td>
    <td class="border-table">{{ $subject['test_count'] }}</td>
    <td class="border-table">{{ $cycle['individual_tasks'] }}</td>
    <td class="border-table">{{ $subject['credits'] > 0 ? $subject['credits'] : '' }}</td>
    <td class="border-table">{{ $subject['total_volume_hour'] > 0 ? $subject['total_volume_hour'] : '' }}</td>
    <td class="border-table">{{ $subject['total_classroom'] > 0 ? $subject['total_classroom'] : '' }}</td>
    <td class="border-table">{{ $subject['hours'] > 0 ? $subject['hours'] : '' }}</td>
    <td class="border-table">{{ $subject['practices'] > 0 ? $subject['practices'] : '' }}</td>
    <td class="border-table">{{ $subject['laboratories'] > 0 ? $subject['laboratories'] : '' }}</td>
    <td class="border-table">{{ $subject['individual_work'] > 0 ? $subject['individual_work'] : '' }}</td>

    @foreach ($subject['hours_modules'] ?? range(1, $plan['study_term']['semesters'] * FORM_ORGANIZATIONS_TABLE[$plan['form_organization_id']]) as $hour)
    <td class="border-table">
        @if (isset($hour['hour']))
        {{ $hour['hour'] > 0 ? $hour['hour'] : '' }}
        @endif
    </td>
    @endforeach

</tr>
@endforeach
@endif
@endif

@if (array_key_exists('total', $cycle))
<tr class="table-bold">
    @if (array_key_exists('index', $cycle))
    <td class="border-table">{{ $cycle['index'] }}</td>
    @else
    <td class="border-table">&nbsp;</td>
    @endif
    <td class="border-table text-bold">{{ $cycle['title'] }}</td>
    <td class="border-table text-bold">&nbsp;</td>
    <td class="border-table text-bold">&nbsp;</td>
    <td class="border-table text-bold">&nbsp;</td>
    <td class="border-table text-bold">{{ $cycle['credits'] }}</td>
    <td class="border-table text-bold">{{ $cycle['total_volume_hour'] }}</td>
    <td class="border-table text-bold">{{ $cycle['total_classroom'] }}</td>
    <td class="border-table text-bold">{{ $cycle['hours'] }}</td>
    <td class="border-table text-bold">{{ $cycle['practices'] }}</td>
    <td class="border-table text-bold">{{ $cycle['laboratories'] }}</td>
    <td class="border-table text-bold">{{ $cycle['individual_work'] }}</td>

    @php

    $arr = $cycle['hours_modules'] ??
    range(1, $plan['study_term']['semesters'] * $FORM_ORGANIZATIONS_TABLE[$plan['form_organization_id']])
    @endphp
    @foreach ($arr as $hour)
    <td class="border-table text-bold">{{ $cycle['hours_modules'] ? $hour : 0 }}</td>
    @endforeach
</tr>
@endif
@if (isset($cycle['cycle_id']) && !isset($cycle['asu_id']) && !array_key_exists('total', $cycle) && $cycle['title'] != 'Ідентифікатор не знайдено.')
<tr>
    <td colspan=" 100%" class="table-bold border-table">
        {{ $cycle['title'] }}
    </td>
</tr>
@endif

@endforeach