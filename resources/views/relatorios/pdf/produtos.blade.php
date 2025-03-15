<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Resultados</title>
    <style>
        @page {
            size: A4 landscape;
        }

        body,
        html {
            margin: 0.5%;
            padding: 0;

        }

        td,
        th {
            border: 1px solid black;
            text-align: center
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px
        }
    </style>
</head>

<body>
    <h1 style="margin: 0 0 40px 0;text-align: center">Relatório de Produtos</h1>
    <h3 style="text-align: center">Intervalo: {{$inicio->format('d/m/Y H:i')}} - {{$fim->format('d/m/Y H:i')}}</h3>

    <table style="page-break-after: always; margin:20px 0 0 0 ">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if ($total->isEmpty())
            <tr><td colspan="2">Sem Resultados</td></tr>
            @else
                @foreach ($total as $produto)
           
                    <tr>
                        <td>{{ $produto->nome_produto }}</td>
                        <td>{{ $produto->total }}</td>
                    </tr>
                @endforeach

            @endif
        </tbody>
    </table>

   
</body>

</html>
