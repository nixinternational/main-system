import { BaseStrategy } from './BaseStrategy.js';
import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Estratégia de cálculo para nacionalização Anápolis
 */
export class AnapolisStrategy extends BaseStrategy {
    calcularImpostos(produto, moedas) {
        return {
            ii: produto.ii || 0,
            ipi: produto.ipi || 0,
            pis: produto.pis || 0,
            cofins: produto.cofins || 0,
            icms: produto.icms || 0
        };
    }

    calcularDespesas(produto, cabecalho) {
        // Para Anápolis, usar lógica padrão de soma de campos
        // Campos externos para Anápolis (definidos diretamente aqui)
        const campos = this.getCamposCabecalho();
        let total = 0;

        campos.forEach(campo => {
            const valor = produto.valoresBrutosCamposExternos?.[campo]?.[produto.rowId] || 0;
            total += valor;
        });

        return {
            total,
            despesaDesembaraco: total
        };
    }

    calcularCustoFinal(produto, totais) {
        // Similar a outras nacionalizações, mas pode ter campos específicos
        const vlrTotalNfComIcmsSt = produto.vlrTotalNfComIcmsSt || totais.vlrTotalNfComIcms || 0;
        const despesaDesembaraco = produto.despesaDesembaraco || 0;
        const diferencaCambialFrete = produto.diferencaCambialFrete || 0;
        const diferencaCambialFob = produto.diferencaCambialFob || 0;
        const vlrIcmsReduzido = produto.vlrIcmsReduzido || 0;
        const quantidade = produto.quantidade || 1;

        const numerador = (vlrTotalNfComIcmsSt + despesaDesembaraco + diferencaCambialFrete + diferencaCambialFob) - vlrIcmsReduzido;
        const custoUnitarioFinal = quantidade > 0 ? numerador / quantidade : 0;
        const custoTotalFinal = custoUnitarioFinal * quantidade;

        return {
            custoUnitarioFinal,
            custoTotalFinal
        };
    }

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
        return ['rep_anapolis', 'correios', 'frete_dta_sts_ana', 'armaz_ana'];
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
            'frete_dta_sts_ana',
            'sda',
            'rep_sts',
            'desp_anapolis',
            'rep_anapolis',
            'correios',
            'li_dta_honor_nix',
            'honorarios_nix'
        ];
    }
}
