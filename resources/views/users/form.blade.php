@extends('layouts.app')
@section('title', isset($user) ? "Editar Usuário $user->name" : 'Cadastrar Usuário')


@section('content')
    <form enctype="multipart/form-data"
        action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}"
        method="POST">
        @csrf
        @if (isset($user))
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-4">
                <label for="exampleInputEmail1" class="form-label">Nome</label>
                <input value="{{ isset($user) ? $user->name : '' }}" class="form-control" name="nome"
                    id="nome">
                @error('nome')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-4">
                <label for="exampleInputEmail1" class="form-label">Email</label>
                <input type="email" value="{{ isset($user) ? $user->email : '' }}" class="form-control" name="email"
                    id="email">
                @error('email')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-4">
                <label for="exampleInputEmail1" class="form-label">Senha</label>
                <input  type="password" class="form-control" name="senha"
                    id="senha">
                @error('senha')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-4">
                <label for="exampleInputEmail1" class="form-label">Grupo</label>
        
                <select class="custom-select select2"  name="grupo">
                  <option selected disabled>Selecione uma opção</option>
                  @foreach ($grupos as $grupo)
                    <option {{isset($user) && $user->grupo_id == $grupo->id ? 'selected': ''}} value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                  @endforeach
                </select>
              </div>
              @if(!isset($user))
              <div class="col-4">
                <label for="exampleInputEmail1" class="form-label">Motorista</label>
        
                <select class="custom-select select2" multiple="multiple"  name="motorista[]">
                  <option selected disabled>Selecione uma opção</option>
                  @foreach ($motoristas as $motorista)
                    <option value="{{ $motorista->id }}">{{ $motorista->nome }}</option>
                  @endforeach
                </select>
              </div>
              @endif
        </div>
     


        <button type="submit" class="btn btn-primary mt-3">Salvar</button>

    </form>

    <script>
        // document.getElementById('imageCategoria').addEventListener('change', function(event) {
        //     let output = document.getElementById('previewImage');
        //     output.src = URL.createObjectURL(event.target.files[0]);
        //     output.onload = function() {
        //         URL.revokeObjectURL(output.src) // free memory
        //     }
        // })

        $('.select2').select2({
            width: '100%'
        })
    </script>


@endsection
