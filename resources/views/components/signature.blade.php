<tr><td colspan="24">&nbsp;</td></tr>
<tr><td colspan="24">&nbsp;</td></tr>
@foreach($plan->signatures as $signature)
    @if($signature->position->agreed)
        <tr class="text-left signature-position">
            <td colspan="24">ПОГОДЖЕНО:</td>
        </tr>
    @endif

    <tr class="signature-row">
        <td colspan="10" class="text-left signature-position">
            {{ $signature->position->position }} {{ $signature->manual_position }}
        </td>
        <td colspan="6" class="signature-line no-letter-spacing">
            <span>________________</span><br>
            <small>(підпис)</small>
        </td>
        <td colspan="8" class="signature-name">
            {{ $signature->name }} {{ mb_strtoupper($signature->surname) }}
        </td>
    </tr>
@endforeach