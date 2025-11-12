<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 10px;
        }

        td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .blue {
            color: #0000CC;
        }

        .red {
            color: #990000;
        }

        .no-border {
            border: none !important;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .small {
            font-size: 8px;
        }

        .section-title {
            background-color: #ddd;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>

    <table width="100%">
        <tr>
            <td rowspan="4" style="width: 20%; text-align: center;">
                <span class="bold">LOGOTIPO</span><br><br>
            </td>
            <td colspan="12" class="center bold">NOTA FISCAL</td>
            <td colspan="3" class="center">
                Nº<br>
                <span class="bold">000.00</span>
            </td>
        </tr>
        <tr>
            <td colspan="6"></td>
            <td colspan="2" class="center">SAÍDA</td>
            <td colspan="2" class="center bold">X</td>
            <td colspan="2" class="center">ENTRADA</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="10" rowspan="2">
                <span class="bold">EMITENTE</span><br>
                NOME: RAZÃO SOCIAL<br>
                ENDEREÇO<br>
                FONE/FAX<br>
                CNPJ
            </td>
            <td colspan="2">BAIRRO:</td>
            <td colspan="2">MUNICÍPIO:</td>
            <td>CEP:</td>
        </tr>
        <tr>
            <td colspan="2">UF:</td>
            <td colspan="2">INSC. ESTADUAL:</td>
            <td></td>
        </tr>
    </table>

    <br>

    <table width="100%">
        <tr>
            <td rowspan="2" colspan="8" class="bold">
                NATUREZA DA OPERAÇÃO <br><br> ENTRADA DE MERCADORIA
            </td>
            <td rowspan="2" colspan="1" class="blue">CFOP: <br><br> 3.102 </td>


            <td rowspan="2" colspan="1" >INSCRIÇÃO ESTADUAL <br> DO SUBSTITUTO TRIBUTÁRIO <br><br> XX</td>
            <td>1ª VIA DESTINATÁRIO/REMETENTE</td>
        </tr>
        <tr>
            <td>DATA LIMITE PARA<br>EMISSÃO</td>

        </tr>
    </table>
    <h4 style="margin-top: 30px">DESTINATÁRIO REMETENTE</h4>

    <table>
        <tr>
            <td colspan="15">NOME / RAZÃO SOCIAL:

                <br>
                <br>
                {{ $cliente->nome ?? 'NÃO INFORMADO' }}
            </td>
            <td colspan="2">CNPJ / CPF: {{ $cliente->cnpj ?? '-' }}</td>
            <td colspan="1">DATA EMISSÃO: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>

        </tr>
        <tr>
            <td colspan="13">ENDEREÇO:
                <br>
                <br>
                {{ $cliente->endereco ?? 'NÃO INFORMADO' }}
            </td>
            <td colspan="3">BAIRRO/DISTRITO: <br><br>
                {{ $cliente->bairro ?? '-' }}</td>
            <td colspan="1">CEP: {{ $cliente->cep ?? '-' }}</td>
            <td colspan="1">DATA SAÍDA/ENTRADA: {{ $processo->data_desembaraco_fim ? \Carbon\Carbon::parse($processo->data_desembaraco_fim)->format('d/m/Y') : '-' }}</td>


        </tr>
        <tr>
            <td colspan="13">MUNICÍPIO: <br><br>
                {{ $cliente->cidade ?? 'NÃO INFORMADO' }}
            </td>
            <td colspan="2" class="blue">FONE / FAX: {{ $cliente->telefone ?? '-' }}</td>
            <td colspan="1">UF: {{ $cliente->estado ?? '-' }}</td>
            <td colspan="1">INSC. ESTADUAL: {{ $cliente->inscricao_estadual ?? '-' }}</td>
            <td colspan="1">HORA SAÍDA: {{ \Carbon\Carbon::now()->format('H:i') }}</td>

        </tr>

    </table>


    <h4 style="margin-top: 30px">DADOS DO PRODUTO</h4>

    <table>
        {{-- <tr><td colspan="13" class="section-title">DADOS DO PRODUTO</td></tr> --}}
        <tr>
            <th>CÓDIGO</th>
            <th colspan="3">DESCRIÇÃO DOS PRODUTOS</th>
            <th>CLASSIF. FISCAL</th>
            <th>SIT. TRIBUTÁRIA</th>
            <th>UNIDADE</th>
            <th>QUANTIDADE</th>
            <th>VALOR UNIT.</th>
            <th>VALOR TOTAL</th>
            <th>ICMS</th>
            <th>IPI</th>
            <th>OUTROS</th>
        </tr>

        <!-- Produtos do processo -->
        @forelse ($processoProdutos as $index => $produto)
            <tr>
                <td>{{ optional($produto->produto)->codigo ?? ($produto->item ?? ($index + 1)) }}</td>
                <td colspan="3">{{ optional($produto->produto)->nome ?? ($produto->descricao ?? 'PRODUTO NÃO INFORMADO') }}</td>
                <td>{{ optional($produto->produto)->ncm ?? '0000.00.00' }}</td>
                <td>{{ $produto->icms_percent ? number_format($produto->icms_percent, 2, ',', '.') . '%' : '-' }}</td>
                <td>{{ optional($produto->produto)->unidade ?? 'UN' }}</td>
                <td class="right">{{ number_format($produto->quantidade ?? 0, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($produto->valor_unit_nf ?? 0, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($produto->valor_total_nf ?? 0, 2, ',', '.') }}</td>
                <td class="right">{{ $produto->icms_percent ? number_format($produto->icms_percent, 2, ',', '.') . '%' : '-' }}</td>
                <td class="right">{{ $produto->ipi_percent ? number_format($produto->ipi_percent, 2, ',', '.') . '%' : '-' }}</td>
                <td class="right">{{ number_format(($produto->valor_pis ?? 0) + ($produto->valor_cofins ?? 0), 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="center">Nenhum produto cadastrado</td>
            </tr>
        @endforelse
    </table>

    <h4 style="margin-top: 30px">CÁLCULO DO IMPOSTO</h4>
    <table width="100%">
        <tr>
            <td>
                BASE DE CÁLCULO<br><br>
                <span class="right">{{ number_format(($totalProdutos ?? 0) - ($totalIPI ?? 0), 2, ',', '.') }}</span>
            </td>
            <td>
                VALOR DO ICMS<br><br>
                <span class="right">{{ number_format($totalICMS ?? 0, 2, ',', '.') }}</span>
            </td>
            <td>
                BASE DE CÁLCULO ICMS SUBSTITUIÇÃO<br><br>
                <span class="right">{{ number_format(collect($processoProdutos)->sum('base_icms_st') ?? 0, 2, ',', '.') }}</span>
            </td>
            <td>
                VALOR DO ICMS DE SUBSTITUIÇÃO<br><br>
                <span class="right">{{ number_format($totalICMSST ?? 0, 2, ',', '.') }}</span>
            </td>
            <td>
                VALOR TOTAL DOS PRODUTOS<br><br>
                <span class="right">{{ number_format($totalProdutos ?? 0, 2, ',', '.') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                VALOR DO FRETE<br><br>
                <span class="right">{{ number_format(collect($processoProdutos)->sum('frete_brl') ?? 0, 2, ',', '.') }}</span>
            </td>
            <td>
                VALOR DO SEGURO<br><br>
                <span class="right">{{ number_format(collect($processoProdutos)->sum('seguro_brl') ?? 0, 2, ',', '.') }}</span>
            </td>
            <td>
                OUTRAS DESPESAS ACESSÓRIAS<br><br>
                <span class="right">{{ number_format(($processo->outras_taxas_agente ?? 0) + ($processo->despesas_aduaneiras ?? 0), 2, ',', '.') }}</span>
            </td>
            <td>
                VALOR TOTAL DO IPI<br><br>
                <span class="right">{{ number_format($totalIPI ?? 0, 2, ',', '.') }}</span>
            </td>
            <td>
                VALOR TOTAL DA NOTA<br><br>
                <span class="right">{{ number_format($totalNota ?? 0, 2, ',', '.') }}</span>
            </td>
        </tr>
    </table>
    {{-- <div style="page-  -before: always;"></div> --}}

    <h4 style="margin-top: 30px">TRANSPORTADOR / VOLUMES TRANSPORTADOS</h4>

    <table width="100%" style="border-collapse: collapse; font-size: 10px;">

        <tr>
            <td colspan="7" rowspan="3" style="border: 1px solid #000;">NOME / RAZÃO SOCIAL:
                <br><br><strong>RODOPORTO TRANSPORTES RODOVIÁRIOS LTDA</strong>
                << /td>
            <td colspan="2" style="border: 1px solid #000; text-align: center;">
                FRETE POR CONTA
            </td>
            <td rowspan="3" colspan="2" style="border: 1px solid #000;">PLACA VEÍCULO</td>
            <td rowspan="3" colspan="2" style="border: 1px solid #000;">UF</td>
            <td rowspan="3" colspan="3" style="border: 1px solid #000;">CNPJ / CPF</td>
        </tr>
        <tr>
            <td colspan="1" style="border: 1px solid #000;">EMITENTE</td>
            <td colspan="1" style="border: 1px solid #000; color: blue;"></td>

        </tr>
        <tr>
            <td colspan="1" style="border: 1px solid #000;">DESTINATÁRIO</td>
            <td colspan="1" style="border: 1px solid #000; color: blue;">X</td>

        </tr>
        <tr>
            <td colspan="7">
                ENDEREÇO:
                <br>
                <br>
                RUA DO COMERCIO, 055, CENTRO
            </td>
            <td colspan="4">
                MUNICÍPIO
                <br>
                <br>
                SANTOS
            </td>
            <td colspan="2">
                UF
                <br>
                <br>
                SP
            </td>
            <td colspan="3">
                INSCRIÇÃO ESTADUAL
                <br>
                <br>

            </td>
        </tr>
        <tr>
            <td>QUANTIDADE <br> <br> {{ number_format(collect($processoProdutos)->sum('quantidade') ?? 0, 2, ',', '.') }}</td>
            <td colspan="6">ESPÉCIE <br><br>{{ $processo->especie ?? 'NÃO INFORMADO' }}</td>
            <td colspan="2">MARCA<br><br>-</td>
            <td colspan="3">NUMERO<br><br>{{ $processo->numero_processo ?? '-' }}</td>
            <td colspan="2">PESO BRUTO<br><br>{{ number_format($processo->peso_bruto ?? 0, 3, ',', '.') }} KG</td>
            <td colspan="2">PESO LIQUIDO<br><br>{{ number_format($processo->peso_liquido ?? 0, 3, ',', '.') }} KG</td>
        </tr>

    </table>


    <h4 style="margin-top: 30px">DADOS ADICIONAIS</h4>
    <table>
        <tr>
            <td style="width: 45%" class="small">
                INFOS COMPLEMENTARES:<br><br>
                @if($processo->di)
                    DI {{ $processo->di }} 
                @endif
                @if($processo->data_desembaraco_fim)
                    – DESEMBARACADA EM {{ \Carbon\Carbon::parse($processo->data_desembaraco_fim)->format('d/m/Y') }}
                @endif
                @if($processo->numero_processo && $processo->numero_processo != '-')
                    – PROCESSO {{ $processo->numero_processo }}
                @endif
                @if($totalICMS)
                    ICMS R$ {{ number_format($totalICMS, 2, ',', '.') }}
                @endif
                @if($totalIPI)
                    / IPI R$ {{ number_format($totalIPI, 2, ',', '.') }}
                @endif
                @if($totalPIS)
                    / PIS R$ {{ number_format($totalPIS, 2, ',', '.') }}
                @endif
                @if($totalCOFINS)
                    / COFINS R$ {{ number_format($totalCOFINS, 2, ',', '.') }}
                @endif
                @if($processo->taxa_dolar)
                    / TAXA DO DÓLAR: {{ number_format($processo->taxa_dolar, 4, ',', '.') }}
                @endif
                @if($processo->taxa_siscomex || $processo->afrmm || $processo->armazenagem_sts || $processo->frete_dta_sts_ana || $processo->honorarios_nix)
                    – DESPESAS ADUANEIRAS:
                    @if($processo->taxa_siscomex)
                        TX SISCOMEX R$ {{ number_format(collect($processoProdutos)->sum('taxa_siscomex') ?? 0, 2, ',', '.') }}
                    @endif
                    @if($processo->afrmm)
                        / AFRMM R$ {{ number_format($processo->afrmm, 2, ',', '.') }}
                    @endif
                    @if($processo->armazenagem_sts)
                        / ARMAZ DTA R$ {{ number_format($processo->armazenagem_sts, 2, ',', '.') }}
                    @endif
                    @if($processo->frete_dta_sts_ana)
                        / FRETE DTA R$ {{ number_format($processo->frete_dta_sts_ana, 2, ',', '.') }}
                    @endif
                    @if($processo->honorarios_nix)
                        / HONORÁRIOS DESP. R$ {{ number_format($processo->honorarios_nix, 2, ',', '.') }}
                    @endif
                @endif
                @if($processo->desp_anapolis)
                    – MERCADORIA A SER RETIRADA NO PORTO SECO CENTRO OESTE ANÁPOLIS.
                @endif
                O PAGAMENTO DO ICMS SERÁ EFETUADO APÓS A ENTRADA DA MERCADORIA EM SEU NOVO ESTABELECIMENTO.
                <br><br>
            </td>
            <td style="width: 45%" colspan="3">RESERVADO AO FISCO</td>
            <td style="width: 10%">Nº DE CONTROLE DO FORMULARIO <br><br><br> 0000.000</td>
        </tr>
    </table>
    <br><br>
    <table>
        <tr>
            <td colspan="17">RECEBEMOS DE (RAZÃO SOCIAL DO EMITENTE) OS PRODUTOS CONSTANTES DA NOTA FISCAL INDICADA AO
                LADO</td>
        </tr>
        <tr>
            <td rowspan="2" colspan="2">DATA DO RECEBIMENTO

                <br><br>
                25/07/2025
            </td>
            <td colspan="14">ASSINATURA</td>
            <td colspan="1">NOTA FISCAL</td>
        </tr>
        <tr>
            <td colspan="14"></td>
            <td>N 000.00</td>
        </tr>
    </table>

</body>

</html>
