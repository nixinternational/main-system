@extends('layouts.app')
@section('title', 'Relatório de motoristas')


@section('content')

  <form enctype="multipart/form-data" action="{{ route('motorista.relatorio') }}" method="POST">
    @csrf

    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="">Motorista</label>
          <div class="input-group  ">
            <select class="" id="motorista" name="motorista">

            </select>
          </div>
          @error('motorista')
            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
          @enderror
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label>Dia da entrega</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="far fa-calendar-alt"></i>
              </span>
            </div>
            <input type="text" autocomplete="off" value="" class="form-control float-right" id="data"
              name="data">

          </div>
          @error('data')
            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
          @enderror
        </div>
      </div>




    </div>
    <button type="submit" class="btn btn-primary mt-4">Emitir</button>

  </form>

  <script>
    $('#data').datetimepicker({
      i18n: {
        de: {
          months: [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
          ],
          dayOfWeek: [
            'Dom',
            'Seg',
            'Ter',
            'Qua',
            'Qui',
            'Sex',
            'Sáb'
          ]
        }
      },
      format: 'd/m/Y',
      lang: 'pt'
    });
    $('#motorista').select2({
      width: "100%",
      ajax: {
        url: '/motoristaByName',
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
                text: `${item.nome} - ${item.turno} `,
                id: item.id
              }
            })

          };
        }
      }
    });
  </script>

@endsection
