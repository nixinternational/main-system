@extends('layouts.app')
@section('title', isset($pedido) ? "Editar pedido: $pedido->id" : 'Cadastrar ')


@section('content')
  <form enctype="multipart/form-data"
    action="{{ isset($pedido) ? route('pedido.update', $pedido->id) : route('pedido.store') }}" method="POST">
    @csrf
    @if (isset($pedido))
      @method('PUT')
    @endif
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="">Cliente</label>
          <div class="input-group  ">
            <select class="" id="cliente" name="cliente">
              @if (isset($pedido))
                <option selected value="{{ $pedido->cliente->id }}-{{ $pedido->cliente->tipo_cliente }}">
                  {{ $pedido->cliente->name }}</option>
              @endif
            </select>
          </div>
          @error('cliente')
            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
          @enderror
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="">Motorista</label>
          <div class="input-group  ">
            <select class="" id="motorista" name="motorista">
              @if (isset($pedido))
                <option selected value="{{ $pedido->motorista->id }}">
                  {{ $pedido->motorista->nome }}</option>
              @endif
            </select>
          </div>
          @error('motorista')
            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
          @enderror
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label>Data e Hora da entrega</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="far fa-calendar-alt"></i>
              </span>
            </div>
            <input autocomplete="off" type="text" value="{{ isset($pedido) ? $pedido->dt_previsao_formatted : '' }}"
              class="form-control float-right" id="dataHora" name="dataHora">

          </div>
          @error('dataHora')
            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
          @enderror
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label>Status</label>
          <div class="input-group">
            <select class="custom-select" {{ !isset($pedido) ? 'disabled' : '' }} name="status">
              <option hidden>Selecione uma opção</option>
              <option
                {{ isset($pedido) && $pedido->status == 'AGENDADO' ? 'selected' : (!isset($pedido) ? 'selected' : '') }}
                value="AGENDADO">Agendado
              </option>
              <option {{ isset($pedido) && $pedido->status == 'ENTREGUE' ? 'selected' : '' }} value="ENTREGUE">Entregue
              </option>
              <option {{ isset($pedido) && $pedido->status == 'CANCELADO' ? 'selected' : '' }} value="CANCELADO">
                Cancelado</option>

            </select>
          </div>
          @error('status')
            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
          @enderror
        </div>
      </div>

      <div class="w-100  card  card ">

        <div class="card-header ">
          <h3 class="card-title">Produtos</h3>
          <div class="card-tools">

          </div>

        </div>
        <div class="card-body row">
          <div class="col-1 d-flex justify-content-center align-items-start">
            <button id="addProduto" type="button" class="btn btn-success rounded shadow">Adicionar Produto
            </button>
          </div>
          <div class="col-11">
            <table class="table table-bordered" id="produtos">
              <thead class="thead-primary">
                <tr class="bg-primary">
                  <th scope="col">Produto</th>
                  <th scope="col">Quantidade</th>
                  <th scope="col">Valor Unitário</th>
                  <th scope="col">Observação</th>
                  <th scope="col">Total</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                @php
                  $total = 0;
                @endphp
                @if (isset($pedido))
                  @foreach ($pedido->produtos as $produtosEscolhidos)
                    @php
                      $total += $produtosEscolhidos->quantidade * $produtosEscolhidos->preco;
                    @endphp
                    <tr data-id="{{ $loop->index }}">
                      <td>
                        <select class="custom-select produtos" data-id="{{ $loop->index }}" name="produto[]">
                          <option hidden>Selecione uma opção</option>
                          @foreach ($produtos as $produto)
                            <option {{ $produtosEscolhidos->produto_id == $produto->id ? 'selected' : '' }}
                              value="{{ $produto->id }}">{{ $produto->nome }}</option>
                          @endforeach
                        </select>
                      </td>

                      <td><input type="number" step="0.1" class="quantidadeProduto form-control " data-id="{{ $loop->index }}"
                          value="{{ $produtosEscolhidos->quantidade }}" name="quantidade[]"></td>
                      <td><input type="number" step="0.1"
                          {{ $pedido->cliente->tipo_cliente == 'h' ? '' : 'disabled' }} class="form-control"
                          id="precoProduto-{{ $loop->index }}" value="{{ $produtosEscolhidos->preco }}"
                          data-id="{{ $loop->index }}" name="precoProduto[]"></td>
                      <td>
                        <textarea rows="1" type="text" class="observacao form-control " data-id="{{ $loop->index }}"
                          name="observacao[]">
                                                {{ $produtosEscolhidos->observacao }}
                                                </textarea>
                      </td>

                      <td><input type="number" step="0.1" disabled class="form-control"
                          id="valorCalculado-{{ $loop->index }}"
                          value="{{ $produtosEscolhidos->quantidade * $produtosEscolhidos->preco }}">
                      </td>
                      <td><button class="btn btn-danger killme" data-id="{{ $produtosEscolhidos->id }}"
                          type="button">Excluir</button></td>

                    </tr>
                  @endforeach
                @endif
              </tbody>
              <tfoot>
                <tr class=" bg-primary">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td id="totalProdutos" colspan="">{{ $total }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>
      <div class="col-sm-6 mt-3">

        <div class="form-group clearfix">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" data-ok="0" class="custom-control-input" name="repete" id="repete">
            <label class="custom-control-label" for="repete">Replicar pedido?</label>
          </div>
        </div>
      </div>
      <div class="col-12" id="repeticao" style="display: none">
        <div class="row">
          <div class="col-9 ">
            <div class="form-group">
              <label for="">Datas para cópia</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                  </span>
                </div>
                <input autocomplete="off" type="text" class="form-control float-right" name="periodo"
                  id="periodo">

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    </div>


    <input type="hidden" id="cliente_id" name="cliente_id" value="{{ isset($pedido) ? $pedido->cliente_id : '' }}">
    <button type="submit" class="btn btn-primary mt-3">Salvar</button>

  </form>

  <input type="hidden" id="produtosCatalogo" value="{{ json_encode($produtos) }}">
  <script></script>
@endsection

@push('scripts')
  <script type="module">
    $(document).ready(function() {
      $(`.produtos`).select2({
        width: '100%'
      })

    });
    let tipo_cliente = '{{ isset($pedido) ? $pedido->cliente->tipo_cliente : '' }}';
    $('#cliente').on('change', function() {
      let cliente = this.value.split('-')
      $('#cliente_id').val(cliente[0])
      tipo_cliente = cliente[1];
    })
    $(document).on('click', '.killme', function() {
      const id = this.dataset.id;
      if (id == 0) {
        this.parentNode.parentNode.remove()
      } else if (id != undefined && this.dataset.id != null && this.dataset.id != '') {
        fetch(`/removeProdutoPedido/${id}`, {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content')
          },
        }).then((response) => {
          response.json().then((res) => {
            if (res.success) {
              Toast.fire({
                icon: 'success',
                title: res.message
              });
            }
            this.parentNode.parentNode.remove()

          });

        }).catch((error) => {
          console.log(error)
          Toast.fire({
            icon: 'success',
            title: 'Erro ao excluir item!'
          });
        });
      }
    })
    $('#repete').on('change', function() {
      if ($(this).is(':checked')) {
        $('#repeticao').fadeIn('fast', function() {

        })
      } else {
        $('#repeticao').fadeOut('fast', function() {
          $('#periodo').val('')
        })
      }
    })

    $(document).on('change', '.produtos', function(e) {
      let produto = JSON.parse($('#produtosCatalogo').val()).find((element) => element.id == this.value);
      if (produto) {
        let id = this.dataset.id;
        $(`#precoProduto-${id}`).val(produto.precos[tipo_cliente])
      }
    })


    $(document).on('select2:selecting', '.produtos', function(e) {
      var data = e.params.args.data;
      let idSelected = data.id
      $(`.produtos`).each((index, element) => {
        if (idSelected == element.value) {
          Toast.fire({
            icon: 'error',
            title: 'Produto já selecionado!'
          });
          $(this).val('').trigger('change')
        }
      })
    })

    $(document).on('change', '.quantidadeProduto', function() {
      let id = this.dataset.id;
      let preco = $(`#precoProduto-${id}`).val()
      if (preco != '') {
        let precoTotal = parseInt(this.value) * parseFloat(preco)
        $(`#valorCalculado-${id}`).val(precoTotal)
        let totalRows = $('#produtos tbody tr').length;
        let total = 0;
        for (let a = 0; a <= totalRows - 1; a++) {
          let valor = $(`#valorCalculado-${a}`).val() == '' || $(`#valorCalculado-${a}`).val() ==
            undefined ? 0 : $(`#valorCalculado-${a}`).val()
          total += parseFloat(valor);
        }
        $('#totalProdutos').text(total);

      }
    })
    $(document).on('change', '.precoProduto', function() {
      let id = this.dataset.id;
      let quantidade = $(`#quantidade-${id}`).val()
      if (quantidade != '') {

        let precoTotal = parseInt(quantidade) * parseFloat(this.value)
        $(`#valorCalculado-${id}`).val(precoTotal)
        let totalRows = $('#produtos tbody tr').length;
        let total = 0;
        for (let a = 0; a <= totalRows - 1; a++) {
          let valor = $(`#valorCalculado-${a}`).val() == '' || $(`#valorCalculado-${a}`).val() ==
            undefined ? 0 : $(`#valorCalculado-${a}`).val()
          total += parseFloat(valor);
        }
        $('#totalProdutos').text(total);

      }
    })



    $('#addProduto').on('click', async function() {
      if (tipo_cliente != '' || $('#cliente_id').val() != '') {
        let precoLiberado = tipo_cliente == 'h'
        let produtos = JSON.parse($('#produtosCatalogo').val());
        let id = $('#produtos tbody tr').length;
        let selectProdutos = `<select class="custom-select produtos " id="select2-${id}" data-id="${id}" name="produto[]" >
                <option value="0" hidden>Selecione uma opção</option>`;
        for (let produto of produtos) {
          if (produtos.length == 1) {
            selectProdutos +=
              `<option selected value="${produto.id}">${produto.id} - ${produto.nome}</option>`;
          } else {
            selectProdutos += `<option value="${produto.id}">${produto.id} - ${produto.nome}</option>`;

          }
        }
        selectProdutos += "</select>"
        let tr = `<tr data-id="${id}">
                            <td>${selectProdutos}</td>
                            <td><input  type="number" step="0.1" class="quantidadeProduto form-control " data-id="${id}" id="quantidade-${id}" name="quantidade[]"></td>
                            <td><input  type="number" step="0.1" ${precoLiberado ? '':'disabled'} class="form-control precoProduto" id="precoProduto-${id}" data-id="${id}" name="precoProduto[]"></td>
                            <td><textarea  rows="1"  type="text" class="observacao form-control " data-id="${id}" name="observacao[]"></textarea></td>
                            <td><input  type="number" step="0.1" disabled class="form-control" id="valorCalculado-${id}" value="0"></td>
                            <td><button class="btn btn-danger killme" data-id="0" type="button" >Excluir</button></td>
                    </tr>
                    `
        $('#produtos tbody').append(tr)
        if (produtos.length == 1) {
          $(`#precoProduto-${id}`).val(produtos[0].precos[tipo_cliente])

        }
        $(`#select2-${id}`).select2({
          width: '100%'
        })

      } else {
        Toast.fire({
          icon: 'error',
          title: 'Escolha o cliente!'
        });
      }

    })



    $('#dataHora').datetimepicker({
      i18n: {
        de: {

        }
      },
      format: 'd/m/Y H:i',
      lang: 'pt'
    });
    $('#horario').datetimepicker({
      datepicker: false,
      format: 'H:i',
      lang: 'pt'
    });
    $('#periodo').datepicker({
      multidate: true,

      format: 'dd/mm/yyyy',
      lang: 'pt'
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
                id: `${item.id}-${item.tipo_cliente}`,
              }
            })

          };
        }
      }
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
@endpush
