@extends('layouts.app')
@section('title', isset($permissao) ? "Editar permissao $permissao->nome" : 'Cadastrar permissao')


@section('content')
    <form enctype="multipart/form-data"
        action="{{ isset($permissao) ? route('permissao.update', $permissao->id) : route('permissao.store') }}"
        method="POST">
        @csrf
        @if (isset($permissao))
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-8">
                <label for="exampleInputEmail1" class="form-label">Nome</label>
                <input value="{{ isset($permissao) ? $permissao->nome : '' }}" class="form-control" name="nome"
                    id="nome">
                @error('nome')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-4">
                <label for="exampleInputEmail1" class="form-label">Grupo</label>
        
                <select class="custom-select select2" multiple="multiple" name="grupo[]">
                  <option selected  disabled>Selecione uma opção</option>
                  @foreach ($grupos as $grupo)
                    <option {{in_array($grupo->id,$gruposSelecionados) 
                    
                    
                    ? 'selected' : ''}} value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                  @endforeach
                </select>
              </div>
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
