@extends('layouts.app')
@section('title', 'Lançamentos')

@section('actions')
    <a href="{{ route('lancamento.create') }}" class="btn btn-primary">
        Cadastrar saída
    </a>
@endsection


@section('content')
<form class="d-flex flex-row justify-content-around" id="formSearch" action="{{ route('lancamento.index') }}" method="GET">


    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1">Código do Lote</span>
        </div>
        <input value="{{ $_GET['search'] ?? '' }}" style="max-width: 150px" type="text" id="search" name="search"
            class="form-control mr-2" placeholder="" aria-label="" aria-describedby="basic-addon1">
        <a href="{{ route('lancamento.index') }}" class="btn btn-primary">Limpar busca</a>

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
        </div>
        {{$lancamentos->appends(['paginacao' => $_GET['paginacao'] ?? 10])}}
    </div>
</form>

    @if ($lancamentos->isNotEmpty())
        <table id="produtosTable" class="table shadow rounded table-striped table-hover">
            <thead class="bg-primary ">
                <tr>
                    <th>Lançamento</th>
                    <th>Tipo</th>
                    <th>Código do Lote</th>
                    <th>Nome do produto</th>
                    <th>Quantidade</th>

                    <th>Data de Operação</th>



                </tr>
            </thead>
            <tbody>
                @foreach ($lancamentos as $lancamento)
                    <tr>
                        <td>{{ str_pad($lancamento->id, 4, '0', STR_PAD_LEFT) }}</td>

                        <td>{!! \App\Enums\TipoLancamento::from($lancamento->tipo) == \App\Enums\TipoLancamento::Entrada
                            ? '<span class="bg-success p-1 rounded">Entrada</span>'
                            : '<span class="bg-danger p-1 rounded">Saída</span>' !!}</td>
                        </td>
                        <td>
                            {{ str_pad($lancamento->lote_id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            {{ $lancamento->lote->produto->nome }}
                        </td>
                        <td>
                            {{ $lancamento->quantidade }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($lancamento->created_at)->format('d/m/Y H:i') }}
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
