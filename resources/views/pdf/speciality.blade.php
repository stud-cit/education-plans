<!DOCTYPE html>
<html lang="uk">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>КАТАЛОГ ВИБІРКОВИХ НАВЧАЛЬНИХ ДИСЦИПЛІН ЦИКЛУ ПРОФЕСІЙНОЇ ПІДГОТОВКИ ЗА СПЕЦІАЛЬНІСТЮ</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
        }

        .table {
            width: 100%;
            max-width: 100%;
            font-size: 8pt;
            word-wrap: break-word;
            table-layout: fixed;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .table th,
        .table td {
            padding: 2px 5px;
            border: 1px solid rgba(0, 0, 0, 0.12);
        }

        .table thead {
            border-bottom: 1px solid rgba(0, 0, 0, 0.12);
        }

        .table td {
            vertical-align: top;
        }

        .pdf_title,
        .pdf_subtitle,
        .pdf_faculty {
            text-align: center;
            font-size: 8pt;
            color: rgba(0, 0, 0, 0.8);
        }

        .pdf_faculty-line {
            text-align: center;
            font-size: 8pt;
            padding: 0 50px;
            border-top: 1px solid;
            margin: 8px auto;
            width: fit-content;
            width: -moz-fit-content;
            color: rgba(0, 0, 0, 0.8);
        }

        .pdf_table-noresult {
            text-align: center;
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: 300;
            line-height: 3;
        }

        .pdf_subscribe {
            font-size: 8pt;
        }

        .text-center {
            text-align: center;
        }

        .text {
            hyphens: auto;
            word-wrap: break-word;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }

        tbody {
            display: table-row-group;
        }

        th {
            width: 8%;
        }

        .limit {
            width: 6%;
        }

        .language {
            width: 7%;
        }
    </style>
</head>

<body>
    @php
    $length = count($data) -1;
    @endphp

    @if(count($data) > 0)
    @foreach($data as $index => $item)
    <div class="pdf">
        <p class="pdf_title">
            СУМСЬКИЙ ДЕРЖАВНИЙ УНІВЕРСИТЕТ
        </p>
        @if(isset($item['faculty']))
        <div class="pdf_faculty">
            {{ $item['faculty'] }}
        </div>
        @else
        <div class="pdf_faculty-line">
            (назва навчально-наукового інституту/факультету)
        </div>
        @endif

        @if(isset($item['department']))
        <div class="pdf_faculty">
            {{ $item['department'] }}
        </div>
        @else
        <div class="pdf_faculty-line">
            (назва кафедри)
        </div>
        @endif

        @if(isset($item['speciality']))
        <p class="pdf_subtitle">
            КАТАЛОГ ВИБІРКОВИХ НАВЧАЛЬНИХ ДИСЦИПЛІН ЦИКЛУ ПРОФЕСІЙНОЇ ПІДГОТОВКИ ЗА СПЕЦІАЛЬНІСТЮ <br>
            {{ $item['speciality'] }} <br>
            {{ $item['educationLevel'] }} {{ $item['year'] }} &mdash; {{ $item['year'] + 1 }} н. р.
        </p>
        @endif
        <div class="pdf">
            <x-catalog.table :item="$item" />
        </div>
        <p class="pdf_subscribe">
            За всіма вказаними навчальними дисциплінами розроблені повні комплекси навчально-методичного забезпечення.
        </p>

    </div>

    @if($length !== $index)
    <div style="page-break-before:always">&nbsp;</div>
    @endif
    @endforeach
    @else
    <div class="text-center">
        <h2>Каталогів не знайдено</h2>
    </div>
    @endif

</body>
