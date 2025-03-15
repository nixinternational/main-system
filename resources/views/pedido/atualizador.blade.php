@extends('layouts.app')
@section('title', isset($produto) ? : 'Baixa de pedidos')


@section('content')
  <div class="">

    <form class="mr-2 d-flex flex-column" id="formSearch" action="{{ route('pedido.atualiza') }}" method="GET">
      <div class="row">
        <div class="col-3">

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            </div>
            <input value="{{ $_GET['motorista'] ?? '' }}" type="text" id="motorista" name="motorista"
              class="form-control" placeholder=" Motorista" aria-label="" aria-describedby="basic-addon1">
          </div>
        </div>
        <div class="col-3">

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            </div>
            <input value="{{ $_GET['cliente'] ?? '' }}" type="text" id="cliente" name="cliente" class="form-control"
              placeholder="Cliente" aria-label="" aria-describedby="basic-addon1">
          </div>
        </div>
        <div class="col-2">
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="far fa-calendar-alt"></i>
                </span>
              </div>
              <input value="{{ $_GET['dataHora'] ?? '' }}" placeholder="Data de entrega" type="text"
                class="form-control float-right" name="dataHora" id="dataHora">
            </div>

          </div>
        </div>
        <div class="col-2">
          <div class="form-group">
            <div class="input-group">
              <select class="custom-select" id="status" name="status">
                <option value="-1">Selecione uma opção</option>

                <option {{ isset($_GET['status']) && $_GET['status'] == 'AGENDADO' ? 'selected' : '' }} value="AGENDADO">
                  Agendado</option>
                <option {{ isset($_GET['status']) && $_GET['status'] == 'ENTREGUE' ? 'selected' : '' }} value="ENTREGUE">
                  Entregue</option>
                <option {{ isset($_GET['status']) && $_GET['status'] == 'CANCELADO' ? 'selected' : '' }}
                  value="CANCELADO">Cancelado</option>

              </select>
            </div>

          </div>
        </div>
        <div class="col-md-2 col-sm-12">
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fa fa-search"></i>
                </span>
              </div>
              <input value="{{ $_GET['codigo'] ?? '' }}" placeholder="ID" type="text" class="form-control float-right"
                name="codigo" id="codigo">
            </div>

          </div>
        </div>
        <div class="col-1">
          <a href="{{ route('pedido.index') }}" class="btn btn-primary">Limpar busca</a>
        </div>

        <div class="col-2">

          <select id="paginacao" name="paginacao" class="custom-select mr-2" style="min-width: 80px"
            id="inputGroupSelect01">

            <option value="50" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '50' ? 'selected' : '' }}>
              50
            </option>
            <option value="70" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '70' ? 'selected' : '' }}>
              70
            </option>
            <option value="100" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '100' ? 'selected' : '' }}>
              100
            </option>


          </select>
        </div>
        {{ $pedidos->appends([
            'paginacao' => $_GET['paginacao'] ?? 50,
            'motorista' => $_GET['motorista'] ?? '',
            'cliente' => $_GET['cliente'] ?? '',
            'dataHora' => $_GET['dataHora'] ?? '',
            'status' => $_GET['status'] ?? '',
        ]) }}
      </div>
      <div class="d-flex flex-row input-group row justify-content-end">
        <div class="  ">

          <div class="col-10">

          </div>

        </div>
      </div>
    </form>
    @if (!$pedidos->isEmpty())

      <form method="POST" action="{{ route('pedido.atualizar') }}">
        @csrf
        <table id="pedidoTable" class="table shadow rounded table-striped table-hover">


          <thead class="bg-primary ">
            <tr>
              <th><input type="checkbox" id="selectAll"></th>
              <th>Id</th>
              <th>Cliente</th>
              <th>Status</th>
              <th>Motorista</th>
              <th>Data de entrega</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pedidos as $pedido)
              <tr @if ($pedido->deleted_at != null) style="background-color:#ff8e8e" @endif>
                <td><input type="checkbox" class="pedidoCheck" name="pedido[]" value="{{ $pedido->id }}">
                </td>
                <td>{{ $pedido->id }}</td>
                <td>{{ $pedido->cliente->name }}</td>
                <td>{{ $pedido->status }}</td>
                <td>{{ $pedido->motorista->nome }}</td>
                <td>{{ \Carbon\Carbon::parse($pedido->dt_previsao)->format('d/m/Y H:i') }}</td>

                <td>
                  @if ($pedido->deleted_at == null)
                    <a href="{{ route('pedido.edit', $pedido->id) }}" type="button"
                      class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>

        </table>
        <div class="d-flex justify-content-between align-items-center">
          <div class="col-3">
            <div class="form-group">
              <label>Status</label>
              <div class="input-group">
                <select class="custom-select" name="status">
                  <option hidden>Selecione uma opção</option>
                  <option {{ isset($pedido) && $pedido->status == 'AGENDADO' ? 'selected' : '' }} value="AGENDADO">
                    Agendado</option>
                  <option {{ isset($pedido) && $pedido->status == 'ENTREGUE' ? 'selected' : '' }} value="ENTREGUE">
                    Entregue</option>
                  <option {{ isset($pedido) && $pedido->status == 'CANCELADO' ? 'selected' : '' }} value="CANCELADO">
                    Cancelado</option>

                </select>
              </div>
              @error('status')
                <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
              @enderror
            </div>
          </div>
          <div class="col-1">
            <button class="btn btn-primary" type="submit">Atualizar Pedido</button>
          </div>
        </div>
      </form>
    @else
      <x-not-found />

    @endif
  </div>
  <script>
    $('#selectAll').on('change', function() {
      let val = $(this).is(':checked');
      $('.pedidoCheck').each((index, element) => {
        $(element).prop('checked', val)
      })
    })
    $('#dataHora').datetimepicker({
      i18n: {
        de: {
          months: [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
          ],
          dayOfWeek: [
            'Dom',
            'Seg',
            'Ter',
            'Qua',
            'Qui',
            'Sex',
            'Sáb'
          ]
        }
      },
      onSelect: function(dateText) {
        console.log(dateText)
        document.getElementById('formSearch').submit()

      },
      format: 'd/m/Y',
      lang: 'pt'
    });
    document.getElementById('codigo').addEventListener('change', function() {
      document.getElementById('formSearch').submit()
    })
    document.getElementById('cliente').addEventListener('change', function() {
      document.getElementById('formSearch').submit()
    })
    document.getElementById('motorista').addEventListener('change', function() {
      document.getElementById('formSearch').submit()
    })
    $("#dataHora").on("change.datetimepicker", ({
      date,
      oldDate
    }) => {
      document.getElementById('formSearch').submit()
      return ''
    })
    document.getElementById('dataHora').addEventListener('change', function() {})
    document.getElementById('status').addEventListener('change', function() {
      document.getElementById('formSearch').submit()
    })
    document.getElementById('paginacao').addEventListener('change', function() {
      document.getElementById('formSearch').submit()
    })
  </script>
@endsection
