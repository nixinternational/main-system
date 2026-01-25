import { BaseStrategy } from './BaseStrategy.js';
import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Estratégia de cálculo para nacionalização Mato Grosso
 */
export class MatoGrossoStrategy extends BaseStrategy {
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
     * Calcula despesas para Mato Grosso
     * Fórmula: SOMA(multa:honorario_nix)-(multa+taxa_siscomex+capatazia+afrmm)
     */
    calcularDespesas(produto, cabecalho) {
        // Campos externos para Mato Grosso (definidos diretamente aqui)
        const campos = this.getCamposCabecalho();
        let somaTotal = 0;
        const camposExcluidos = [
            'multa',
            'taxa_siscomex',
            'capatazia',
            'afrmm'
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
     * Calcula o custo final para Mato Grosso
     * Fórmula: ((AX19+BV19+BW19+BX19)/F19)
     * Onde:
     * AX = VLR TOTAL NF C/ICMS-ST
     * BV = DESP DESEMBARACO
     * BW = DIF CAMBIAL FRETE
     * BX = DIF CAMBIAL FOB
     * F = QUANTIDADE
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
     * Calcula a base de ICMS sem redução para Mato Grosso
     * Fórmula: =(X19+AF19+AH19+AJ19+AK19+AL19)
     * Onde:
     * X = vlr aduaneiro brl
     * AF = vlr ii
     * AH = vlr ipi
     * AJ = vlr pis
     * AK = vlr cofins
     * AL = desp aduaneira
     */
    calcularBcIcmsSemReducao(produto, impostos, despesas) {
        const vlrAduaneiro = produto.vlrAduaneiro || 0;
        const vlrII = produto.vlrII || 0;
        const vlrIPI = produto.vlrIPI || 0;
        const vlrPIS = produto.vlrPIS || 0;
        const vlrCOFINS = produto.vlrCOFINS || 0;
        const despAduaneira = despesas || 0;

        return vlrAduaneiro + vlrII + vlrIPI + vlrPIS + vlrCOFINS + despAduaneira;
    }

    /**
     * Calcula a base de ICMS reduzido para Mato Grosso
     * Fórmula: =((X19+AF19+AH19+AJ19+AK19+AL19)/(1-AC19))*(SE(AE19=0;1;AE19))
     * Onde:
     * X = vlr aduaneiro brl
     * AF = vlr ii
     * AH = vlr ipi
     * AJ = vlr pis
     * AK = vlr cofins
     * AL = desp aduaneira
     * AC = IMCS_PERCENT
     * AE = reducao
     */
    calcularBcIcmsReduzido(produto, impostos, despesas) {
        const vlrAduaneiro = produto.vlrAduaneiro || 0;
        const vlrII = produto.vlrII || 0;
        const vlrIPI = produto.vlrIPI || 0;
        const vlrPIS = produto.vlrPIS || 0;
        const vlrCOFINS = produto.vlrCOFINS || 0;
        const despAduaneira = despesas || 0;
        const icmsPercent = impostos.icms || 0;
        const reducao = produto.reducao || 1;

        const numerador = vlrAduaneiro + vlrII + vlrIPI + vlrPIS + vlrCOFINS + despAduaneira;
        const divisor = 1 - icmsPercent;
        const fatorReducao = reducao === 0 ? 1 : reducao;

        return (numerador / divisor) * fatorReducao;
    }

    /**
     * Calcula CFR para Mato Grosso
     * Fórmula: FOB Total USD + Frete Internacional USD + Seguro Internacional USD
     */
    calcularCFR(produto, moedas) {
        const fobTotal = produto.fobTotal || 0;
        const freteIntUsd = produto.freteIntUsd || 0;
        const seguroIntUsd = produto.seguroIntUsd || 0;
        return fobTotal + freteIntUsd + seguroIntUsd;
    }

    /**
     * Calcula valor aduaneiro para Mato Grosso
     * Fórmula: VLR CFR Total + Acréscimo Frete USD + THC USD
     */
    calcularValorAduaneiro(produto, moedas) {
        const vlrCrfTotal = produto.vlrCrfTotal || 0;
        const acrescimoFreteUsd = produto.acrescimoFreteUsd || 0;
        const thcUsd = produto.thcUsd || 0;
        return vlrCrfTotal + acrescimoFreteUsd + thcUsd;
    }

    /**
     * Calcula despesa aduaneira para Mato Grosso
     * Fórmula: taxa siscomex linha + afrmm
     */
    calcularDespesaAduaneira(produto) {
        const taxaSiscomex = produto.taxaSiscomex || 0;
        const afrmm = produto.afrmm || 0;
        return taxaSiscomex + afrmm;
    }

    calcularDespesaDesembaraco(produto, cabecalho) {
        const despesas = this.calcularDespesas(produto, cabecalho);
        return despesas.despesaDesembaraco || 0;
    }

    getColunasVisiveis() {
        return [
            'dez_porcento',
            'custo_com_margem',
            'vlr_ipi_mg',
            'vlr_icms_mg',
            'pis_mg',
            'cofins_mg',
            'custo_total_final_credito',
            'custo_unit_credito',
            'bc_icms_st_mg',
            'mva_mg',
            'icms_st_mg',
            'vlr_icms_st_mg',
            'custo_total_c_icms_st',
            'custo_unit_c_icms_st',
            'exportador_mg',
            'tributos_mg',
            'despesas_mg',
            'total_pago_mg',
            'percentual_s_fob_mg'
        ];
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
            'frete_sts_cgb',
            'diarias',
            'sda',
            'rep_sts',
            'armaz_cgb',
            'rep_cgb',
            'demurrage',
            'tx_def_li',
            'li_dta_honor_nix',
            'honorarios_nix'
        ];
    }
}
