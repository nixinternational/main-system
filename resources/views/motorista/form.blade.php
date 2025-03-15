@extends('layouts.app')
@section('title', isset($motorista) ? "Editar motorista: $motorista->nome" : 'Cadastrar motorista')


@section('content')
    <form enctype="multipart/form-data"
        action="{{ isset($motorista) ? route('motorista.update', $motorista->id) : route('motorista.store') }}"
        method="POST">
        @csrf
        @if (isset($motorista))
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Nome</label>
                    <input value="{{ isset($motorista) ? $motorista->nome : old('nome') ?? '' }}" class="form-control"
                        name="nome" id="nome">
                    @error('nome')
                        <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Categoria</label>
                    <div class="input-group  ">
                        <select name="turno" value="{{ isset($motorista) ? $motorista->turno : old('turno') ?? '' }}"
                            class="custom-select" id="inputGroupSelect01">
                            <option value="" hidden>Selecione uma opção</option>
                            <option {{ isset($motorista) && $motorista->turno == 'MANHÃ' ? 'selected' : '' }}
                                value="MANHÃ">Manhã
                            </option>
                            <option {{ isset($motorista) && $motorista->turno == 'TARDE' ? 'selected' : '' }}
                                value="TARDE">Tarde
                            </option>
   
                        </select>
                    </div>
                    @error('turno')
                        <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-primary">Salvar</button>


    </form>


    <script>
        $(document).ready(function($) {
            $('input[name=cnpj]').mask('99.999.999/9999-99')
        })
    </script>
@endsection

@push('scripts')
@endpush
