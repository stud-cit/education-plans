<tr class="table-month">
    @foreach ($weeks as $key => $value)
    <td class="table-week border-table" colspan="2">
        {{$key + 1}}
    </td>
    @endforeach
</tr>