@extends('layouts.app')
@section('title', 'Produtos')

@section('actions')
    <a href="{{ route('produto.create') }}" class="btn btn-primary">
        Cadastrar produto
    </a>
@endsection


@section('content')
    <form class="mr-2 d-flex row justify-content-between" id="formSearch" action="{{ route('produto.index') }}" method="GET">


        <div class="d-flex col-md-12 col-sm-12 col-6">

            <div class=" input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" class="form-control"
                    placeholder="" aria-label="" aria-describedby="basic-addon1">
                <a href="{{ route('produto.index') }}" class="btn btn-primary col-12">Limpar busca</a>

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
                {{ $produtos->appends(['paginacao' => $_GET['paginacao'] ?? 10]) }}

        </div>
    </form>
    <div class="table-responsive">

        @if (!$produtos->isEmpty())
            <table id="produtosTable" class="table shadow rounded table-striped table-hover">
                <thead class="bg-primary ">
                    <tr>
                        <th>Id</th>
                        <th>Nome</th>

                        <th>Unidade</th>
                        <th>Preço A (R$)</th>
                        <th>Preço B (R$)</th>
                        <th>Preço C (R$)</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach ($produtos as $produto)
                        <tr @if ($produto->deleted_at != null) style="background-color:#ff8e8e" @endif>

                            <td>{{ $produto->id }}
                            </td>

                            <td>{{ $produto->nome }}</td>

                            <td style="text-overflow: ellipsis">{{ $produto->unidade }}</td>
                            <td>{{ $produto->precos['a'] }}</td>
                            <td>{{ $produto->precos['b'] }}</td>
                            <td>{{ $produto->precos['c'] }}</td>


                            <td class="d-flex  justify-content-around">

                                @if ($produto->deleted_at == null)
                                    <a href="{{ route('produto.edit', $produto->id) }}" type="button"
                                        class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                                @endif
                                <form method="POST"
                                    action="{{ route($produto->deleted_at == null ? 'produto.destroy' : 'produto.ativar', $produto->id) }}"
                                    enctype="multipart/form-data">
                                    @if ($produto->deleted_at == null)
                                        @method('DELETE')
                                    @else
                                        @method('PUT')
                                    @endif
                                    @csrf
                                    <button type="submit"
                                        class="btn {{ $produto->deleted_at == null ? 'btn-danger' : 'btn-success' }}"><i
                                            class="fa fa-power-off"></i></button>

                                </form>



                            </td>
                        </tr>
                    @endforeach
                <tfoot>


                </tfoot>
                </tbody>
            </table>
        @else
            <x-not-found />
        @endif
    </div>
    @include('produto.modalInfo')

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
