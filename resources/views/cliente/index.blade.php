@extends('layouts.app')
@section('title', 'Clientes')

@section('actions')
    <a href="{{ route('cliente.create') }}" class="btn btn-primary">
        Cadastrar cliente
    </a>
@endsection


@section('content')
    <form class="mr-2 d-flex row justify-content-between" id="formSearch" action="{{ route('cliente.index') }}" method="GET">
        <div class="d-flex col-md-12 col-sm-12 col-6">

            <div class=" input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" class="form-control"
                    placeholder="" aria-label="" aria-describedby="basic-addon1">
                <a href="{{ route('cliente.index') }}" class="btn btn-primary">Limpar busca</a>

            </div>
        </div>
        <div class="d-flex row col-md-12 col-sm-12 col-6">
            <div class="input-group  col-2">
                <select id="paginacao" name="paginacao" class="custom-select mr-2" style="min-width: 80px"
                    id="inputGroupSelect01">
                    <option value="10" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '10' ? 'selected' : '' }}>
                        10
                    </option>
                    <option value="20" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '20' ? 'selected' : '' }}>
                        20
                    </option>
                    <option value="30" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '30' ? 'selected' : '' }}>
                        30
                    </option>


                </select>
            </div>
                {{ $clientes->appends(['paginacao' => $_GET['paginacao'] ?? 10]) }}

        </div>
    </form>
    @if (!$clientes->isEmpty())
        <div class="table-responsive">

            <table id="clienteTable" class="table  shadow rounded table-striped table-hover">
                <thead class="bg-primary ">
                    <tr>
                        <th>ID</th>
                        <th>NOME</th>
                        <th>CNPJ</th>
                        <th>LOGRADOURO</th>
                        <th>BAIRRO</th>
                        <th>CIDADE</th>
                        <th>TIPO</th>
                        <th class="d-flex justify-content-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr @if ($cliente->deleted_at != null) style="background-color:#ff8e8e" @endif>
                            <td>{{ $cliente->id }}</td>
                            <td>{{ $cliente->name }}</td>
                            <td>{{ $cliente->cnpj }}</td>
                            <td>{{ $cliente->logradouro }}</td>
                            <td>{{ $cliente->bairro }}</td>
                            <td>{{ $cliente->cidade }}</td>
                            <td>{{ $cliente->tipo_cliente }}</td>
                            <td class="d-flex  justify-content-around">
                                @if ($cliente->deleted_at == null)
                                    <a href="{{ route('cliente.edit', $cliente->id) }}" type="button"
                                        class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                                @endif
                                <form method="POST"
                                    action="{{ route($cliente->deleted_at == null ? 'cliente.destroy' : 'cliente.ativar', $cliente->id) }}"
                                    enctype="multipart/form-data">
                                    @if ($cliente->deleted_at == null)
                                        @method('DELETE')
                                    @else
                                        @method('PUT')
                                    @endif
                                    @csrf
                                    <button type="submit"
                                        class="btn {{ $cliente->deleted_at == null ? 'btn-danger' : 'btn-success' }}"><i
                                            class="fa fa-power-off"></i></button>

                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-not-found />
    @endif



@endsection

@push('scripts')
    <script>
        document.getElementById('search').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
        document.getElementById('paginacao').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
    </script>
@endpush
