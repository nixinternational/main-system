@extends('layouts.app')
@section('title', 'Bancos NIX')

@section('actions')
    <a href="{{ route('banco-nix.create') }}" class="btn btn-primary">
        Cadastrar Banco
    </a>
@endsection


@section('content')
    <form class="mr-2 d-flex row justify-content-between" id="formSearch" action="{{ route('banco-nix.index') }}" method="GET">
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
                {{ $bancos->appends(['paginacao' => $_GET['paginacao'] ?? 10]) }}

        </div>
    </form>
    @if(!$bancos->isEmpty())
        <div class="table-responsive">

            <table id="clienteTable" class="table  shadow rounded table-striped table-hover">
                <thead class="bg-primary ">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Agência</th>
                        <th>Conta Corrente</th>
                        <th>N° Banco</th>
                        <th class="d-flex justify-content-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bancos as $banco)
                            <td>{{ $banco->id }}</td>
                            <td>{{ $banco->nome }}</td>
                            <td>{{ $banco->agencia }}</td>
                            <td>{{ $banco->conta_corrente }}</td>
                            <td>{{ $banco->numero_banco }}</td>
                            <td class="d-flex  justify-content-around">
                                    <a href="{{ route('banco-nix.edit', $banco->id) }}" type="button"
                                        class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                                <form method="POST"
                                    action="{{ route('banco-nix.destroy', $banco->id) }}"
                                    enctype="multipart/form-data">
                                        @method('DELETE')
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-danger"><i
                                            class="fa fa-power-off"></i>
                                    </button>

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
