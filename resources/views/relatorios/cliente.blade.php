@extends('layouts.app')
@section('title', 'Relatório de Clientes')


@section('content')

    <form enctype="multipart/form-data" action="{{ route('cliente.relatorio') }}" method="POST">
        @csrf
        <div class="row">

            <div class="col-12">
                <div class="">
                    <label for="">Cliente</label>
                    <div class=" ">
                        <select class=""  id="cliente" name="cliente[]"  multiple="multiple"> 

                        </select>
                    </div>
                    @error('cliente')
                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
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
                    <label>Status</label>
                    <select class="custom-select" name="status" >
                        <option hidden value="-1">Selecione uma opção</option>
                        <option {{ isset($pedido) && $pedido->status == 'AGENDADO' ? 'selected' : '' }} value="AGENDADO">
                            Agendado</option>
                        <option {{ isset($pedido) && $pedido->status == 'A CAMINHO' ? 'selected' : '' }} value="A CAMINHO">A
                            Caminho</option>
                        <option {{ isset($pedido) && $pedido->status == 'ENTREGUE' ? 'selected' : '' }} value="ENTREGUE">
                            Entregue</option>
                        <option {{ isset($pedido) && $pedido->status == 'CANCELADO' ? 'selected' : '' }} value="CANCELADO">
                            Cancelado</option>

                    </select>
                    @error('status')
                        <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Emitir</button>
    </form>


    <script>
        $('#intervalo').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('#cliente').select2({
            width: "100%",
            multiple:true,
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
