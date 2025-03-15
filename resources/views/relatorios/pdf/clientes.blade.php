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
  <h1 style="margin: 0 0 40px 0;text-align: center">Relatório de Clientes</h1>
  <h3 style="text-align: center">Intervalo: {{ $inicio->format('d/m/Y H:i') }} - {{ $fim->format('d/m/Y H:i') }}</h3>
  @php
    $geral = [];

  @endphp
  @foreach ($dados as $dado)
    <table>
      <caption>
        Informações do Cliente
      </caption>
      <thead>
        <tr>
          <th>Nome</th>
          <th>CNPJ</th>
          <th>Endereço</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $dado['cliente']->name }}</td>
          <td>{{ $dado['cliente']->cnpj }}</td>
          <td>{!! $dado['cliente']->endereco_completo !!}</td>
        </tr>
      </tbody>
    </table>
    <table>
      <caption>
        Pedidos
      </caption>
      <thead>
        <tr>
          <th>Pedido</th>
          <th>Horario</th>
          <th>Item - Quantidade - Valor.un</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @if ($dado['pedidos']->isEmpty())
          <tr>
            <td colspan="5">Sem Resultados</td>
          </tr>
        @else
          @foreach ($dado['pedidos'] as $pedido)
            @php
              $totalPedido = 0;
            @endphp
            <tr>
              <td>{{ $pedido->id }}</td>
              <td>{{ \Carbon\Carbon::parse($pedido->dt_previsao)->format('d/m/Y') }}</td>
              <td>
                @foreach ($pedido->produtos as $produto)
                  @php
                    $totalPedido += $produto->preco * $produto->quantidade;
                  @endphp

                  {{ $produto->nome_produto }}({{ $produto->produto->unidade }}) -
                  {{ $produto->quantidade }} - R$
                  {{ number_format($produto->preco, 2, ',', '.') }}<br>
                @endforeach

              </td>
              <td>{{ number_format($totalPedido, 2, ',', '.') }}</td>
              <td>{{ $pedido->status }}</td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
    <table style="page-break-after: always; ">
      <caption>
        Produtos
      </caption>
      <thead>
        <tr>
          <th>Item</th>
          <th>Quantidade</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        @php
          $qtdTotal = 0;
          $valorTotal = 0;
        @endphp
        @foreach ($dado['produtos_total'] as $produto)
          @php
            $qtdTotal += $produto->quantidade_total;
            $valorTotal += $produto->preco_total;
            if (!isset($geral[$produto->nome])) {
                $geral[$produto->nome] = [
                    'qtd' => 0,
                    'valor' => 0,
                ];
            }
            $geral[$produto->nome]['qtd'] += $produto->quantidade_total;
            $geral[$produto->nome]['valor'] += $produto->preco_total;
          @endphp
          <tr>
            <td>{{ $produto->nome }}</td>
            <td>{{ $produto->quantidade_total }}</td>
            <td>{{ number_format($produto->preco_total, 2, ',', '.') }}</td>
          </tr>
        @endforeach
        <tr>
          <td><b>TOTAL</b></td>
          <td>{{ $qtdTotal }}</td>
          <td>{{ number_format($valorTotal, 2, ',', '.') }}</td>
        </tr>
      </tbody>

    </table>
  @endforeach


  <table>
    <caption>
      Produtos Totais
    </caption>
    <thead>
      <tr>
        <th>Item</th>
        <th>Quantidade</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @php
        $qtdTotal = 0;
        $valorTotal = 0;
      @endphp
      @foreach ($geral as $nome => $valor)
        @php
          $qtdTotal += $valor['qtd'];
          $valorTotal += $valor['valor'];
        @endphp
        <tr>
          <td>{{ $nome }}</td>
          <td>{{ $valor['qtd'] }}</td>
          <td>{{ number_format($valor['valor'], 2, ',', '.') }}</td>
        </tr>
      @endforeach
      <tr>
        <td><b>TOTAL</b></td>
        <td>{{ $qtdTotal }}</td>
        <td>{{ number_format($valorTotal, 2, ',', '.') }}</td>
      </tr>
    </tbody>

  </table>



</body>

</html>