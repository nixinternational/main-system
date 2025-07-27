@extends('layouts.app')
@section('title', 'Fornecedores')

@section('actions')
    <a href="{{ route('fornecedor.create') }}" class="btn btn-primary">
        Cadastrar Fornecedor
    </a>
@endsection


@section('content')

    <form class="mr-2 d-flex justify-content-between" id="formSearch" action="{{ route('fornecedor.index') }}" method="GET">
        <div class="d-flex ">

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" class="form-control"
                    placeholder="" aria-label="" aria-describedby="basic-addon1">
                <a href="{{ route('fornecedor.index') }}" class="btn btn-primary">Limpar busca</a>

            </div>
        </div>
        <div class="d-flex">
            <div class="input-group  ">
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
                {{ $fornecedores->appends(['paginacao' => $_GET['paginacao'] ?? 10]) }}

            </div>
        </div>
    </form>
    @if (!$fornecedores->isEmpty())
        <table id="fornecedorTable" class="table shadow rounded table-striped table-hover">
            <thead class="bg-primary ">
                <tr>
                    <th>Id</th>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Ativo</th>
                    <th class="d-flex justify-content-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fornecedores as $fornecedor)
                    <tr @if ($fornecedor->deleted_at != null) style="background-color:#ff8e8e" @endif>
                        <td>{{ $fornecedor->id }}</td>
                        <td>{{ $fornecedor->nome }}</td>
                        <td>{{ formatCnpjCpf($fornecedor->cnpj) }}</td>
                        <td>{{ $fornecedor->deleted_at == null ? 'Ativo' : 'Inativo' }}</td>
                        <td class="d-flex  justify-content-around">
                            @if ($fornecedor->deleted_at == null)
                                <a href="{{ route('fornecedor.edit', $fornecedor->id) }}" type="button"
                                    class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                            @endif
                            <form method="POST"
                                action="{{ route($fornecedor->deleted_at == null ? 'fornecedor.destroy' : 'fornecedor.ativar', $fornecedor->id) }}"
                                enctype="multipart/form-data">
                                @if ($fornecedor->deleted_at == null)
                                    @method('DELETE')
                                @else
                                    @method('PUT')
                                @endif
                                @csrf
                                <button type="submit"
                                    class="btn {{ $fornecedor->deleted_at == null ? 'btn-danger' : 'btn-success' }}"><i
                                        class="fa fa-power-off"></i></button>

                            </form>
                         


                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <x-not-found />
    @endif

    <!-- Modal -->

    @include('fornecedor.modalFoto')


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
