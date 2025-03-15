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
    <h1 style="margin: 0 0 40px 0;text-align: center">Relatório de Produção</h1>
    <h3 style="text-align: center">Intervalo: {{ $inicio->format('d/m/Y H:i') }} - {{ $fim->format('d/m/Y H:i') }}</h3>

    @foreach ($producao as $categoria => $produtos)
        <h3 style=" margin:20px 0 0 0 ;text-align:center">{{ $categoria }}</h3>
        <table >
          

            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>TURNO</th>

                </tr>
            </thead>
            <tbody>
                @if (count($producao) == 0)
                    <tr>
                        <td colspan="7">Sem Resultados</td>
                    </tr>
                @else
                    @php
                        $totalItens = [];
                    @endphp
                    @foreach ($produtos as $produto)
                        <tr>
                            <td>{{ $produto->nome }}</td>
                            <td>
                                {{ $produto->quantidade }}
                                @php
                                    if (!isset($totalItens[$produto->nome])) {
                                        $totalItens[$produto->nome] = 0;
                                    }
                                    $totalItens[$produto->nome] += $produto->quantidade;
                                @endphp

                            </td>
                            <td>{{ $produto->turno }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    @endforeach

    <table style="margin:20px 0 0 0 ">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantidade Total</th>

            </tr>
        </thead>
        <tbody>
            @if (count($producao) == 0)
                <tr>
                    <td colspan="7">Sem Resultados</td>
                </tr>
            @else
                @foreach ($totalItens as $nome => $total)
                    <tr>
                        <td>{{ $nome }}</td>
                        <td>{{ $total }}</td>

                    </tr>
                @endforeach

            @endif
        </tbody>
    </table>
</body>

</html>
