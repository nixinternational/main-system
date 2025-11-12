@extends('layouts.app')
@section('actions')
@section('title', 'Usuários')

    <a href="{{ route('user.create') }}" class="btn btn-primary">
        Cadastrar usuario
    </a>
@endsection
@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-users me-2"></i>Listagem de Usuários
            </h3>
        </div>
        <div class="card-body">
            @if (!$users->isEmpty())
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead style="background: var(--theme-gradient-primary);">
                            <tr>
                                <th>{!! sortable('name', 'Nome') !!}</th>
                                <th>{!! sortable('email', 'Email') !!}</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            <a href="{{ route('user.edit', $user->id) }}" 
                                                class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <x-not-found />
            @endif
        </div>
    </div>
@endsection