@extends('layouts.app')
@section('title', isset($cliente) ? 'Processos - ' . $cliente->nome : 'Cadastrar Processo')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 text-white">
                    <i class="fas fa-file-alt me-2"></i>Processos - {{ $cliente->nome ?? 'Cliente' }}
                </h3>
                <a href="{{ route('processo.criar', ['cliente_id' => $cliente->id]) }}"
                    class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i>Criar Processo
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (!$processos->isEmpty())
                <div class="table-responsive">
                    <table id="clienteTable" class="table table-striped table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
                            <tr>
                                <th>{!! sortable('codigo_interno', 'Processo', 'processo-cliente') !!}</th>
                                <th>{!! sortable('descricao', 'Descrição', 'processo-cliente') !!}</th>
                                <th>{!! sortable('canal', 'Canal', 'processo-cliente') !!}</th>
                                <th>{!! sortable('status', 'Status', 'processo-cliente') !!}</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($processos as $processo)
                                <tr>
                                    <td>{{ $processo->codigo_interno ?? '-' }}</td>
                                    <td>{{ $processo->descricao ?? 'Sem Descrição' }}</td>
                                    <td>
                                        @php
                                            $cores = [
                                                'vermelho' => 'bg-danger',
                                                'verde' => 'bg-success',
                                                'amarelo' => 'bg-warning',
                                            ];
                                            $corClasse = $cores[$processo->canal] ?? 'bg-secondary';
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
                                            $statusTexto = $statusMap[$processo->status] ?? ucfirst(str_replace('_', ' ', $processo->status));
                                        @endphp
                                        {{ $statusTexto }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            <a href="{{ route('processo.edit', $processo->id) }}" 
                                                class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" class="form-delete-processo d-inline"
                                                action="{{ route('processo.destroy', $processo->id) }}"
                                                enctype="multipart/form-data">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $processos->appends([])->links() }}
                </div>
            @else
                <x-not-found />
            @endif
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.form-delete-processo').on('submit', function(e) {
                e.preventDefault();
                

                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Você realmente deseja excluir este processo? Esta ação não pode ser desfeita!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            })


        });
    </script>
@endpush
