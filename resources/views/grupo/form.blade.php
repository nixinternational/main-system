@extends('layouts.app')
@section('title', isset($grupo) ? "Editar grupo $grupo->nome" : 'Cadastrar grupo')


@section('content')
    <form enctype="multipart/form-data"
        action="{{ isset($grupo) ? route('grupo.update', $grupo->id) : route('grupo.store') }}"
        method="POST">
        @csrf
        @if (isset($grupo))
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-12">
                <label for="exampleInputEmail1" class="form-label">Nome</label>
                <input value="{{ isset($grupo) ? $grupo->nome : '' }}" class="form-control" name="nome"
                    id="nome">
                @error('nome')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>

        </div>
 
        <div class="row ">
            @foreach ($permissaos as $permissao)
            <div class="col-3">
                <div class="form-check">
                    <input class="form-check-input"
                    {{in_array($permissao->id,$permissoesSelecionadas)? 'checked':''}}
                    type="checkbox" name="permissao[]" value="{{$permissao->id}}" id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                      {{$permissao->nome}}
                    </label>
                  </div>
                  
            </div>
            @endforeach
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
    </script>


@endsection
