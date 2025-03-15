@extends('layouts.app')
@section('title', 'Relatório de Produtos')


@section('content')

    <form enctype="multipart/form-data" action="{{ route('produto.relatorio') }}" method="POST">
        @csrf
        <div class="row">

            <div class="col-6">
                <div class="form-group">
                    <label>Período de busca</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control float-right" name="intervalo" id="intervalo">
                    </div>

                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Produto</label>
                    <select name="produto" class="custom-select select2" id="produto">
                        <option value="" hidden>Selecione uma opção</option>
                        @foreach ($produtos as $produto)
                            <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                        @endforeach

                    </select>

                </div>
            </div>

        </div>
        <button type="submit" class="btn btn-primary mt-4">Emitir</button>
    </form>


    <script>
        $('.select2').select2({
            width: '100%'
        })

        $('#intervalo').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY H:mm'
            },
            timePicker: true,
            timePicker24Hour: true,
        });
        $('#cliente').select2({
            width: "100%",
            ajax: {
                url: '/clientsByName',
                dataType: "json",
                type: "GET",
                delay: 450, // wait 250 milliseconds before triggering the request

                data: function(params) {

                    var queryParameters = {
                        nome: params.term
                    }
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.data, function(item) {
                            return {
                                text: item.name,
                                id: `${item.id}`,
                            }
                        })

                    };
                }
            }
        });
    </script>

@endsection
