@extends('layouts.app')
@section('title', 'Cadastrar produção')


@section('content')

    <form enctype="multipart/form-data" action="{{ route('producao.store') }}" method="POST">
        @csrf
        @if (isset($produto))
            @method('PUT')
        @endif

        <div id="accordion">
            @foreach ($categorias as $categoria)
                <div class="card card-primary">
                    <div class="card-header" id="heading{{ $categoria->id }}">
                        <h5 class="mb-0">
                            <button class=" btn btn-block text-white text-left" style="font-size: 25px"   type="button" data-toggle="collapse"
                                data-target="#collapse{{ $categoria->id }}" aria-expanded="true"
                                aria-controls="collapse{{ $categoria->id }}">
                                <b>{{ $categoria->nome }}</b>
                            </button>
                           
                        </h5>
                    </div>

                    <div id="collapse{{ $categoria->id }}" class="collapse "
                        aria-labelledby="heading{{ $categoria->id }}" data-parent="#accordion">
                        <div class="card-body" style="background: #dedede">
                            <table class="table shadow rounded table-striped table-hover">
                                <thead class="bg-primary ">
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Data de Início</th>
                                    </tr>
                                </thead>
                                <tbody style="background: #fafafa">
                                    @foreach ($categoria->produtos as $produto)
                                        <tr>
                                            <td><input type="hidden" value="{{ $produto->id }}"
                                                    name="producao[{{ $produto->id }}][produto_id]"> {{ $produto->nome }}
                                            </td>
                                            <td><input type="number" class="form-control " data-id="{{ $loop->index }}"
                                                    value="" name="producao[{{ $produto->id }}][quantidade]"></td>
                                            <td> <input autocomplete="off" type="text" value=""
                                                    class="form-control float-right dataHora" id=""
                                                    name="producao[{{ $produto->id }}][data_inicio]">
                                            </td>
                                
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary mt-4">Salvar</button>

    </form>

    <script>
        $('.select2').select2({
            width: '100%'
        })

        $('.dataHora').datetimepicker({
            i18n: {
                de: {

                }
            },
            format: 'd/m/Y H:i',
            lang: 'pt'
        });
    </script>
@endsection
