import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para distribuição proporcional de valores do cabeçalho
 */
export class DistribuidorValores {
    /**
     * Distribui um valor do cabeçalho proporcionalmente ao FOB de cada linha
     * @param {number} valorCampo - Valor total do campo do cabeçalho
     * @param {Array<Object>} linhas - Array com dados das linhas (fobTotal, rowId)
     * @param {Object} valoresBrutosCamposExternos - Objeto para armazenar valores brutos
     * @param {string} campo - Nome do campo sendo distribuído
     * @returns {Object} - Objeto com valores distribuídos por linha
     */
    distribuirPorFatorFOB(valorCampo, linhas, valoresBrutosCamposExternos, campo) {
        const resultado = {};
        const fobTotalGeral = linhas.reduce((sum, linha) => sum + (linha.fobTotal || 0), 0);

        // IMPORTANTE: Armazenar valor total original para uso nos totalizadores
        if (!valoresBrutosCamposExternos[campo]) {
            valoresBrutosCamposExternos[campo] = [];
        }
        if (!valoresBrutosCamposExternos[campo]._totalOriginal) {
            valoresBrutosCamposExternos[campo]._totalOriginal = valorCampo;
            // #region agent log
            fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'DistribuidorValores.js:24',message:'Valor original armazenado (FOB)',data:{campo:campo,valorOriginal:valorCampo},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
            // #endregion
        }

        if (fobTotalGeral === 0 || valorCampo === 0) {
            linhas.forEach(linha => {
                resultado[linha.rowId] = 0;
                valoresBrutosCamposExternos[campo][linha.rowId] = 0;
            });
            return resultado;
        }

        let somaDistribuida = 0;
        const ultimaLinha = linhas[linhas.length - 1];

        // Distribuir para todas as linhas exceto a última
        for (let i = 0; i < linhas.length - 1; i++) {
            const linha = linhas[i];
            const fatorVlrFob = linha.fobTotal / fobTotalGeral;
            const valorCalculado = valorCampo * fatorVlrFob;
            const valorArredondado = Math.floor(valorCalculado * 100) / 100;
            
            resultado[linha.rowId] = valorArredondado;
            somaDistribuida += valorArredondado;
            valoresBrutosCamposExternos[campo][linha.rowId] = valorArredondado;
        }

        // Última linha recebe a diferença para garantir que a soma seja exata
        const valorUltimaLinha = valorCampo - somaDistribuida;
        resultado[ultimaLinha.rowId] = valorUltimaLinha;
        valoresBrutosCamposExternos[campo][ultimaLinha.rowId] = valorUltimaLinha;

        return resultado;
    }

    /**
     * Distribui um valor do cabeçalho por peso
     * @param {number} valorCampo - Valor total do campo do cabeçalho
     * @param {Array<Object>} linhas - Array com dados das linhas (pesoTotal, rowId)
     * @param {Object} valoresBrutosCamposExternos - Objeto para armazenar valores brutos
     * @param {string} campo - Nome do campo sendo distribuído
     * @returns {Object} - Objeto com valores distribuídos por linha
     */
    distribuirPorPeso(valorCampo, linhas, valoresBrutosCamposExternos, campo) {
        const resultado = {};
        const pesoTotalGeral = linhas.reduce((sum, linha) => sum + (linha.pesoTotal || 0), 0);

        // IMPORTANTE: Armazenar valor total original para uso nos totalizadores
        if (!valoresBrutosCamposExternos[campo]) {
            valoresBrutosCamposExternos[campo] = [];
        }
        if (!valoresBrutosCamposExternos[campo]._totalOriginal) {
            valoresBrutosCamposExternos[campo]._totalOriginal = valorCampo;
            // #region agent log
            fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'DistribuidorValores.js:82',message:'Valor original armazenado (Peso)',data:{campo:campo,valorOriginal:valorCampo},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
            // #endregion
        }

        if (pesoTotalGeral === 0 || valorCampo === 0) {
            linhas.forEach(linha => {
                resultado[linha.rowId] = 0;
                valoresBrutosCamposExternos[campo][linha.rowId] = 0;
            });
            return resultado;
        }

        let somaDistribuida = 0;
        const ultimaLinha = linhas[linhas.length - 1];

        // Distribuir para todas as linhas exceto a última
        for (let i = 0; i < linhas.length - 1; i++) {
            const linha = linhas[i];
            const fatorPeso = linha.pesoTotal / pesoTotalGeral;
            const valorCalculado = valorCampo * fatorPeso;
            const valorArredondado = Math.floor(valorCalculado * 100) / 100;
            
            resultado[linha.rowId] = valorArredondado;
            somaDistribuida += valorArredondado;
            valoresBrutosCamposExternos[campo][linha.rowId] = valorArredondado;
        }

        // Última linha recebe a diferença
        const valorUltimaLinha = valorCampo - somaDistribuida;
        resultado[ultimaLinha.rowId] = valorUltimaLinha;
        valoresBrutosCamposExternos[campo][ultimaLinha.rowId] = valorUltimaLinha;

        return resultado;
    }
}
