<tr class="table-month border-table">
    <td rowspan="2" colspan="2">Курс</td>
    @foreach ($month as $items)
    <td colspan="{{ $items['countWeek'] * 2 }}" class="border-table">
        {{ $items['monthTitle'] }}
    </td>
    @endforeach
</tr>