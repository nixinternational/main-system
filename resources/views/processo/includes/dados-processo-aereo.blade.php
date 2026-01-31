   <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
       aria-labelledby="custom-tabs-two-home-tab">
       <form enctype="multipart/form-data" id="formProcesso"
           action="{{ isset($processo) ? route('update.processo', $processo->id) . '?tipo_processo=aereo' : route('processo.store') }}"
           method="POST">
           @csrf
           @if (isset($processo))
               @method('PUT')
           @endif
           @php
               if (isset($processo)) {
                   $moedasSuportadas += [
                       'ARS' => 'Peso argentino',
                       'CNY' => 'Yuan chinês',
                       'HKD' => 'Dólar de Hong Kong',
                       'MXN' => 'Peso mexicano',
                       'NZD' => 'Dólar neozelandês',
                       'SGD' => 'Dólar de Singapura',
                       'ZAR' => 'Rand sul-africano',
                       'AED' => 'Dirham dos Emirados Árabes',
                       'INR' => 'Rúpia indiana',
                       'RUB' => 'Rublo russo',
                       'TRY' => 'Lira turca',
                       'KRW' => 'Won sul-coreano',
                   ];
               }
           @endphp
           @if (isset($processo))
               @php
                   $tiposMap = [
                       'maritimo' => [
                           'nome' => 'Marítimo', 
                           'icon' => 'fa-ship',
                           'bgColor' => 'var(--theme-gradient-primary)',
                           'textColor' => '#ffffff'
                       ],
                       'aereo' => [
                           'nome' => 'Aéreo', 
                           'icon' => 'fa-plane',
                           'bgColor' => 'var(--theme-gradient-primary)',
                           'textColor' => '#ffffff'
                       ],
                       'rodoviario' => [
                           'nome' => 'Rodoviário', 
                           'icon' => 'fa-truck',
                           'bgColor' => 'var(--theme-gradient-primary)',
                           'textColor' => '#ffffff'
                       ],
                   ];
                   // Se for ProcessoAereo, sempre será 'aereo', caso contrário usa o tipo_processo do modelo
                   $tipo = isset($tipoProcesso) ? $tipoProcesso : ($processo->tipo_processo ?? 'aereo');
                   $tipoInfo = $tiposMap[$tipo] ?? $tiposMap['aereo'];
               @endphp
               <div class="row mb-4">
                   <div class="col-12">
                       <div class="card-tipo-processo shadow-sm" style="background: {{ $tipoInfo['bgColor'] }}; border: none; border-radius: 10px; padding: 20px 25px;">
                           <div class="d-flex align-items-center">
                               <div class="tipo-processo-icon" style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 22px;">
                                   <i class="fas {{ $tipoInfo['icon'] }}" style="font-size: 28px; color: {{ $tipoInfo['textColor'] }};"></i>
                               </div>
                               <div>
                                   <div style="font-size: 13px; color: rgba(255, 255, 255, 0.95); font-weight: 600; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 8px;">
                                       Tipo de Processo
                                   </div>
                                   <div style="font-size: 26px; font-weight: 700; color: {{ $tipoInfo['textColor'] }}; margin: 0; letter-spacing: 0.5px;">
                                       {{ $tipoInfo['nome'] }}
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           @endif
           <div class="row">
               <div class="col-4">
                   <label for="exampleInputEmail1" class="form-label">Cliente</label>
                   <p>{{$processo->cliente->nome}}</p>
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

               @php

               @endphp
               <div class="row mt-3">
                   <div class="col-3">
                       <label for="exampleInputEmail1" class="form-label">Canal</label>
                       <select class="custom-select select2" name="canal">
                           <option value="" selected hidden>Selecione uma opção</option>
                           <option {{ isset($processo) && $processo->canal == 'vermelho' ? 'selected' : '' }}
                               value="vermelho" hidden>Vermelho</option>
                           <option {{ isset($processo) && $processo->canal == 'amarelo' ? 'selected' : '' }}
                               value="amarelo" hidden>Amarelo</option>
                           <option {{ isset($processo) && $processo->canal == 'verde' ? 'selected' : '' }}
                               value="verde" hidden>Verde</option>
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
                           <input type="date" class=" form-control" id="credenciamento_radar"
                               name="data_desembaraco_fim"
                               value="{{ old('data_desembaraco_fim', isset($processo) ? $processo->data_desembaraco_fim : '') }}">
                       </div>
                   </div>

               </div>

               <div class="row mt-1">

                   <div class="col-md-2">
                       <label for="thc_capatazia" class="form-label">THC/CAPATAZIA (R$)</label>
                       <input
                           value="{{ isset($processo->thc_capatazia) ? number_format($processo->thc_capatazia, 2, ',', '.') : '' }}"
                           class="form-control moneyReal" name="thc_capatazia" id="thc_capatazia">
                   </div>

                   <div class="col-md-2">
                       <label for="peso_bruto" class="form-label">PESO BRUTO</label>
                       <input type="text"
                           value="{{ isset($processo->peso_bruto) ? number_format($processo->peso_bruto, 4, ',', '.') : '' }}"
                           class="form-control moneyReal" name="peso_bruto" id="peso_bruto">
                   </div>
                   <div class="col-md-2">
                       <label for="peso_liquido" class="form-label">PESO LÍQUIDO</label>
                       <input type="text"
                           value="{{ isset($processo->peso_liquido) ? number_format($processo->peso_liquido, 4, ',', '.') : '' }}"
                           class="form-control moneyReal" readonly id="peso_liquido">
                   </div>
                  <div class="col-md-2">
                      <label for="multa" class="form-label">MULTA</label>
                      <input type="text"
                          value="{{ isset($processo->multa) ? number_format($processo->multa, 2, ',', '.') : '' }}"
                          class="form-control moneyReal2" name="multa" id="multa">
                  </div>
                  <div class="col-md-2">
                      <label for="taxa_siscomex_total" class="form-label">TAXA SISCOMEX TOTAL (R$)</label>
                      <input type="text" readonly
                          class="form-control moneyReal2" id="taxa_siscomex_total">
                  </div>
                  <div class="col-md-2">
                      <label for="quantidade" class="form-label">QUANTIDADE</label>
                      <input type="text"
                          value="{{ isset($processo->quantidade) ? number_format($processo->quantidade, 4, ',', '.') : '' }}"
                          class="form-control moneyReal" name="quantidade" id="quantidade">
                  </div>
                  <div class="col-md-2">
                      <label for="especie" class="form-label">ESPÉCIE</label>
                      <input type="text" value="{{ isset($processo) ? $processo->especie : '' }}"
                          class="form-control " name="especie" id="especie">
                  </div>

               </div>
               
               @php
                   $nacionalizacaoSelecionada = strtolower($processo->nacionalizacao ?? 'geral');
                   // Normalizar valores antigos: 'outros' -> 'geral'
                   if ($nacionalizacaoSelecionada === 'outros') {
                       $nacionalizacaoSelecionada = 'geral';
                   }
               @endphp
               <div class="row mt-3">
                   <div class="col-md-4">
                       <label for="nacionalizacao" class="form-label">Local de Nacionalização</label>
                       <select class="custom-select" name="nacionalizacao" id="nacionalizacao">
                           <option value="santa_catarina" {{ $nacionalizacaoSelecionada === 'santa_catarina' ? 'selected' : '' }}>Santa Catarina</option>
                           <option value="geral" {{ $nacionalizacaoSelecionada === 'geral' ? 'selected' : '' }}>Geral</option>
                       </select>
                   </div>
               </div>
               
               <div class="row mt-3" id="campos-exw-cif" style="display: {{ $nacionalizacaoSelecionada === 'santa_catarina' ? 'none' : 'block' }};">
                   <div class="col-md-3">
                       <label for="valor_exw_usd" class="form-label">VALOR EXW (USD)</label>
                       <div class="input-group">
                           <span class="input-group-text">USD</span>
                           <input type="text" readonly class="form-control moneyReal2" id="valor_exw_usd">
                       </div>
                   </div>
                   <div class="col-md-3">
                       <label for="valor_exw_brl" class="form-label">VALOR EXW (BRL)</label>
                       <div class="input-group">
                           <span class="input-group-text">R$</span>
                           <input type="text" readonly class="form-control moneyReal2" id="valor_exw_brl">
                       </div>
                   </div>
                   <div class="col-md-3">
                       <label for="valor_cif_usd" class="form-label">VALOR CIF (USD)</label>
                       <div class="input-group">
                           <span class="input-group-text">USD</span>
                           <input type="text" readonly class="form-control moneyReal2" id="valor_cif_usd">
                       </div>
                   </div>
                   <div class="col-md-3">
                       <label for="valor_cif_brl" class="form-label">VALOR CIF (BRL)</label>
                       <div class="input-group">
                           <span class="input-group-text">R$</span>
                           <input type="text" readonly class="form-control moneyReal2" id="valor_cif_brl">
                       </div>
                   </div>
               </div>
               
               <div class="row mt-3" id="campos-cpt" style="display: {{ $nacionalizacaoSelecionada === 'santa_catarina' ? 'block' : 'none' }};">
                   <div class="col-md-3">
                       <label for="valor_cpt_usd" class="form-label">VALOR CPT (USD)</label>
                       <div class="input-group">
                           <span class="input-group-text">USD</span>
                           <input type="text" readonly class="form-control moneyReal2" id="valor_cpt_usd">
                       </div>
                   </div>
                   <div class="col-md-3">
                       <label for="valor_cpt_brl" class="form-label">VALOR CPT (BRL)</label>
                       <div class="input-group">
                           <span class="input-group-text">R$</span>
                           <input type="text" readonly class="form-control moneyReal2" id="valor_cpt_brl">
                       </div>
                   </div>
               </div>
               <div class="row">

               </div>

               <div class="divider-section my-4"></div>

               <div class="section-header mb-4">
                   <div class="d-flex align-items-center justify-content-between flex-wrap">
                       <div class="d-flex align-items-center mb-3 mb-md-0 " >
                           <div class="icon-wrapper mr-3" >
                               <i class="fas fa-exchange-alt" style="margin:0"></i>
                           </div>
                           <h4 class="mb-0">Cotações de Moedas</h4>
                       </div>
                       <button class="btn btn-custom-primary" type="button" id="atualizarCotacoes">
                           <i class="fas fa-sync-alt me-3"></i>Atualizar cotações para hoje
                       </button>
                   </div>
               </div>
               
               <div class="row mb-4">
                   <div class="col-md-4 col-lg-3">
                       <label for="data_cotacao" class="form-label fw-bold">
                           <i class="fas fa-calendar-alt me-3" style="color: #b7aa09;"></i>Data de Cotação
                       </label>
                       <input type="date" class="form-control shadow-sm" name="data_cotacao" id="data_cotacao"
                           value="{{ isset($processo->data_moeda_frete_internacional) ? $processo->data_moeda_frete_internacional : '' }}"
                           max="{{ date('Y-m-d') }}">
                   </div>
               </div>
               <div class="row mt-3">
                   <div class="col-md-6 mb-3">
                       <div class="card-item shadow-sm h-100">
                           <div class="card-header-primary">
                               <i class="fas fa-shipping-fast me-2"></i>
                               <span>Frete Internacional</span>
                           </div>
                       <div class="card-body p-3">
                           <div class="row">
                               <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                   <label for="frete_internacional" class="form-label fw-bold">FRETE INTERNACIONAL</label>
                               <div class="input-group">
                                   <span class="input-group-text" id="frete_internacional_symbol">-</span>
                                   <input
                                       value="{{ isset($processo->frete_internacional) ? number_format($processo->frete_internacional, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal2" name="frete_internacional"
                                       id="frete_internacional" aria-describedby="frete_internacional_symbol">
                               </div>
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="">MOEDA</label>
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
                       <div class="row mt-2">
                           <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                               <label for="frete_internacional_usd" class="form-label small">EM USD</label>
                               <div class="input-group">
                                   <span class="input-group-text">USD</span>
                                   <input readonly
                                       value="{{ isset($processo->frete_internacional_usd) ? number_format($processo->frete_internacional_usd, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal" name="frete_internacional_usd"
                                       id="frete_internacional_usd">
                               </div>
                           </div>
                           <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                               <label for="frete_internacional_brl" class="form-label small">EM BRL</label>
                               <div class="input-group">
                                   <span class="input-group-text">R$</span>
                                   <input readonly
                                       value="{{ isset($processo->frete_internacional_brl) ? number_format($processo->frete_internacional_brl, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal" name="frete_internacional_brl"
                                       id="frete_internacional_brl">
                               </div>
                           </div>
                           <div class="col-12 col-sm-4">
                               <label for="cotacao_frete_internacional" class="form-label small">COTAÇÃO</label>
                               <input
                                   value="{{ isset($processo->cotacao_frete_internacional) ? number_format($processo->cotacao_frete_internacional, 4, ',', '.') : '' }}"
                                   class="form-control cotacao" id="cotacao_frete_internacional"
                                   name="cotacao_frete_internacional">
                           </div>
                       </div>
                       </div>
                       </div>
                   </div>

                   <div class="col-md-6 mb-3">
                       <div class="card-item shadow-sm h-100">
                           <div class="card-header-primary">
                               <i class="fas fa-shield-alt me-2"></i>
                               <span>Seguro Internacional</span>
                           </div>
                       <div class="card-body p-3">
                           <div class="row">
                               <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                   <label for="seguro_internacional" class="form-label fw-bold">SEGURO INTERNACIONAL</label>
                               <div class="input-group">
                                   <span class="input-group-text" id="seguro_internacional_symbol">-</span>
                                   <input
                                       value="{{ isset($processo->seguro_internacional) ? number_format($processo->seguro_internacional, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal2" name="seguro_internacional"
                                       id="seguro_internacional" aria-describedby="seguro_internacional_symbol">
                               </div>
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="">MOEDA</label>
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
                       <div class="row mt-2">
                           <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                               <label for="seguro_internacional_usd" class="form-label small">EM USD</label>
                               <div class="input-group">
                                   <span class="input-group-text">USD</span>
                                   <input readonly
                                       value="{{ isset($processo->seguro_internacional_usd) ? number_format($processo->seguro_internacional_usd, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal2" name="seguro_internacional_usd"
                                       id="seguro_internacional_usd">
                               </div>
                           </div>
                           <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                               <label for="seguro_internacional_brl" class="form-label small">EM BRL</label>
                               <div class="input-group">
                                   <span class="input-group-text">R$</span>
                                   <input readonly
                                       value="{{ isset($processo->seguro_internacional_brl) ? number_format($processo->seguro_internacional_brl, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal2" name="seguro_internacional_brl"
                                       id="seguro_internacional_brl">
                               </div>
                           </div>
                           <div class="col-12 col-sm-4">
                               <label for="cotacao_seguro_internacional" class="form-label small">COTAÇÃO</label>
                               <input
                                   value="{{ isset($processo->cotacao_seguro_internacional) ? number_format($processo->cotacao_seguro_internacional, 4, ',', '.') : '' }}"
                                   class="form-control cotacao" id="cotacao_seguro_internacional"
                                   name="cotacao_seguro_internacional">
                           </div>
                       </div>
                       </div>
                       </div>
                   </div>

                   <div class="col-md-6 mb-3">
                       <div class="card-item shadow-sm h-100">
                           <div class="card-header-primary">
                               <i class="fas fa-plus-circle me-2"></i>
                               <span>Acréscimo do Frete</span>
                           </div>
                       <div class="card-body p-3">
                           <div class="row">
                               <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                   <label for="acrescimo_frete" class="form-label fw-bold">ACRESCIMO DO FRETE</label>
                               <div class="input-group">
                                   <span class="input-group-text" id="acrescimo_frete_symbol">-</span>
                                   <input
                                       value="{{ isset($processo->acrescimo_frete) ? number_format($processo->acrescimo_frete, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal" name="acrescimo_frete" id="acrescimo_frete" aria-describedby="acrescimo_frete_symbol">
                               </div>
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="form-label ">MOEDA</label>
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
                       <div class="row mt-2">
                           <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                               <label for="acrescimo_frete_usd" class="form-label small">EM USD</label>
                               <div class="input-group">
                                   <span class="input-group-text">USD</span>
                                   <input readonly
                                       value="{{ isset($processo->acrescimo_frete_usd) ? number_format($processo->acrescimo_frete_usd, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal" name="acrescimo_frete_usd"
                                       id="acrescimo_frete_usd">
                               </div>
                           </div>
                           <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                               <label for="acrescimo_frete_brl" class="form-label small">EM BRL</label>
                               <div class="input-group">
                                   <span class="input-group-text">R$</span>
                                   <input readonly
                                       value="{{ isset($processo->acrescimo_frete_brl) ? number_format($processo->acrescimo_frete_brl, 2, ',', '.') : '' }}"
                                       class="form-control moneyReal" name="acrescimo_frete_brl"
                                       id="acrescimo_frete_brl">
                               </div>
                           </div>
                           <div class="col-12 col-sm-4">
                               <label for="cotacao_acrescimo_frete" class="form-label small">COTAÇÃO</label>
                               <input
                                   value="{{ isset($processo->cotacao_acrescimo_frete) ? number_format($processo->cotacao_acrescimo_frete, 4, ',', '.') : '' }}"
                                   class="form-control cotacao" id="cotacao_acrescimo_frete"
                                   name="cotacao_acrescimo_frete">
                           </div>
                       </div>
                       </div>
                       </div>
                   </div>

                   <div class="col-md-6 mb-3">
                       <div class="card-item shadow-sm h-100">
                           <div class="card-header-primary">
                               <i class="fas fa-dollar-sign me-2"></i>
                               <span>SERVICE CHARGES</span>
                           </div>
                       <div class="card-body p-3">
                           <div class="row">
                               <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                   <label for="service_charges" class="form-label fw-bold">SERVICE CHARGES</label>
                              <div class="input-group">
                                  <span class="input-group-text" id="service_charges_symbol">-</span>
                                  <input
                                      value="{{ isset($processo->service_charges) ? number_format($processo->service_charges, 7, ',', '.') : '' }}"
                                      class="form-control moneyReal7" name="service_charges" id="service_charges" aria-describedby="service_charges_symbol">
                              </div>
                          </div>
                          <div class="col-12 col-sm-6">
                              <label class="">MOEDA</label>
                              <select name="service_charges_moeda" id="service_charges_moeda"
                                  class="select2 w-100 moedas" aria-label="Moedas BRICS, UE e G20">
                                  <option value="">Selecione um país...</option>
                                  @foreach ($moedasSuportadas as $codigo => $nome)
                                      <option value="{{ $codigo }}"
                                          {{ isset($processo) && $processo->service_charges_moeda == $codigo ? 'selected' : '' }}>
                                          {{ $codigo }} - {{ $nome }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                      <div class="row mt-2">
                          <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                              <label for="service_charges_usd" class="form-label small">EM USD</label>
                              <div class="input-group">
                                  <span class="input-group-text">USD</span>
                                  <input readonly
                                      value="{{ isset($processo->service_charges_usd) ? number_format($processo->service_charges_usd, 2, ',', '.') : '' }}"
                                      class="form-control moneyReal" name="service_charges_usd"
                                      id="service_charges_usd">
                              </div>
                          </div>
                          <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                              <label for="service_charges_brl" class="form-label small">EM BRL</label>
                              <div class="input-group">
                                  <span class="input-group-text">R$</span>
                                  <input readonly
                                      value="{{ isset($processo->service_charges_brl) ? number_format($processo->service_charges_brl, 2, ',', '.') : '' }}"
                                      class="form-control moneyReal" name="service_charges_brl"
                                      id="service_charges_brl">
                              </div>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label for="cotacao_service_charges" class="form-label small">COTAÇÃO</label>
                              <input
                                  value="{{ isset($processo->cotacao_service_charges) ? number_format($processo->cotacao_service_charges, 4, ',', '.') : '' }}"
                                  class="form-control cotacao" id="cotacao_service_charges"
                                  name="cotacao_service_charges">
                          </div>
                      </div>
                      </div>
                      </div>
                   </div>

                   <div class="col-md-6 mb-3">
                       <div class="card-item shadow-sm h-100">
                           <div class="card-header-primary">
                               <i class="fas fa-dollar-sign me-2"></i>
                               <span>Moeda do Processo</span>
                           </div>
                       <div class="card-body p-3">
                           <div class="row">
                               <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                   <label for="display_cotacao" class="form-label fw-bold">COTAÇÃO DO PROCESSO</label>

                               <input
                                   value="{{ isset($processo->cotacao_moeda_processo[$processo->moeda_processo])
                                       ? number_format($processo->cotacao_moeda_processo[$processo->moeda_processo]['venda'], 4, ',', '.')
                                       : '' }}"
                                   class="form-control cotacao" name="display_cotacao" id="display_cotacao" required>
                               <input type="hidden" id="cotacao_moeda_processo" name="cotacao_moeda_processo"
                                   value="{{ json_encode($processo->cotacao_moeda_processo ?? $dolar) }}">
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="form-label ">MOEDA</label>
                               <select name="moeda_processo" id="moeda_processo" class="select2 w-100 moedas"
                                   aria-label="Moedas BRICS, UE e G20">
                                   <option value="">Selecione um país</option>
                                   @foreach ($moedasSuportadas as $codigo => $nome)
                                       <option value="{{ $codigo }}"
                                           {{ isset($processo) && $processo->moeda_processo == $codigo ? 'selected' : '' }}>
                                           {{ $codigo }} - {{ $nome }}
                                       </option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div id="visualizacaoMoedaDolar"
                           class="row mt-2 {{ $processo->moeda_processo == 'USD' || !$processo->moeda_processo ? 'd-none' : '' }}">
                           <div class="col-12">
                               <label for="moeda_processo_usd" class="form-label ">MOEDA EM USD</label>
                               <input disabled class="form-control cotacao"
                                   value="{{ isset($processo->cotacao_moeda_processo[$processo->moeda_processo . '_USD'])
                                       ? number_format($processo->cotacao_moeda_processo[$processo->moeda_processo . '_USD']['venda'], 4, ',', '.')
                                       : '' }}"
                                   name="moeda_processo_usd" id="moeda_processo_usd">
                           </div>
                       </div>
                       </div>
                       </div>
                   </div>
               </div>
               <div class="row">
                  @php
                      $cotacoes = isset($processo->cotacao_moeda_processo)
                          ? (is_array($processo->cotacao_moeda_processo)
                              ? $processo->cotacao_moeda_processo
                              : (is_string($processo->cotacao_moeda_processo) 
                                  ? json_decode($processo->cotacao_moeda_processo, true) 
                                  : []))
                          : [];
                      // Garantir que é um array
                      if (!is_array($cotacoes)) {
                          $cotacoes = [];
                      }
                  @endphp

                      <div class="col-12 mt-4">
                          <div class="card-item shadow-sm" style="flex: 1 1 100%;">
                              <div class="card-header-primary">
                                  <i class="fas fa-edit me-3"></i>
                                  <span>Editar valores de venda das moedas do dia</span>
                                  <span class="badge-custom ms-3" id="cotacao-data-exibicao">
                                      {{ Carbon\Carbon::parse($processo->data_moeda_frete_internacional)->format('d/m/Y') }}
                                  </span>
                              </div>
                              <div class="card-body p-3">
                                  <div class="row" id="cotacoes-moedas-row">
                                      @foreach ($cotacoes as $codigo => $cotacao)
                                           <div class="col-md-3 mb-3">
                                               <label class="form-label fw-bold small text-muted">
                                                   {{ $cotacao['nome'] ?? $codigo }} ({{ $codigo }})
                                               </label>
                                               <input type="hidden" name="cotacao_moeda_processo[{{ $codigo }}][nome]"
                                                   value="{{ $cotacao['nome'] ?? $codigo }}">
                                               <input type="text" step="0.0001" min="0"
                                                   class="form-control cotacao shadow-sm"
                                                   name="cotacao_moeda_processo[{{ $codigo }}][venda]"
                                                   value="{{ number_format($cotacao['venda'], 4, ',', '.') }}"
                                                   aria-label="Valor de venda para {{ $cotacao['nome'] ?? $codigo }}">
                                           </div>
                                       @endforeach
                                   </div>
                               </div>
                           </div>
                       </div>
               </div>



               <style>
                   /* Cor primária customizada */
                   :root {
                       --primary-color: #b7aa09;
                       --primary-dark: #9a8e08;
                       --primary-light: #d4c50a;
                   }

                   /* Seção de Divisor */
                   .divider-section {
                       height: 2px;
                       background: linear-gradient(to right, transparent, var(--theme-primary), transparent);
                       border-radius: 2px;
                   }

                  /* Cabeçalho da Seção */
                  .section-header {
                      padding: 20px;
                      border-radius: 10px;
                  }

                   .icon-wrapper {
                       width: 45px;
                       height: 45px;
                       background: var(--theme-gradient-primary);
                       border-radius: 10px;
                       display: flex;
                       align-items: center;
                       justify-content: center;
                       color: white;
                       font-size: 20px;
                       box-shadow: 0 4px 6px rgba(183, 170, 9, 0.4);
                   }

                   /* Card Item */
                  .card-item {
                      background: #ffffff;
                      border: none;
                      border-radius: 12px;
                       box-sizing: border-box;
                       display: flex;
                       flex-direction: column;
                       transition: all 0.3s ease;
                       overflow: hidden;
                   }

                  .card-item:hover {
                      transform: translateY(-5px);
                      box-shadow: 0 8px 20px rgba(183, 170, 9, 0.2) !important;
                   }

                   /* Cabeçalho do Card */
                   .card-header-primary {
                       background: var(--theme-gradient-primary);
                       color: white;
                       padding: 15px 20px;
                       font-weight: 600;
                       font-size: 16px;
                       display: flex;
                       align-items: center;
                       border-bottom: 2px solid rgba(255, 255, 255, 0.2);
                   }

                   .card-header-primary i {
                       font-size: 18px;
                       margin-right: 10px !important;
                   }

                   .card-header-primary span:not(.badge-custom) {
                       margin-right: 10px;
                   }

                   /* Corpo do Card */
                   .card-body {
                       background: #ffffff;
                       flex: 1;
                   }

                   /* Card Tipo de Processo */
                   .card-tipo-processo {
                       transition: all 0.3s ease;
                       border: none !important;
                   }

                   .card-tipo-processo:hover {
                       transform: translateY(-2px);
                       box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15) !important;
                   }

                   .tipo-processo-icon {
                       transition: all 0.3s ease;
                   }

                   .card-tipo-processo:hover .tipo-processo-icon {
                       background: rgba(255, 255, 255, 0.3) !important;
                       transform: scale(1.05);
                   }

                   /* Para mobile pequeno */
                   @media (max-width: 576px) {
                       .form-control,
                       .select2 {
                           font-size: 14px;
                       }

                       .form-label {
                           font-size: 13px;
                           margin-bottom: 4px;
                       }

                       .select2-container .select2-selection--single {
                           height: 38px !important;
                       }

                       .select2-container--default .select2-selection--single .select2-selection__rendered {
                           line-height: 38px !important;
                           font-size: 14px;
                       }

                       .select2-container--default .select2-selection--single .select2-selection__arrow {
                           height: 38px !important;
                       }
                   }

                   /* Input Groups melhorados */
                   .input-group-text {
                       background: var(--theme-gradient-primary);
                       color: white;
                       border: none;
                       font-weight: 600;
                       min-width: 50px;
                       justify-content: center;
                   }

                   .form-control:focus {
                       border-color: #b7aa09;
                       box-shadow: 0 0 0 0.2rem rgba(183, 170, 9, 0.25);
                   }

                   /* Labels melhorados */
                   .form-label.fw-bold {
                       color: #495057;
                       font-size: 14px;
                       margin-bottom: 8px;
                   }

                   .form-label.small {
                       color: #6c757d;
                       font-size: 12px;
                       font-weight: 600;
                       text-transform: uppercase;
                       letter-spacing: 0.5px;
                   }

                   /* Botão Customizado */
                   .btn-custom-primary {
                       background: var(--theme-gradient-primary);
                       border: none;
                       padding: 12px 30px;
                       font-weight: 600;
                       color: white;
                       box-shadow: 0 4px 6px rgba(183, 170, 9, 0.4);
                       transition: all 0.3s ease;
                   }

                   .btn-custom-primary:hover {
                       background: var(--theme-gradient-primary-hover);
                       transform: translateY(-2px);
                       box-shadow: 0 6px 12px rgba(183, 170, 9, 0.5);
                       color: white;
                   }

                   /* Botão Salvar */
                   .btn-primary {
                       background: var(--theme-gradient-primary);
                       border: none;
                       padding: 12px 30px;
                       font-weight: 600;
                       box-shadow: 0 4px 6px rgba(183, 170, 9, 0.4);
                       transition: all 0.3s ease;
                   }

                   .btn-primary:hover {
                       background: var(--theme-gradient-primary-hover);
                       transform: translateY(-2px);
                       box-shadow: 0 6px 12px rgba(183, 170, 9, 0.5);
                   }

                   /* Badge customizado */
                   .badge-custom {
                       background-color: rgba(255, 255, 255, 0.9) !important;
                       color: #b7aa09 !important;
                       padding: 6px 12px;
                       font-size: 14px;
                       border-radius: 6px;
                       font-weight: 600;
                       margin-left: auto;
                   }

                   /* Espaçamento geral para ícones */
                   i.fas, i.far, i.fal {
                       margin-right: 8px;
                   }

                   /* Ajuste específico para botões */
                   .btn i {
                       margin-right: 8px !important;
                   }

                   /* Ajuste para labels com ícones */
                   .form-label i {
                       margin-right: 8px !important;
                   }
               </style>

           @endif
           <div class="col-1">
               <button type="submit" class="btn btn-primary mt-3">Salvar</button>
           </div>
       </form>

       <script>
           $(document).ready(function() {
               var today = new Date().toISOString().split('T')[0];
               $('#data_cotacao').attr('max', today);

               $('#data_cotacao').on('change', function() {
                   var selectedDate = $(this).val();
                   if (selectedDate > today) {
                       Swal.fire({
                           icon: 'warning',
                           title: 'Data inválida',
                           text: 'Não é permitido selecionar datas futuras. A data foi ajustada para hoje.',
                           timer: 3000
                       });
                       $(this).val(today);
                   }
               });
           });

           function formatarCampoPorClasse(container, classe, decimais) {
               container.find(classe).each(function() {
                   let val = $(this).val();

                   if (val && val.trim() !== '') {
                       val = val.trim().replace('%', '').trim();
                       if (val.includes(',')) {
                           val = val.replace(/\./g, '').replace(',', '.');
                       } else {
                           val = val.replace(',', '.');
                       }
                       let numero = parseFloat(val);
                       if (!isNaN(numero)) {
                           let formatado = numero.toLocaleString('pt-BR', {
                               minimumFractionDigits: decimais,
                               maximumFractionDigits: decimais
                           });
                           if (classe.includes('percentage')) {
                               formatado += ' %';
                           }
                           $(this).val(formatado);
                       }
                   }
               });
           }

           // Monitoramento específico para os campos aéreos (delivery_fee e collect_fee)
           function monitorarCamposAereos() {
               const campoDeliveryFee = $('#delivery_fee');
               const campoCollectFee = $('#collect_fee');

               // Monitora mudanças de valor
               campoDeliveryFee.add(campoCollectFee).on('input change', function(e) {
                   console.trace('Stack trace do evento');
               });

               // Monkey patch do método val()
               const originalVal = $.fn.val;
               $.fn.val = function(value) {
                   if (value !== undefined && (this.is('#delivery_fee') || this.is('#collect_fee'))) {
                       console.trace('Stack trace do .val()');
                   }
                   return originalVal.apply(this, arguments);
               };

               // Monitora focos e blurs para debug
               campoDeliveryFee.add(campoCollectFee).on('focus', function() {
               });

               campoDeliveryFee.add(campoCollectFee).on('blur', function() {
               });
           }

           // Executar o monitoramento
           monitorarCamposAereos();

           function forcarFormatacaoCamposCards() {

               const container = $('#formProcesso');

               formatarCampoPorClasse(container, '.moneyReal', 5);
               formatarCampoPorClasse(container, '.moneyReal2', 2);
               formatarCampoPorClasse(container, '.cotacao', 4);
               formatarCampoPorClasse(container, '.percentage', 7);
               formatarCampoPorClasse(container, '.percentage2', 2);

           }
           $('#data_cotacao').on('blur', function() {
               const $input = $(this);
               const dataCotacao = $input.val();
               const url = "{{ route('cotacao.obter', ['data_cotacao' => ':data_cotacao']) }}".replace(
                   ':data_cotacao', dataCotacao);

               Swal.fire({
                   title: 'Buscando cotações...',
                   text: 'Aguarde enquanto as cotações são atualizadas.',
                   allowOutsideClick: false,
                   allowEscapeKey: false,
                   didOpen: () => {
                       Swal.showLoading();
                   }
               });

               $.ajax({
                   url: url,
                   type: 'GET',
                   dataType: 'json',
                   success: function(response) {
                       const data = response['data'];

                       const moedaFrete = $('#frete_internacional_moeda').val();
                       if (moedaFrete && data[moedaFrete]) {
                           $('#cotacao_frete_internacional').val(MoneyUtils.formatMoney(data[moedaFrete]
                               .venda, 4));
                       }

                       const moedaSeguro = $('#seguro_internacional_moeda').val();
                       if (moedaSeguro && data[moedaSeguro]) {
                           $('#cotacao_seguro_internacional').val(MoneyUtils.formatMoney(data[moedaSeguro]
                               .venda, 4));
                       }

                       const moedaAcrescimo = $('#acrescimo_frete_moeda').val();
                       if (moedaAcrescimo && data[moedaAcrescimo]) {
                           $('#cotacao_acrescimo_frete').val(MoneyUtils.formatMoney(data[moedaAcrescimo]
                               .venda, 4));
                       }

                       const moedaServiceCharges = $('#service_charges_moeda').val();
                       if (moedaServiceCharges && data[moedaServiceCharges]) {
                           $('#cotacao_service_charges').val(MoneyUtils.formatMoney(data[moedaServiceCharges]
                               .venda, 4));
                       }

                       setTimeout(() => {
                           forcarFormatacaoCamposCards();

                           // Atualizar conversões após atualizar cotações
                           if (typeof convertToUSDAndBRL === 'function') {
                               convertToUSDAndBRL('frete_internacional');
                               convertToUSDAndBRL('seguro_internacional');
                               convertToUSDAndBRL('acrescimo_frete');
                               convertToUSDAndBRL('service_charges');
                           }
                           
                           $('#cotacao_frete_internacional, #cotacao_seguro_internacional, #cotacao_acrescimo_frete, #cotacao_service_charges')
                               .trigger('change');
                           
                           // Atualizar valores EXW e CIF após atualizar cotações
                           if (typeof atualizarValoresExwECif === 'function') {
                               setTimeout(function() {
                                   atualizarValoresExwECif();
                               }, 200);
                           }
                       }, 100);
                       const moedaProcesso = $('#moeda_processo').val();
                       if (moedaProcesso && data[moedaProcesso]) {
                           $('#cotacao_moeda_processo').val(JSON.stringify(data));
                           $('#display_cotacao').val(MoneyUtils.formatMoney(data[moedaProcesso].venda, 4));
                       }

                       let dataPesquisa = $input.val();
                       if (dataPesquisa) {
                           let partes = dataPesquisa.split('-');
                           if (partes.length === 3) {
                               let exibicao = partes[2] + '/' + partes[1] + '/' + partes[0];
                               $('#cotacao-data-exibicao').text(exibicao);
                           }
                       }

                       let cotacoes = $('#cotacao_moeda_processo').val();
                       if (cotacoes) {
                           try {
                               cotacoes = JSON.parse(cotacoes);
                               let html = '';
                               Object.keys(cotacoes).forEach(function(codigo) {
                                   let cotacao = cotacoes[codigo];
                                   html += `<div class="col-md-3 mb-2">
                                <label class="form-label">${cotacao.nome ?? codigo} (${codigo})</label>
                                <input type="hidden" name="cotacao_moeda_processo[${codigo}][nome]" 
                                    value="${cotacao.nome ?? codigo}">
                                <input type="text" step="0.0001" min="0" class="form-control cotacao"
                                    name="cotacao_moeda_processo[${codigo}][venda]"
                                    value="${cotacao.venda ?? ''}"
                                    aria-label="Valor de venda para ${cotacao.nome ?? codigo}">
                            </div>`;
                               });
                               $('#cotacoes-moedas-row').html(html);
                           } catch (e) {}
                       }
                       Swal.close();

                       Toast.fire({
                           icon: 'success',
                           title: 'Cotações atualizadas com sucesso!'
                       });
                   },
                   error: function(xhr, status, error) {
                       Swal.fire({
                           icon: 'error',
                           title: 'Erro ao obter cotações',
                           text: error,
                       });
                       console.error('Erro ao obter cotações:', error);
                   }
               });
           });
       </script>
   </div>
