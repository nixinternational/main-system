@extends('layouts.app')
@section('title', isset($cliente) ? 'Processos - ' . $cliente->nome : 'Cadastrar Processo')

@section('content')
    <div class="row">
        <div class="col-12 shadow-lg px-0">
            <div class="card w-100 card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3">
                            <h3 class="card-title text-dark font-weight-bold" style=""></h3>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill"
                                href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home"
                                aria-selected="false">Processos</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <a href="{{ route('processo.create') }}" class="btn btn-primary my-3">Criar Processo</a>
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
                            aria-labelledby="custom-tabs-two-home-tab">
                            <div class="table-responsive">
                                {{$processos->appends([])}}
                                <table id="clienteTable" class="table  shadow rounded table-striped table-hover">
                                    <thead class="bg-primary ">
                                        <tr>
                                            <th>Processo</th>
                                            <th>Descrição</th>
                                            <th>Canal</th>
                                            <th>Status</th>
                                            <th class="d-flex justify-content-center">AÇÕES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($processos as $processo)
                                            <td>{{ $processo->codigo_interno }}</td>
                                            <td>{{ $processo->descricao ?? 'Sem Descrição' }}</td>
                                            <td>

                                                @php
                                                    $cores = [
                                                        'vermelho' => 'bg-danger',
                                                        'verde' => 'bg-success',
                                                        'amarelo' => 'bg-warning',
                                                    ];

                                                    $corClasse = $cores[$processo->canal] ?? 'bg-gray-400';
                                                @endphp

                                                <span class="d-inline-block rounded-circle {{ $corClasse }}"
                                                    style="width: 30px; height: 30px;"></span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'andamento' => 'Em Andamento',
                                                        'finalizado' => 'Finalizado',
                                                        'prestacao_contas' => 'Prestação de Contas',
                                                    ];

                                                    $statusTexto =
                                                        $statusMap[$processo->status] ??
                                                        ucfirst(str_replace('_', ' ', $processo->status));
                                                @endphp
                                                {{ $statusTexto }}
                                            </td>
                                            <td class="d-flex  justify-content-around">
                                                <a href="{{ route('processo.edit', $processo->id) }}" type="button"
                                                    class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
