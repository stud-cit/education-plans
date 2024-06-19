{{--
            <tr class="table-title">
                <td colspan="{{$fullColspanTitle}}" align="center">СУМСЬКИЙ ДЕРЖАВНИЙ УНІВЕРСИТЕТ</td>
</tr>
<tr class="table-faculty">
    <td colspan="{{$fullColspanTitle}}">
        {{ $plan->facultyName }}
    </td>
</tr>
<tr class="table-text">
    <td colspan="25">Затверджено рішенням вченої ради.</td>
</tr>
<tr class="table-text">
    <td colspan="25">Протокол від ____._____________._____р.№____</td>
</tr>
<tr class="table-text">
    <td colspan="25">Голова ради ________________ Анатолій ВАСИЛЬЄВ</td>
</tr>
<tr class="table-text">
    <td colspan="7" style="text-align: center">(підпис)</td>
</tr>
<tr class="table-text">
    <td colspan=25">______ ________________________ ________ р.</td>
</tr>
<tr class="table-text">
    <td style="text-align: center" colspan="7">М.П.</td>
</tr>
<tr></tr>

<tr>
    <td colspan="100%" align="center" class="table-title">НАВЧАЛЬНИЙ ПЛАН</td>
</tr>
<tr></tr>

@foreach ($professions as $index => $td)
<tr>
    @foreach ($td as $i => $item)
    <td @if (isset($item['colspan'])) colspan="{{ $item['colspan'] }}" @elseif (isset($item['acolspan'])) colspan="{{ ($fullColspanTitle - $item['acolspan'] * 2) / 2 }}" @endif @class([ 'table-profession-title'=> isset($item['title']), 'table-profession-text' => isset($item['key']) ])>

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
--}