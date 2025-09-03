   <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
       aria-labelledby="custom-tabs-two-home-tab">


       <div class="row">
           <div class="col-4">
               <label for="exampleInputEmail1" class="form-label">Cliente</label>
               <select {{ isset($processo) ? 'readonly' : '' }} class="custom-select select2" name="cliente_id">
                   <option selected disabled>Selecione uma opção</option>
                   @foreach ($clientes as $cliente)
                       <option {{ isset($processo) && $processo->cliente_id == $cliente->id ? 'selected' : '' }}
                           value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                   @endforeach
               </select>
           </div>
           @if (isset($processo))
               <div class="col-md-4">
                   <label for="processo_codigo_interno" class="form-label">PROCESSO</label>
                   <input value="{{ isset($processo) ? $processo->codigo_interno : '' }}" class=" form-control"
                       name="codigo_interno" id="processo_codigo_interno">
               </div>
               <div class="col-md-4">
                   <label for="descricao" class="form-label">Descrição (max 120 caracteres)</label>
                   <input value="{{ isset($processo) ? $processo->descricao : '' }}" class=" form-control"
                       name="descricao" id="descricao">
               </div>
           @endif

       </div>
       @if (isset($processo))
           <div class="row mt-3">
               <div class="col-3">
                   <label for="exampleInputEmail1" class="form-label">Canal</label>
                   <select class="custom-select select2" name="canal">
                       <option value="" selected hidden>Selecione uma opção</option>
                       <option {{ isset($processo) && $processo->canal == 'vermelho' ? 'selected' : '' }}
                           value="vermelho" hidden>Vermelho</option>
                       <option {{ isset($processo) && $processo->canal == 'amarelo' ? 'selected' : '' }} value="amarelo"
                           hidden>Amarelo</option>
                       <option {{ isset($processo) && $processo->canal == 'verde' ? 'selected' : '' }} value="verde"
                           hidden>Verde</option>
                   </select>
               </div>
               <div class="col-3">
                   <label for="status" class="form-label">Status</label>
                   <select class="custom-select select2" name="status">
                       <option value="" selected hidden>Selecione uma opção</option>
                       <option {{ isset($processo) && $processo->status == 'andamento' ? 'selected' : '' }}
                           value="andamento" hidden>Em Andamento</option>
                       <option {{ isset($processo) && $processo->status == 'finalizado' ? 'selected' : '' }}
                           value="finalizado" hidden>Finalizado</option>
                       <option {{ isset($processo) && $processo->status == 'prestacao_contas' ? 'selected' : '' }}
                           value="prestacao_contas" hidden>Prestação de Contas</option>
                   </select>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6">
                   <div class="form-group">
                       <label for="credenciamento_radar">Início Processo</label>
                       <input type="date" class=" form-control" id="credenciamento_radar"
                           name="data_desembaraco_inicio"
                           value="{{ old('data_desembaraco_inicio', isset($processo) ? $processo->data_desembaraco_inicio : '') }}">
                   </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6">
                   <div class="form-group">
                       <label for="credenciamento_radar">Data Desembaraço</label>
                       <input type="date" class=" form-control" id="credenciamento_radar" name="data_desembaraco_fim"
                           value="{{ old('data_desembaraco_fim', isset($processo) ? $processo->data_desembaraco_fim : '') }}">
                   </div>
               </div>

           </div>

           <div class="row mt-1">


               <div class="col-md-2">
                   <label for="thc_capatazia" class="form-label">THC/CAPATAZIA (R$)</label>
                   <input
                       value="{{ isset($processo->thc_capatazia) ? number_format($processo->thc_capatazia, 5, ',', '.') : '' }}"
                       class="form-control moneyReal" name="thc_capatazia" id="thc_capatazia">
               </div>

               <div class="col-md-2">
                   <label for="peso_bruto" class="form-label">PESO BRUTO</label>
                   <input type="text"
                       value="{{ isset($processo->peso_bruto) ? number_format($processo->peso_bruto, 5, ',', '.') : '' }}"
                       class="form-control moneyReal" name="peso_bruto" id="peso_bruto">
               </div>
               <div class="col-md-2">
                   <label for="peso_bruto" class="form-label">PESO LÍQUIDO</label>
                   <input type="text"
                       value="{{ isset($processo->peso_liquido) ? number_format($processo->peso_liquido, 5, ',', '.') : '' }}"
                       class="form-control moneyReal" readonly>
               </div>
               <div class="col-md-2">
                   <label for="multa" class="form-label">MULTA</label>
                   <input type="text"
                       value="{{ isset($processo->multa) ? number_format($processo->multa, 5, ',', '.') : '' }}"
                       class="form-control moneyReal" name="multa" id="multa">
               </div>
               <div class="col-md-2">
                   <label for="multa" class="form-label">QUANTIDADE</label>
                   <input type="text"
                       value="{{ isset($processo->quantidade) ? number_format($processo->quantidade, 5, ',', '.') : '' }}"
                       class="form-control" name="quantidade" id="quantidade">
               </div>
               <div class="col-md-2">
                   <label for="multa" class="form-label">ESPÉCIE</label>
                   <input type="text" value="{{ isset($processo) ? $processo->especie : '' }}"
                       class="form-control " name="especie" id="especie">
               </div>

           </div>
           <div class="row">

           </div>

           <div class="" style="margin: 2% 0; height:1px; background-color:black; width: 100%"></div>

           <div class="d-flex align-center">
               <h4 class="mr-2">Cotações</h4> <button class="btn btn-success" type="button"
                   id="atualizarCotacoes">Atualizar
                   cotações</button>
               <a href="{{ route('currency.update') }}" class="btn btn-primary">
                   Atualizar moedas
               </a>

           </div>
           <div class=" mt-3" style="display: flex; gap:10px">

               <div class=" alert alert-secondary p-3">
                   <div class="row">
                       <div class="col-sm-6 ">
                           <label for="frete_internacional" class="form-label text-white">FRETE
                               INTERNACIONAL
                           </label>
                           <input
                               value="{{ isset($processo->frete_internacional) ? number_format($processo->frete_internacional, 5, ',', '.') : '' }}"
                               class="form-control moneyReal" name="frete_internacional" id="frete_internacional">
                       </div>
                       <div class="col-sm-6 ">

                           <label class="text-white">MOEDA</label>
                           <select name="frete_internacional_moeda" id="frete_internacional_moeda"
                               class="select2 w-100 moedas" aria-label="Moedas BRICS, UE e G20">
                               <option value="">Selecione um país...</option>

                               @foreach ($moedasSuportadas as $codigo => $nome)
                                   <option value="{{ $codigo }}"
                                       {{ isset($processo) && $processo->frete_internacional_moeda == $codigo ? 'selected' : '' }}>
                                       {{ $codigo }} - {{ $nome }}
                                   </option>
                               @endforeach

                           </select>

                       </div>
                   </div>
                   <div class="row">
                       <div class="col-sm-6 ">

                           <input readonly
                               value="{{ isset($processo) ? number_format($processo->frete_internacional * ($dolar[$processo->frete_internacional_moeda]['compra'] ?? 0), 5, ',', '.') : '' }}"
                               class="form-control moneyReal" name="frete_internacional_visualizacao"
                               id="frete_internacional_visualizacao">
                       </div>
                       <div class="col-sm-6 ">

                           <input
                               value="{{ isset($processo->cotacao_frete_internacional) ? $processo->cotacao_frete_internacional : '' }}"
                               class="form-control cotacao" id="cotacao_frete_internacional"
                               name="cotacao_frete_internacional" style="margin: 0 auto">

                       </div>

                   </div>
                   <div class="row mt-1">
                       <div class="col-12">
                           <span id="">Data de
                               Cotação: </span>

                           <input type="date" class=" form-control" name="data_moeda_frete_internacional"
                               id="data_moeda_frete_internacional"
                               value="{{ isset($processo->data_moeda_frete_internacional) ? $processo->data_moeda_frete_internacional : '' }}">
                       </div>
                   </div>
               </div>

               <div class=" alert alert-secondary p-3">
                   <div class="row">
                       <div class="col-lg-6  ">
                           <label for="seguro_internacional" class="form-label text-white">SEGURO
                               INTERNACIONAL
                           </label>
                           <input
                               value="{{ isset($processo->seguro_internacional) ? number_format($processo->seguro_internacional, 5, ',', '.') : '' }}"
                               class="form-control moneyReal" name="seguro_internacional" id="seguro_internacional">
                       </div>
                       <div class="col-sm-6 ">
                           <label class="text-white">MOEDA</label>
                           <select name="seguro_internacional_moeda" id="seguro_internacional_moeda"
                               class="select2 w-100 moedas" aria-label="Moedas BRICS, UE e G20">
                               <option value="">Selecione um país...</option>
                               @foreach ($moedasSuportadas as $codigo => $nome)
                                   <option value="{{ $codigo }}"
                                       {{ isset($processo) && $processo->seguro_internacional_moeda == $codigo ? 'selected' : '' }}>
                                       {{ $codigo }} - {{ $nome }}
                                   </option>
                               @endforeach


                           </select>

                       </div>
                   </div>
                   <div class="row">
                       <div class="col-lg-6  ">

                           <input readonly
                               value="{{ isset($processo) ? number_format($processo->seguro_internacional * ($dolar[$processo->seguro_internacional_moeda]['compra'] ?? 0), 5, ',', '.') : '' }}"
                               class="form-control moneyReal" name="seguro_internacional_visualizacao"
                               id="seguro_internacional_visualizacao">
                       </div>
                       <div class="col-sm-6 ">
                           <input
                               value="{{ isset($processo->cotacao_seguro_internacional) ? $processo->cotacao_seguro_internacional : '' }}"
                               class="form-control cotacao" id="cotacao_seguro_internacional"
                               name="cotacao_seguro_internacional" style="margin: 0 auto">
                       </div>
                   </div>
                   <div class="row mt-1">
                       <div class="col-12">
                           <span id="">Data de
                               Cotação: </span>
                           <input type="date" class=" form-control" name="data_moeda_seguro_internacional"
                               id="data_moeda_seguro_internacional"
                               value="{{ isset($processo->data_moeda_seguro_internacional) ? $processo->data_moeda_seguro_internacional : '' }}">
                       </div>
                   </div>
               </div>

               <div class=" alert alert-secondary p-3">
                   <div class="row">
                       <div class="col-6 ">
                           <label for="acrescimo_frete" class="form-label text-white">ACRESCIMO
                               DO
                               FRETE</label>
                           <input
                               value="{{ isset($processo->acrescimo_frete) ? number_format($processo->acrescimo_frete, 5, ',', '.') : '' }}"
                               class="form-control moneyReal" name="acrescimo_frete" id="acrescimo_frete">
                       </div>
                       <div class="col-6 ">

                           <label class="form-label text-white">MOEDA</label>
                           <select name="acrescimo_frete_moeda" id="acrescimo_frete_moeda"
                               class="select2 w-100 moedas" aria-label="Moedas BRICS, UE e G20">
                               <option value="">Selecione um país</option>

                               @foreach ($moedasSuportadas as $codigo => $nome)
                                   <option value="{{ $codigo }}"
                                       {{ isset($processo) && $processo->acrescimo_frete_moeda == $codigo ? 'selected' : '' }}>
                                       {{ $codigo }} - {{ $nome }}
                                   </option>
                               @endforeach
                           </select>
                       </div>
                   </div>

                   <div class="row ">



                       <div class="col-sm-6 ">
                           <input readonly
                               value="{{ isset($processo) ? number_format($processo->acrescimo_frete * ($dolar[$processo->acrescimo_frete_moeda]['compra'] ?? 0), 5, ',', '.') : '' }}"
                               class="form-control moneyReal" name="acrescimo_frete_visualizacao"
                               id="acrescimo_frete_visualizacao">
                       </div>
                       <div class="col-sm-6 ">
                           <input
                               value="{{ isset($processo->cotacao_acrescimo_frete) ? $processo->cotacao_acrescimo_frete : '' }}"
                               style="margin: 0 auto" class="form-control cotacao" id="cotacao_acrescimo_frete"
                               name="cotacao_acrescimo_frete">
                       </div>
                   </div>

                   <div class="row mt-1">
                       <div class="col-12">
                           <span id="">Data de
                               Cotação: </span>
                           <input type="date" class=" form-control" name="data_moeda_acrescimo_frete"
                               id="data_moeda_acrescimo_frete"
                               value="{{ isset($processo->data_moeda_acrescimo_frete) ? $processo->data_moeda_acrescimo_frete : '' }}">
                       </div>

                   </div>
               </div>

           </div>



       @endif



   </div>
