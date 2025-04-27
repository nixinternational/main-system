@extends('layouts.app')
@section('title', isset($tipoDocumento) ? "Editar tipo de documento: $tipoDocumento->nome" : 'Cadastrar tipo de documento')


@section('content')
    <form enctype="multipart/form-data"
        action="{{ isset($tipoDocumento) ? route('tipo-documento.update', $tipoDocumento->id) : route('tipo-documento.store') }}"
        method="POST">
        @csrf
        @if (isset($tipoDocumento))
            @method('PUT')
        @endif
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label for="exampleInputEmail1" class="form-label">Nome</label>
                        <input value="{{ isset($tipoDocumento) ? $tipoDocumento->nome : '' }}" class="form-control" name="nome"
                            id="nome">
                        @error('nome')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
        
                </div>
                <div class="row">
                    <div class="col-12 mt-1">
                        <div class="form-floating">
                            <label for="floatingTextarea2">Descrição</label>
                            <textarea name="descricao" class="form-control" id="descricaotipoDocumento" style="height: 100px;resize:none">{{ isset($tipoDocumento) ? $tipoDocumento->descricao : '' }}</textarea>
                            @error('descricao')
                                <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                            @enderror
                        </div>
                    </div>
                </div>
        
        
                <button type="submit" class="btn btn-primary mt-3">Salvar</button>
            </div>
        </div>
    </div>

    </form>

    <script>
        // document.getElementById('imagetipoDocumento').addEventListener('change', function(event) {
        //     let output = document.getElementById('previewImage');
        //     output.src = URL.createObjectURL(event.target.files[0]);
        //     output.onload = function() {
        //         URL.revokeObjectURL(output.src) // free memory
        //     }
        // })
    </script>


@endsection
