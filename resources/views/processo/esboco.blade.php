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
                SHANDONG MESSI POWER CO LTD
            </td>
            <td colspan="2">CNPJ / CPF:</td>
            <td colspan="1">DATA EMISSÃO: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>

        </tr>
        <tr>
            <td colspan="13">ENDEREÇO:
                <br>
                <br>
                JINAN, SHANDONG, CHINA
            </td>
            <td colspan="3">BAIRRO/DISTRITO: <br><br>
                JINAN DISTRICT</td>
            <td colspan="1">CEP:</td>
            <td colspan="1">DATA SAÍDA/ENTRADA</td>


        </tr>
        <tr>
            <td colspan="13">MUNICÍPIO: <br><br>
                JINAN SHANDONG
            </td>
            <td colspan="2" class="blue">FONE / FAX:</td>
            <td colspan="1">UF:</td>
            <td colspan="1">INSC. ESTADUAL:</td>
            <td colspan="1">HORA SAÍDA:</td>

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

        <!-- Exemplo de linha de produto (replicável) -->
        @foreach (range(1, 10) as $index)
            <tr>
                <td>001</td>
                <td colspan="3">Produto Exemplo</td>
                <td>0000.00.00</td>
                <td>XXX</td>
                <td>PCS</td>
                <td>10</td>
                <td>50,00</td>
                <td>500,00</td>
                <td>18%</td>
                <td>5%</td>
                <td>-</td>
            </tr>
        @endforeach
    </table>

    <h4 style="margin-top: 30px">CÁLCULO DO IMPOSTO</h4>
    <table width="100%">
        <tr>
            <td>
                BASE DE CÁLCULO<br><br>
                -
            </td>
            <td>
                VALOR DO ICMS<br><br>
                -
            </td>
            <td>
                BASE DE CÁLCULO ICMS SUBSTITUIÇÃO<br><br>
                -
            </td>
            <td>
                VALOR DO ICMS DE SUBSTITUIÇÃO<br><br>
                -
            </td>
            <td>
                VALOR TOTAL DOS PRODUTOS<br><br>
                -
            </td>
        </tr>
        <tr>
            <td>
                VALOR DO FRETE<br><br>
                -
            </td>
            <td>
                VALOR DO SEGURO<br><br>
                -
            </td>
            <td>
                OUTRAS DESPESAS ACESSÓRIAS<br><br>
                -
            </td>
            <td>
                VALOR TOTAL DO IPI<br><br>
                -
            </td>
            <td>
                VALOR TOTAL DA NOTA<br><br>
                -
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
            <td>QUANTIDADE <br> <br> XXXX</td>
            <td colspan="6">ESPÉCIE <br><br>XXXX</td>
            <td colspan="2">MARCA<br><br>XXXX</td>
            <td colspan="3">NUMERO<br><br>XXXX</td>
            <td colspan="2">PESO BRUTO<br><br>XXXX</td>
            <td colspan="2">PESO LIQUIDO<br><br>XXXX</td>
        </tr>

    </table>


    <h4 style="margin-top: 30px">DADOS ADICIONAIS</h4>
    <table>
        <tr>
            <td style="width: 45%" class="small">
                INFOS COMPLEMENTARES:<br><br>
                DI 24/2387113-8 – DESEMBARACADA EM 31/10/2024 – PROCESSO 1911PM-024 T1 R$ 59.247,85 / IPI R$ 5.977,14 /
                PIS R$ 10.961,59 / COFINS R$ 54.742,87 / TAXA DO DÓLAR: 5,7801 – DESPESAS ADUANEIRAS: TX SISCOMEX R$
                47,30 – ABERMN R$ 3.659,39 – ARMAZ DTA R$ 2.505,79 – FRETE DTA R$: 17.110,00 – HONORÁRIOS DESP. R$ 76,00
                – MERCADORIA A SER RETIRADA NO PORTO SECO CENTRO OESTE ANÁPOLIS. O PAGAMENTO DO ICMS SERÁ EFETUADO APÓS
                A ENTRADA DA MERCADORIA EM SEU NOVO ESTABELECIMENTO.
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
