@extends('layouts.app')
@section('title', 'Catálogos')

@section('actions')
    <a href="{{ route('catalogo.create') }}" class="btn btn-primary">
        Cadastrar catálogo
    </a>
@endsection


@section('content')
    <form class="mr-2 d-flex row justify-content-between" id="formSearch" action="{{ route('catalogo.index') }}" method="GET">
        <div class="d-flex col-md-12 col-sm-12 col-6">

            <div class=" input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" class="form-control"
                    placeholder="" aria-label="" aria-describedby="basic-addon1">
                <a href="{{ route('catalogo.index') }}" class="btn btn-primary">Limpar busca</a>

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
        </form>

            @if (!$catalogos->isEmpty())
                <table id="produtosTable" class="table shadow rounded table-striped table-hover">
                    <thead class="bg-primary ">
                        <tr>
                            <th>Cliente</th>
                            <th>Data de Cadastro</th>
                            <th class="d-flex justify-content-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($catalogos as $catalogo)
                            <tr>

                                <td>{{ $catalogo->cliente->nome}}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($catalogo->created_at)->format('d/m/Y H:i') }}
                                </td>

                                <td class="d-flex  justify-content-around">
                                    
                                    <a href="{{route('catalogo.edit',$catalogo->id)}}" type="button"  class="btn btn-warning mr-1 editModal">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" action="{{ route('catalogo.destroy', $catalogo->id) }}" enctype="multipart/form-data"                                        >
                                        
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            @else
                <x-not-found />
            @endif
            {{ $catalogos->appends(['paginacao' => $_GET['paginacao'] ?? 10]) }}

        </div>




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
