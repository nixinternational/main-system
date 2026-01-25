import { BaseStrategy } from './BaseStrategy.js';
import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Estratégia de cálculo para nacionalização Santa Catarina
 */
export class SantaCatarinaStrategy extends BaseStrategy {
    /**
     * Calcula impostos para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} moedas - Objeto com cotações de moedas
     * @returns {Object} - Objeto com impostos calculados
     */
    calcularImpostos(produto, moedas) {
        // Implementação específica para Santa Catarina
        // Por enquanto retorna estrutura básica
        return {
            ii: produto.ii || 0,
            ipi: produto.ipi || 0,
            pis: produto.pis || 0,
            cofins: produto.cofins || 0,
            icms: produto.icms || 0
        };
    }

    /**
     * Calcula despesas para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} cabecalho - Valores dos campos do cabeçalho
     * @returns {Object} - Objeto com despesas calculadas
     */
    calcularDespesas(produto, cabecalho) {
        // Para Santa Catarina, a despesa de desembaraço segue fórmula específica
        // SOMA(BD23:BW23)-(BD23+BE23+BF23+BN23+BO23+BP23+BQ23+BW23)
        // Onde:
        // BD: MULTA
        // BE: TX DEF. LI
        // BF: TAXA SISCOMEX
        // BN: CAPATAZIA
        // BO: AFRMM
        // BP: ARMAZENAGEM PORTO
        // BQ: FRETE RODOVIARIO
        // BW: HONORÁRIOS NIX

        // Campos externos para Santa Catarina (definidos diretamente aqui)
        // Não usar this.getCamposExternos() - método não existe na estratégia
        const campos = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code', 'handling', 'capatazia',
            'afrmm', 'armazenagem_porto', 'frete_rodoviario', 'dif_frete_rodoviario', 'sda', 'rep_porto',
            'tx_correcao_lacre', 'li_dta_honor_nix', 'honorarios_nix'
        ];
        
        let somaTotal = 0;
        const camposExcluidos = [
            'multa',
            'tx_def_li',
            'taxa_siscomex',
            'capatazia',
            'afrmm',
            'armazenagem_porto',
            'frete_rodoviario',
            'honorarios_nix'
        ];

        // Somar todos os campos
        campos.forEach(campo => {
            const valor = produto.valoresBrutosCamposExternos?.[campo]?.[produto.rowId] || 0;
            somaTotal += valor;
        });

        // Subtrair os campos excluídos
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
     * Calcula o custo final para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} totais - Totais calculados
     * @returns {Object} - Objeto com custos finais
     */
    calcularCustoFinal(produto, totais) {
        // Para Santa Catarina: (((AZ23+BX23+BY23+BZ23)-AR23)/F23)
        // Onde:
        // AZ = VLR TOTAL NF C/ICMS-ST
        // BX = DESP. DESEMBARAÇO
        // BY = DIF. CAMBIAL FRETE
        // BZ = DIF CAMBIAL FOB
        // AR = VLR ICMS REDUZ.
        // F = QUANTIDADE

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

    /**
     * Calcula a base de ICMS reduzido para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} impostos - Impostos calculados
     * @param {number} despesas - Despesas calculadas
     * @returns {number} - Base de ICMS reduzido
     */
    calcularBcIcmsReduzido(produto, impostos, despesas) {
        // Para Santa Catarina: =((W19+AE19+AG19+AI19+AJ19+AK19)/(1-AB19))*(SE(AD19=0;1;AD19))
        // Onde:
        // W = vlr aduaneiro
        // AE = vlr II
        // AG = vlr IPI
        // AI = vlr pis
        // AJ = vlr cofins
        // AK = desp aduaneira
        // AB = icms_reduzido_percent
        // AD = reducao (é porcentagem)

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

    /**
     * Retorna as colunas visíveis para Santa Catarina
     * @returns {Array<string>} - Array com nomes das colunas
     */
    getColunasVisiveis() {
        return [
            'multa_complem',
            'dif_impostos',
            'armazenagem_porto',
            'frete_rodoviario',
            'dif_frete_rodoviario',
            'rep_porto'
        ];
    }

    /**
     * Retorna os campos do cabeçalho para Santa Catarina
     * @returns {Array<string>} - Array com nomes dos campos
     */
    getCamposCabecalho() {
        return [
            'outras_taxas_agente',
            'liberacao_bl',
            'desconsolidacao',
            'isps_code',
            'handling',
            'capatazia',
            'afrmm',
            'armazenagem_porto',
            'frete_rodoviario',
            'dif_frete_rodoviario',
            'sda',
            'rep_porto',
            'tx_correcao_lacre',
            'li_dta_honor_nix',
            'honorarios_nix'
        ];
    }

    /**
     * Calcula o valor CFR para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} moedas - Objeto com cotações de moedas
     * @returns {number} - Valor CFR
     */
    calcularCFR(produto, moedas) {
        // Para Santa Catarina: FOB Total + Frete Int USD + Service Charges USD + Acréscimo Frete USD
        const fobTotal = produto.fobTotal || 0;
        const freteIntUsd = produto.freteIntUsd || 0;
        const serviceChargesUsd = produto.serviceChargesUsd || 0;
        const acrescimoFreteUsd = produto.acrescimoFreteUsd || 0;

        return fobTotal + freteIntUsd + serviceChargesUsd + acrescimoFreteUsd;
    }

    /**
     * Calcula o valor aduaneiro para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} moedas - Objeto com cotações de moedas
     * @returns {number} - Valor aduaneiro
     */
    calcularValorAduaneiro(produto, moedas) {
        // Para Santa Catarina: VLR CFR Total + Seguro Int USD + THC USD
        const vlrCrfTotal = produto.vlrCrfTotal || 0;
        const seguroIntUsd = produto.seguroIntUsd || 0;
        const thcUsd = produto.thcUsd || 0;

        return vlrCrfTotal + seguroIntUsd + thcUsd;
    }

    /**
     * Calcula a base de ICMS sem redução para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} impostos - Impostos calculados
     * @param {number} despesas - Despesas calculadas
     * @returns {number} - Base de ICMS sem redução
     */
    calcularBcIcmsSemReducao(produto, impostos, despesas) {
        // Implementação padrão
        const vlrAduaneiro = produto.vlrAduaneiro || 0;
        const vlrII = produto.vlrII || 0;
        const vlrIPI = produto.vlrIPI || 0;
        const vlrPIS = produto.vlrPIS || 0;
        const vlrCOFINS = produto.vlrCOFINS || 0;

        return vlrAduaneiro + vlrII + vlrIPI + vlrPIS + vlrCOFINS + despesas;
    }

    /**
     * Calcula a despesa de desembaraço para Santa Catarina
     * @param {Object} produto - Dados do produto
     * @param {Object} cabecalho - Valores dos campos do cabeçalho
     * @returns {number} - Despesa de desembaraço
     */
    calcularDespesaDesembaraco(produto, cabecalho) {
        const despesas = this.calcularDespesas(produto, cabecalho);
        return despesas.despesaDesembaraco || 0;
    }
}
