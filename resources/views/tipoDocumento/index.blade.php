@extends('layouts.app')
@section('title', 'Tipos de Documento')

@section('actions')
    <a href="{{ route('tipo-documento.create') }}" class="btn btn-primary">
        Cadastrar tipo de documento
    </a>
@endsection


@section('content')
    <form class="mr-2 d-flex justify-content-between" id="formSearch" action="{{ route('tipo-documento.index') }}" method="GET">
        <div class="d-flex ">

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" class="form-control"
                    placeholder="" aria-label="" aria-describedby="basic-addon1">
                <a href="{{ route('tipo-documento.index') }}" class="btn btn-primary">Limpar busca</a>

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
                {{ $tipoDocumentos->appends(['paginacao' => $_GET['paginacao'] ?? 10]) }}

            </div>
        </div>
    </form>
    @if (!$tipoDocumentos->isEmpty())
        <table id="tipoDocumentoTable" class="table shadow rounded table-striped table-hover">
            <thead class="bg-primary ">
                <tr>
                    <th>Id</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th class="d-flex justify-content-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tipoDocumentos as $tipoDocumento)
                    <tr @if ($tipoDocumento->deleted_at != null) style="background-color:#ff8e8e" @endif>
                        <td>{{ $tipoDocumento->id }}</td>
                        <td>{{ $tipoDocumento->nome }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($tipoDocumento->descricao, 100) }}</td>
                        <td class="d-flex  justify-content-around">
                            @if ($tipoDocumento->deleted_at == null)
                                <a href="{{ route('tipo-documento.edit', $tipoDocumento->id) }}" type="button"
                                    class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                            @endif
                            <form method="POST"
                                action="{{ route($tipoDocumento->deleted_at == null ? 'tipo-documento.destroy' : 'tipo-documento.ativar', $tipoDocumento->id) }}"
                                enctype="multipart/form-data">
                                @if ($tipoDocumento->deleted_at == null)
                                    @method('DELETE')
                                @else
                                    @method('PUT')
                                @endif
                                @csrf
                                <button type="submit"
                                    class="btn {{ $tipoDocumento->deleted_at == null ? 'btn-danger' : 'btn-success' }}"><i
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
