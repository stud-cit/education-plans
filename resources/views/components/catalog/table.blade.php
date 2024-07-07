<table class="table">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">Назва дисципліни</th>
            <th class="text-center language" rowspan="2">Мова викладання</th>
            <th class="text-center" rowspan="2">Кафедра, що пропонує дисципліну</th>
            <th class="text-center" colspan="2">Посада, прізвище та ініціали викладача (ів), який (і) пропонується для викладання</th>
            <th class="text-center" rowspan="2">Компетентності (загальні та/або фахові, на розвиток яких спрямована дисципліна</th>
            <th class="text-center" rowspan="2">Результати навчання за навчальною дисципліною</th>
            <th class="text-center" rowspan="2">Види навчальних<br> занять та методи викладання, що пропонуються</th>
            <th class="text-center" rowspan="2">Кількість здобувачів,<br>які можуть записатися<br>на<br> дисципліну</th>
            <th class="text-center" rowspan="2">Вхідні вимоги до здобувачів, які хочуть обрати дисципліну / вимоги до матеріально-технічного забезпечення</th>
            <th class="text-center" rowspan="2">Обмеження<br>щодо<br>семестру вивчення</th>
        </tr>
        <tr>
            <th class="text-center">Лекції</th>
            <th class="text-center">Семінарські та практичні заняття, лабораторні роботи</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            @for($i = 1; $i <= 11; $i++) <td class="text-center">{{ $i }}</td>
                @endfor
        </tr>
        @if(isset($item->group_name))
        <tr>
            <td class="pdf_table-group" colspan="12">
                {{ $item['group_name'] }}
            </td>
        </tr>
        @endif
        @if(isset($item['subjects']) && count($item['subjects']) > 0)
        @foreach($item['subjects'] as $subject)

        <tr>
            <td class="text">{{ $subject['subjectName'] }}</td>
            <td class="text">{{ $subject['language'] }}</td>
            <td class="text">{{ $subject['department'] }}</td>
            <td class="text">{{ $subject['lecturersTitle'] }}</td>
            <td class="text">{{ $subject['practiceTitle'] }}</td>
            <td class="text">{{ $subject['generalCompetence'] }}</td>
            <td class="learning-outcomes-column text">{{ $subject['learningOutcomes'] }}</td>
            <td class="text">{{ $subject['typesEducationalActivities'] }}</td>
            <td class="text-center text">{{ $subject['numberAcquirers'] }}</td>
            <td class="text">{{ $subject['entryRequirementsApplicant'] }}</td>
            <td class="text">{{ $subject['limitation'] }}</td>
        </tr>

        @endforeach
        @else
        <tr>
            <td colspan="12" class="pdf_table-noresult text-center">Данні відсутні</td>
        </tr>
        @endif
    </tbody>
</table>