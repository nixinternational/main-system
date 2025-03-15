@extends('layouts.app')
@section('title', 'Produção')

@section('actions')
    <a href="{{ route('producao.create') }}" class="btn btn-primary">
        Cadastrar produção
    </a>
@endsection


@section('content')


    <form class="mr-2 d-flex flex-column" id="formSearch" action="{{ route('pedido.index') }}" method="GET">
        <div class="row">

            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input autocomplete="off" value="{{ $_GET['dataHora'] ?? '' }}" placeholder="Data de entrega"
                            type="text" class="form-control float-right" name="dataHora" id="dataHora">
                    </div>

                </div>
            </div>


            <div class="col-md-2 col-sm-12 mb-3">
                <a href="{{ route('pedido.index') }}" class="btn btn-primary w-100">Limpar busca</a>

            </div>
        </div>
        <div class="col-md-1">
        </div>
        <div class="row">
            <div class="col-md-1 col-sm-12">

                <select id="paginacao" name="paginacao" class="custom-select mr-2" style="min-width: 80px"
                    id="inputGroupSelect01">

                    <option value="10" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '10' ? 'selected' : '' }}>
                        30
                    </option>
                    <option value="50" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '50' ? 'selected' : '' }}>
                        50
                    </option>
                    <option value="100" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '100' ? 'selected' : '' }}>
                        100
                    </option>


                </select>
            </div>
            <div class="col-md-6 col-sm-12">

                {{ $producaosPendentes->appends([
                    'paginacao' => $_GET['paginacao'] ?? 10,
                    'motorista' => $_GET['motorista'] ?? '',
                    'cliente' => $_GET['cliente'] ?? '',
                    'dataHora' => $_GET['dataHora'] ?? '',
                    'status' => $_GET['status'] ?? '',
                ]) }}
            </div>
        </div>


        <div class="d-flex flex-row input-group row justify-content-end">
            <div class="  ">

                <div class="col-10">

                </div>

            </div>
        </div>
    </form>

    <br>
    <br>
    <h3>Produções Pendentes</h3>
    <div class="table-responsive">
        <table id="pedidoTable" class="table shadow rounded table-striped table-hover">
            <thead class="bg-primary ">
                <tr>
                    <th>id</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Data de Início</th>
                </tr>
            </thead>
            <tbody>
                @if (!$producaosPendentes->isEmpty())
                    @foreach ($producaosPendentes as $producao)
                        <tr @if ($producao->deleted_at != null) style="background-color:#ff8e8e" @endif>

                            <td>{{ $producao->id }}</td>
                            <td>{{ $producao->produto->nome }}</td>
                            <td>{{ $producao->quantidade }}</td>
                            <td>{{ \Carbon\Carbon::parse($producao->dt_inicio)->format('d/m/Y H:i') }}</td>

                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="5">Nenhuma produção pendente</td>
                    </tr>
                @endif
            </tbody>

        </table>
    </div>

    <br>
    <br>
    <br>
    <h3>Produções Concluídas</h3>
    <div class="table-responsive">

        <table id="pedidoTable" class="table shadow rounded table-striped table-hover">
            <thead class="bg-primary ">
                <tr>
                    <th>#</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Data de Início</th>

                </tr>
            </thead>
            <tbody>
                @if (!$producaosConcluidas->isEmpty())

                    @foreach ($producaosConcluidas as $producao)
                        <tr @if ($producao->deleted_at != null) style="background-color:#ff8e8e" @endif>
                            <td>{{ $producao->id }}</td>
                            <td>{{ $producao->produto->nome }}</td>
                            <td>{{ $producao->quantidade }}</td>
                            <td>{{ \Carbon\Carbon::parse($producao->dt_inicio)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="4">Nenhuma produção Concluída</td>
                    </tr>
                @endif
            </tbody>

        </table>
    </div>
    </div>

@endsection

@push('scripts')
    <script>
        $('#deleteOrders').on('click', async function() {

            let pedidosDelete = [];
            $("input[type='checkbox'].pedidoCheck:checked").each((index, element) => {
                pedidosDelete.push(element.value)
            })
            fetch("pedidosDelete", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').val()
                },
                body: JSON.stringify({
                    pedidos: pedidosDelete,
                    _token: "{{ csrf_token() }}",

                })
            }).then((response) => {
                response.json().then((res) => {
                    console.log(res)
                    if (!res.data.success && res.data == '') {
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                    }

                    setTimeout(() => {
                        location.reload()
                    }, 1000);

                });

            }).catch((error) => {
                console.log(error)
            });
        })
        $('#dataHora').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
        });
        document.getElementById('motorista').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
        document.getElementById('cliente').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
        document.getElementById('codigo').addEventListener('change', function() {
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
@endpush
