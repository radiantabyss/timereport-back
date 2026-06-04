<style>
@font-face {
    font-family: 'Arial';
    font-style: normal;
    font-weight: normal;
    src: url('{{ config('path.back_url') }}/fonts/arial.ttf') format('truetype');
}

@font-face {
    font-family: 'Calibri';
    font-style: normal;
    font-weight: 400;
    src: url('{{ config('path.back_url') }}/fonts/calibri.ttf') format('truetype');
}

@font-face {
    font-family: 'Calibri';
    font-style: normal;
    font-weight: 700;
    src: url('{{ config('path.back_url') }}/fonts/calibrib.ttf') format('truetype');
}

@font-face {
    font-family: 'Calibri';
    font-style: italic;
    font-weight: normal;
    src: url('{{ config('path.back_url') }}/fonts/calibrii.ttf') format('truetype');
}

@font-face {
    font-family: 'Calibri';
    font-style: italic;
    font-weight: 700;
    src: url('{{ config('path.back_url') }}/fonts/calibriz.ttf') format('truetype');
}

html, body {
    margin: 1px;
    font-size: 18px;
    line-height: 18px;
    font-family: 'Calibri';
}

h1, h2, h3, h4, h5, h6, p {
    margin: 0;
}

table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}

.page-break {
    page-break-after: always;
}

.title {
    font-size: 20px;
    font-weight: bold;
    margin: 0 0 10px;
}
</style>

{!! $content !!}
