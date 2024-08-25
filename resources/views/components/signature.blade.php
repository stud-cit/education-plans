<div>
    <tr><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td></tr>
    @foreach($plan->signatures as $signature)
        @if($signature->position->agreed)
            <tr class="text-left signature-position">
                <td colspan="5">ПОГОДЖЕНО:</td>
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
            <td class="signature no-letter-spacing">________________</td>
            <td>&nbsp;</td>
            <td colspan="7" class="signature-position name text-left">{{ $signature->name }} {{ $signature->surname }}</td>
        </tr>
        <tr>
            <td class="text-center">(підпис)</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    @endforeach
</div>