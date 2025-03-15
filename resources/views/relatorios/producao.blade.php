@extends('layouts.app')
@section('title', 'Relatório de Produção')


@section('content')

  <form enctype="multipart/form-data" action="{{ route('producao.relatorio.processar') }}" method="POST">
    @csrf

    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="">Produto</label>
          <div class="input-group  ">
            <select class="custom-select produtos select2" id="produtos" data-id="${id}" name="produto[]">
              @foreach ($produtos as $produto)
                <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
              @endforeach
            </select>
          </div>
          @error('produto')
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
            <input autocomplete="off" type="text" value="" class="form-control float-right" id="data"
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
    if (localStorage.getItem('preSelecionado') == null) {
      localStorage.setItem('preSelecionado', JSON.stringify([]))
    }

    let preSelecionado = JSON.parse(localStorage.getItem('preSelecionado'))


    $('#produtos').select2({
      width: '100%',
      multiple: true,

    })
    $('#produtos').val([])
    $('#produtos').trigger('change');
    if (preSelecionado.length > 0) {
      $('#produtos').val(preSelecionado)
      $('#produtos').trigger('change');
    }

    $('#produtos').on('select2:select', function(e) {
      var data = e.params.data;
      console.log(data);
      let preSelecionado = JSON.parse(localStorage.getItem('preSelecionado'))
      if (!preSelecionado.includes(data.id)) {
        preSelecionado.push(data.id)
      }
      localStorage.setItem('preSelecionado', JSON.stringify(preSelecionado))

    });

    $('#produtos').on('select2:unselect', function(e) {
      var data = e.params.data;
      console.log(data);
      let preSelecionado = JSON.parse(localStorage.getItem('preSelecionado'))
      if (preSelecionado.includes(data.id)) {
        const index = preSelecionado.indexOf(data.id);
        if (index > -1) {
          preSelecionado.splice(index, 1);
        }
      }
      localStorage.setItem('preSelecionado', JSON.stringify(preSelecionado))

    });

    $('#data').daterangepicker({
      locale: {
        format: 'DD/MM/YYYY H:mm'
      },
      timePicker: true,
      timePicker24Hour: true,
    });
  </script>

@endsection