import { BaseStrategy } from './BaseStrategy.js';
import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Estratégia de cálculo para nacionalização Santos
 */
export class SantosStrategy extends BaseStrategy {
    /**
     * Calcula impostos para Santos
     */
    calcularImpostos(produto, moedas) {
        return {
            ii: produto.ii || 0,
            ipi: produto.ipi || 0,
            pis: produto.pis || 0,
            cofins: produto.cofins || 0,
            icms: produto.icms || 0
        };
    }

    /**
     * Calcula despesas para Santos
     * Fórmula: SOMA(BA19:BQ19)-(BA19+BB19+BC19+BI19+BJ19+BQ19)
     * Onde:
     * BA: MULTA
     * BB: TX DEF. LI
     * BC: TAXA SISCOMEX
     * BI: CAPATAZIA
     * BJ: AFRMM
     * BQ: HONORÁRIOS NIX
     */
    calcularDespesas(produto, cabecalho) {
        // Campos externos para Santos (definidos diretamente aqui)
        const campos = this.getCamposCabecalho();
        let somaTotal = 0;
        const camposExcluidos = [
            'multa',
            'tx_def_li',
            'taxa_siscomex',
            'capatazia',
            'afrmm',
            'honorarios_nix'
        ];

        campos.forEach(campo => {
            const valor = produto.valoresBrutosCamposExternos?.[campo]?.[produto.rowId] || 0;
            somaTotal += valor;
        });

        let somaExcluidos = 0;
        camposExcluidos.forEach(campo => {
            const valor = produto.valoresBrutosCamposExternos?.[campo]?.[produto.rowId] || 0;
            somaExcluidos += valor;
        });

        const despesaDesembaraco = somaTotal - somaExcluidos;

        return {
            total: despesaDesembaraco,
            despesaDesembaraco
        };
    }

    /**
     * Calcula o custo final para Santos
     * Fórmula: (VLR TOTAL NF C/ICMS-ST + DESP. DESEMBARAÇO + DIF.CAMBIAL FOB + DIF. CAMBIAL FRETE) / quantidade
     */
    calcularCustoFinal(produto, totais) {
        const vlrTotalNfComIcmsSt = produto.vlrTotalNfComIcmsSt || totais.vlrTotalNfComIcms || 0;
        const despesaDesembaraco = produto.despesaDesembaraco || 0;
        const diferencaCambialFrete = produto.diferencaCambialFrete || 0;
        const diferencaCambialFob = produto.diferencaCambialFob || 0;
        const quantidade = produto.quantidade || 1;

        const numerador = vlrTotalNfComIcmsSt + despesaDesembaraco + diferencaCambialFrete + diferencaCambialFob;
        const custoUnitarioFinal = quantidade > 0 ? numerador / quantidade : 0;
        const custoTotalFinal = custoUnitarioFinal * quantidade;

        return {
            custoUnitarioFinal,
            custoTotalFinal
        };
    }

    /**
     * Calcula a base de ICMS reduzido para Santos
     * Fórmula: =((W19+AE19+AG19+AI19+AJ19+AK19)/(1-AB19))*(SE(AD19=0;1;AD19))
     */
    calcularBcIcmsReduzido(produto, impostos, despesas) {
        const vlrAduaneiro = produto.vlrAduaneiro || 0;
        const vlrII = produto.vlrII || 0;
        const vlrIPI = produto.vlrIPI || 0;
        const vlrPIS = produto.vlrPIS || 0;
        const vlrCOFINS = produto.vlrCOFINS || 0;
        const despAduaneira = despesas || 0;
        const icmsReduzidoPercent = impostos.icms || 0;
        const reducao = produto.reducao || 1;

        const numerador = vlrAduaneiro + vlrII + vlrIPI + vlrPIS + vlrCOFINS + despAduaneira;
        const divisor = 1 - icmsReduzidoPercent;
        const fatorReducao = reducao === 0 ? 1 : reducao;

        return (numerador / divisor) * fatorReducao;
    }

    calcularBcIcmsSemReducao(produto, impostos, despesas) {
        const vlrAduaneiro = produto.vlrAduaneiro || 0;
        const vlrII = produto.vlrII || 0;
        const vlrIPI = produto.vlrIPI || 0;
        const vlrPIS = produto.vlrPIS || 0;
        const vlrCOFINS = produto.vlrCOFINS || 0;
        return vlrAduaneiro + vlrII + vlrIPI + vlrPIS + vlrCOFINS + despesas;
    }

    calcularCFR(produto, moedas) {
        const fobTotal = produto.fobTotal || 0;
        const freteIntUsd = produto.freteIntUsd || 0;
        return fobTotal + freteIntUsd;
    }

    calcularValorAduaneiro(produto, moedas) {
        // Implementação padrão
        const vlrCrfTotal = produto.vlrCrfTotal || 0;
        const seguroIntUsd = produto.seguroIntUsd || 0;
        const acrescimoFreteUsd = produto.acrescimoFreteUsd || 0;
        const thcUsd = produto.thcUsd || 0;
        return vlrCrfTotal + seguroIntUsd + acrescimoFreteUsd + thcUsd;
    }

    calcularDespesaDesembaraco(produto, cabecalho) {
        const despesas = this.calcularDespesas(produto, cabecalho);
        return despesas.despesaDesembaraco || 0;
    }

    getColunasVisiveis() {
        return ['tx_correcao_lacre'];
    }

    getCamposCabecalho() {
        return [
            'outras_taxas_agente',
            'liberacao_bl',
            'desconsolidacao',
            'isps_code',
            'handling',
            'capatazia',
            'afrmm',
            'armazenagem_sts',
            'frete_sts_gyn',
            'sda',
            'rep_sts',
            'tx_correcao_lacre',
            'li_dta_honor_nix',
            'honorarios_nix'
        ];
    }
}
