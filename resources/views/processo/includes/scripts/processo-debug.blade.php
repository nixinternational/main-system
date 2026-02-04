{{-- Funções de Debug e Formatação para o Processo Marítimo --}}
{{-- Este arquivo contém todas as funções de debug, formatação e utilitários numéricos --}}

        function resetDebugStore() {
            debugStore = {};
            debugGlobals = {};
        }

        function setDebugGlobals(payload) {
            debugGlobals = {
                ...(payload || {})
            };
        }

        function addDebugEntry(rowId, payload) {
            debugStore[rowId] = {
                ...(debugStore[rowId] || {}),
                ...payload,
                atualizadoEm: new Date().toLocaleString('pt-BR')
            };
        }

        function getDebugFobData() {
            if (!debugStore || Object.keys(debugStore).length === 0) {
                return [];
            }

            const dados = [];
            Object.entries(debugStore).forEach(([rowId, dadosLinha]) => {
                const elementoLinha = document.getElementById(`row-${rowId}`);
                if (!elementoLinha) {
                    return;
                }
                const fobTotal = toNumber(dadosLinha?.fobTotal);
                if (!isNaN(fobTotal)) {
                    dados.push({
                        rowId,
                        fobTotal
                    });
                }
            });
            return dados;
        }

        function normalizeNumericValue(value) {
            if (value === null || value === undefined || value === '') return 0;
            if (typeof value === 'number') return value;
            if (typeof value === 'string') {
                let normalized = value.trim()
                    .replace(/\s/g, '')
                    .replace(/\./g, '')
                    .replace(',', '.')
                    .replace(/[^\d.-]/g, '');
                const parsed = parseFloat(normalized);
                if (!isNaN(parsed)) {
                    return parsed;
                }
            }
            return Number(value) || 0;
        }

        function truncateNumber(value, decimals = 2) {
            // Se for string, trabalhar diretamente com ela para evitar problemas de precisão
            if (typeof value === 'string') {
                // Normalizar: remover espaços
                let str = value.trim().replace(/\s/g, '');
                if (!str || str === '') return 0;
                
                // Encontrar separador decimal (última vírgula ou ponto)
                let lastComma = str.lastIndexOf(',');
                let lastDot = str.lastIndexOf('.');
                let decimalPos = Math.max(lastComma, lastDot);
                
                if (decimalPos >= 0) {
                    // Tem parte decimal
                    let integerPart = str.substring(0, decimalPos).replace(/\./g, '').replace(/,/g, '');
                    let decimalPart = str.substring(decimalPos + 1).replace(/,/g, '').replace(/\./g, '');
                    
                    // Validar que são apenas dígitos
                    if (!/^\d*$/.test(integerPart) || !/^\d*$/.test(decimalPart)) {
                        // Se não for válido, tentar normalizar como número
                        const num = normalizeNumericValue(value);
                        if (!isFinite(num)) return 0;
                        let strFromNum = num.toFixed(Math.max(decimals, 15));
                        return truncateNumber(strFromNum, decimals);
                    }
                    
                    // Truncar parte decimal se necessário
                    if (decimalPart.length > decimals) {
                        decimalPart = decimalPart.substring(0, decimals);
                    }
                    
                    // Reconstruir: inteiro + '.' + decimal (preenchido com zeros se necessário)
                    let resultStr = integerPart + '.' + decimalPart.padEnd(decimals, '0');
                    return parseFloat(resultStr);
                } else {
                    // Sem parte decimal
                    let integerPart = str.replace(/\./g, '').replace(/,/g, '');
                    if (!/^\d*$/.test(integerPart)) {
                        const num = normalizeNumericValue(value);
                        if (!isFinite(num)) return 0;
                        let strFromNum = num.toFixed(Math.max(decimals, 15));
                        return truncateNumber(strFromNum, decimals);
                    }
                    return parseFloat(integerPart + '.' + '0'.repeat(decimals));
                }
            }
            
            // Se for número, converter para string com precisão alta
            if (typeof value === 'number') {
                if (!isFinite(value)) return 0;
                if (decimals <= 0) {
                    return value >= 0 ? Math.floor(value) : Math.ceil(value);
                }
                // Usar toFixed com precisão extra para evitar problemas
                let str = value.toFixed(Math.max(decimals, 15));
                // Agora trabalhar como string
                return truncateNumber(str, decimals);
            }
            
            return 0;
        }

        function formatTruncatedNumber(value, decimals = 2, options = {}) {
            const truncated = truncateNumber(value, decimals);
            return truncated.toLocaleString('pt-BR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
                useGrouping: options.useGrouping !== false
            });
        }

        function toNumber(value) {
            return normalizeNumericValue(value);
        }

        function formatPlainNumber(value, decimals = 4, options = {}) {
            if (value === null || value === undefined || value === '') return '-';
            const num = toNumber(value);
            if (!isFinite(num)) return '-';
            const truncated = truncateNumber(num, decimals);
            let text = truncated.toFixed(decimals);
            if (!options.keepTrailingZeros) {
                text = text.replace(/\.?0+$/, '');
            }
            text = text.replace('.', ',');
            return text || '0';
        }

        function formatRawValue(value, decimals = 10) {
            if (value === null || value === undefined || value === '') return '-';
            const num = toNumber(value);
            if (!isFinite(num)) return '-';
            const truncated = truncateNumber(num, decimals);
            let text = truncated.toFixed(decimals).replace(/\.?0+$/, '');
            return text.replace('.', ',');
        }

        function formatComponent(label, value, decimals = 10) {
            return `${label} (${formatRawValue(value, decimals)})`;
        }

        function formatCalcDetail(result, parts, decimals = 10) {
            const filteredParts = (parts || []).filter(part => part !== null && part !== undefined && part !== '');
            const expression = filteredParts.join(' ');
            const formattedResult = formatRawValue(result, decimals);
            if (!expression) {
                return formattedResult;
            }
            return `${expression} = ${formattedResult}`;
        }

        function formatDebugMoney(value, decimals = 10) {
            return formatRawValue(value, decimals);
        }

        function formatDebugPercentage(value, decimals = 6) {
            if (value === undefined || value === null) {
                return '-';
            }
            const percentValue = toNumber(value) * 100;
            return `${formatRawValue(percentValue, decimals)} %`;
        }

        function buildRow(label, value, formula, detail = '-') {
            return { label, value, formula, detail: detail || '-' };
        }

        function buildGlobalRows(globais) {
            if (!globais || Object.keys(globais).length === 0) return [];
            return [
                buildRow(
                    'FOB Total do processo (USD)',
                    formatDebugMoney(globais.fobTotalProcesso, 4),
                    'Soma de todos os FOB TOTAL USD das linhas.',
                    formatCalcDetail(globais.fobTotalProcesso, [formatComponent('Σ FOB linhas', globais.fobTotalProcesso, 4)], 4)
                ),
                buildRow(
                    'Peso Líq. Total do processo',
                    globais.pesoTotalProcesso ?? '-',
                    'Soma de todos os pesos líquidos totais.',
                    formatCalcDetail(globais.pesoTotalProcesso, [formatComponent('Σ Pesos líquidos', globais.pesoTotalProcesso, 4)], 4)
                ),
                buildRow(
                    'Cotação USD utilizada',
                    formatDebugMoney(globais.cotacaoUSD, 4),
                    'Cotação usada nos cálculos do processo.',
                    formatCalcDetail(globais.cotacaoUSD, [formatComponent('Cotação USD', globais.cotacaoUSD, 4)], 4)
                ),
                buildRow(
                    'Taxa SISCOMEX do processo (R$)',
                    formatDebugMoney(globais.taxaSiscomexProcesso, 2),
                    'Valor calculado automaticamente baseado no número de adições únicas do processo, usando faixas progressivas.',
                    (function() {

                        const valores = $('input[name^="produtos["][name$="[adicao]"]')
                            .map(function() {
                                return $(this).val();
                            })
                            .get();
                        const unicos = [...new Set(valores.filter(v => v !== ""))];
                        const quantidade = unicos.length;
                        const valorRegistroDI = 115.67;
                        
                        let detalhes = [];
                        let totalCalculado = valorRegistroDI;
                        
                        detalhes.push(`Quantidade de adições únicas: ${quantidade}`);
                        detalhes.push(`Taxa base (Registro DI): ${formatRawValue(valorRegistroDI, 2)}`);
                        
                        if (quantidade === 0) {
                            detalhes.push(`Total: ${formatRawValue(totalCalculado, 2)}`);
                        } else {

                            const faixas = [
                                { limite: 2, valor: 38.56, inicio: 0, descricao: 'Adições 1-2' },
                                { limite: 3, valor: 30.85, inicio: 2, descricao: 'Adições 3-5' },
                                { limite: 5, valor: 23.14, inicio: 5, descricao: 'Adições 6-10' },
                                { limite: 10, valor: 15.42, inicio: 10, descricao: 'Adições 11-20' },
                                { limite: 30, valor: 7.71, inicio: 20, descricao: 'Adições 21-50' },
                                { limite: Infinity, valor: 3.86, inicio: 50, descricao: 'Adições acima de 50' }
                            ];
                            

                            faixas.forEach(faixa => {
                                let adicoesNaFaixa;
                                if (faixa.limite === Infinity) {
                                    adicoesNaFaixa = Math.max(quantidade - faixa.inicio, 0);
                                } else {
                                    adicoesNaFaixa = Math.min(
                                        Math.max(quantidade - faixa.inicio, 0),
                                        faixa.limite
                                    );
                                }
                                
                                if (adicoesNaFaixa > 0) {
                                    const valorFaixa = adicoesNaFaixa * faixa.valor;
                                    totalCalculado += valorFaixa;
                                    detalhes.push(`${faixa.descricao}: ${adicoesNaFaixa} × R$ ${faixa.valor.toFixed(2)} = ${formatRawValue(valorFaixa, 2)}`);
                                }
                            });
                            
                            detalhes.push(`TOTAL: ${formatRawValue(totalCalculado, 2)}`);
                        }
                        
                        return detalhes.join(' | ');
                    })()
                ),
                buildRow(
                    'Frete total do processo (USD)',
                    formatDebugMoney(globais.freteProcessoUSD, 4),
                    'Frete informado convertido para USD.',
                    formatCalcDetail(globais.freteProcessoUSD, [formatComponent('Frete convertido', globais.freteProcessoUSD, 4)], 4)
                ),
                buildRow(
                    'Seguro total do processo (USD)',
                    formatDebugMoney(globais.seguroProcessoUSD, 4),
                    'Seguro informado convertido para USD.',
                    formatCalcDetail(globais.seguroProcessoUSD, [formatComponent('Seguro convertido', globais.seguroProcessoUSD, 4)], 4)
                ),
                buildRow(
                    'Acréscimo frete total (USD)',
                    formatDebugMoney(globais.acrescimoProcessoUSD, 4),
                    'Acréscimo informado convertido para USD.',
                    formatCalcDetail(globais.acrescimoProcessoUSD, [formatComponent('Acréscimo convertido', globais.acrescimoProcessoUSD, 4)], 4)
                ),
                buildRow(
                    'Service charges total (USD)',
                    formatDebugMoney(globais.serviceChargesProcessoUSD, 4),
                    'Service charges informados convertidos para USD.',
                    formatCalcDetail(globais.serviceChargesProcessoUSD, [formatComponent('Service charges convertidos', globais.serviceChargesProcessoUSD, 4)], 4)
                ),
            ];
        }

        function buildDebugRows(dados, globais) {
            const pesoTotalProcesso = toNumber(globais?.pesoTotalProcesso);
            const valorAduaneiroBrl = toNumber(dados.valorAduaneiroBrl);
            const quantidade = toNumber(dados.quantidade || 0) || 0;
            const fobUnitario = toNumber(dados.fobUnitario);
            const fobTotal = toNumber(dados.fobTotal);
            const fatorPeso = toNumber(dados.fatorPeso);
            const freteProcessoUSD = toNumber(globais?.freteProcessoUSD);
            const seguroProcessoUSD = toNumber(globais?.seguroProcessoUSD);
            const acrescimoProcessoUSD = toNumber(globais?.acrescimoProcessoUSD);
            const serviceChargesProcessoUSD = toNumber(globais?.serviceChargesProcessoUSD);
            const fobTotalProcesso = toNumber(globais?.fobTotalProcesso);
            const cotacaoUSD = toNumber(globais?.cotacaoUSD);
            const taxaSiscomexProcesso = toNumber(globais?.taxaSiscomexProcesso);
            const vlrII = toNumber(dados.vlrII);
            const bcIpiVal = toNumber(dados.bcIpi);
            const vlrIpi = toNumber(dados.vlrIpi);
            const bcPisCofinsVal = toNumber(dados.bcPisCofins);
            const vlrPis = toNumber(dados.vlrPis);
            const vlrCofins = toNumber(dados.vlrCofins);
            const despesaAduaneiraVal = toNumber(dados.despesaAduaneira);
            const bcIcmsSemReducaoVal = toNumber(dados.bcIcmsSemReducao);
            const vlrIcmsSemReducaoVal = toNumber(dados.vlrIcmsSemReducao);
            const bcIcmsReduzidoVal = toNumber(dados.bcIcmsReduzido);
            const vlrIcmsReduzidoVal = toNumber(dados.vlrIcmsReduzido);
            const vlrTotalProdNfVal = toNumber(dados.vlrTotalProdNf);
            const vlrTotalNfSemIcmsVal = toNumber(dados.vlrTotalNfSemIcms);
            const baseIcmsStVal = toNumber(dados.baseIcmsSt);
            const vlrIcmsStVal = toNumber(dados.valorIcmsSt);
            const icmsStPercent = toNumber(dados.icmsStPercent);
            const fatorReducaoAplicado = dados.reducao || 1;
            const fatorMva = 1 + (dados.mva || 0);
            const quantidadeSafe = quantidade > 0 ? quantidade : 1;
            const nacionalizacaoDebug = globais?.nacionalizacao || '';
            let formulaDespesa = '';
            let detailDespesaExpr = '';
            const despComp = dados.despesasComponentes || {};
            
            if (nacionalizacaoDebug === 'santos') {
                formulaDespesa = 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Honorários.';
                detailDespesaExpr = `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Honorários', despComp.honorarios_nix, 2)}`;
            } else if (nacionalizacaoDebug === 'anapolis') {
                formulaDespesa = 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Armazenagem STS + Frete STS/GYN + Honorários NIX.';
                detailDespesaExpr = `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Armazenagem STS', despComp.armazenagem_sts, 2)} + ${formatComponent('Frete STS/GYN', despComp.frete_dta_sts_ana, 2)} + ${formatComponent('Honorários NIX', despComp.honorarios_nix, 2)}`;
            } else {
                formulaDespesa = 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Armazenagem STS + Frete DTA STS/ANA + Honorários.';
                detailDespesaExpr = `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Armazenagem Porto', despComp.armazenagem_sts, 2)} + ${formatComponent('Frete DTA', despComp.frete_dta_sts_ana, 2)} + ${formatComponent('Honorários', despComp.honorarios_nix, 2)}`;
            }
            const detailDespesa = formatCalcDetail(despesaAduaneiraVal, [detailDespesaExpr], 2);
            const numeradorBcIcms = valorAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despesaAduaneiraVal;
            const fatorIcmsDivisor = 1 - toNumber(dados.aliquotaIcms);

            const rows = [
                buildRow('Produto / Descrição', dados.produto ?? '-', 'Valor informado nas colunas Produto e Descrição.', `Valor informado: ${dados.produto ?? '-'}`),
                buildRow('Quantidade', dados.quantidade ?? '-', 'Valor digitado na coluna Quantidade.', formatComponent('Quantidade', quantidade, 4)),
                buildRow('Peso Líq. Total', dados.pesoTotal ?? '-', 'Campo Peso Líquido Total da linha.', formatComponent('Peso da linha', dados.pesoTotal, 4)),
                buildRow('FOB Unit USD', formatDebugMoney(dados.fobUnitario, 4), 'Valor digitado em FOB UNIT USD.', formatComponent('FOB Unit USD', fobUnitario, 4)),
                buildRow('FOB Total USD', formatDebugMoney(dados.fobTotal, 4), 'FOB Unit USD × Quantidade.', formatCalcDetail(fobTotal, [formatComponent('FOB Unit USD', fobUnitario, 4), '×', formatComponent('Quantidade', quantidade, 4)], 4)),
                buildRow('Fator Peso', formatDebugMoney(dados.fatorPeso, 6), 'Peso Líq. Total da linha ÷ Peso Líq. Total do processo.', formatCalcDetail(fatorPeso, [formatComponent('Peso da linha', dados.pesoTotal, 4), '÷', formatComponent('Peso total processo', pesoTotalProcesso, 4)], 6)),
                buildRow('Frete USD', formatDebugMoney(dados.freteUsd, 4), 'Frete do processo (USD) × Fator Peso da linha.', formatCalcDetail(dados.freteUsd, [formatComponent('Frete processo USD', freteProcessoUSD, 4), '×', formatComponent('Fator Peso', fatorPeso, 6)], 4)),
                buildRow('Seguro USD', formatDebugMoney(dados.seguroUsd, 4), '(Seguro do processo ÷ FOB total do processo) × FOB total da linha.', formatCalcDetail(dados.seguroUsd, ['(', formatComponent('Seguro processo USD', seguroProcessoUSD, 4), '÷', formatComponent('FOB total processo', fobTotalProcesso, 4), ')', '×', formatComponent('FOB total linha', fobTotal, 4)], 4)),
                buildRow('Acréscimo Frete USD', formatDebugMoney(dados.acrescimoUsd, 4), '(Acréscimo do processo ÷ FOB total do processo) × FOB total da linha.', formatCalcDetail(dados.acrescimoUsd, ['(', formatComponent('Acréscimo processo USD', acrescimoProcessoUSD, 4), '÷', formatComponent('FOB total processo', fobTotalProcesso, 4), ')', '×', formatComponent('FOB total linha', fobTotal, 4)], 4)),
                buildRow('Service Charges USD', formatDebugMoney(dados.serviceChargesUsd, 4), 'Service charges do processo × Fator Peso da linha.', formatCalcDetail(dados.serviceChargesUsd, [formatComponent('Service charges processo USD', serviceChargesProcessoUSD, 4), '×', formatComponent('Fator Peso', fatorPeso, 6)], 4)),
                buildRow('THC (R$ → USD)', formatDebugMoney(dados.thc, 4), 'THC/Capatazia informado × Fator Peso (convertido para USD).', formatCalcDetail(dados.thc, [formatComponent('THC processo', dados.thcBaseProcesso, 4), '×', formatComponent('Fator Peso', fatorPeso, 6)], 4)),
                buildRow('VLR CRF Total', formatDebugMoney(dados.vlrCrfTotal, 4), 'FOB Total USD + Frete USD.', formatCalcDetail(dados.vlrCrfTotal, [formatComponent('FOB Total USD', fobTotal, 4), '+', formatComponent('Frete USD', dados.freteUsd, 4)], 4)),
                buildRow('Valor Aduaneiro USD', formatDebugMoney(dados.vlrAduaneiroUsd, 4), 'VLR CRF Total + Service Charges USD + Acréscimo USD + Seguro USD + THC (USD).', formatCalcDetail(dados.vlrAduaneiroUsd, [formatComponent('VLR CRF Total', dados.vlrCrfTotal, 4), '+', formatComponent('Service Charges USD', dados.serviceChargesUsd, 4), '+', formatComponent('Acréscimo USD', dados.acrescimoUsd, 4), '+', formatComponent('Seguro USD', dados.seguroUsd, 4), '+', formatComponent('THC USD', dados.thc, 4)], 4)),
                buildRow('Fator Valor FOB', formatDebugMoney(dados.fatorVlrFob, 6), 'FOB Total USD da linha ÷ FOB Total USD do processo.', formatCalcDetail(dados.fatorVlrFob, [formatComponent('FOB Total linha', fobTotal, 10), '÷', formatComponent('FOB Total processo', fobTotalProcesso, 10)], 6)),
                buildRow('Fator Taxa SISCOMEX', formatDebugMoney(dados.fatorSiscomex, 6), 'Taxa SISCOMEX do processo ÷ (FOB Total USD do processo × Cotação USD).', formatCalcDetail(dados.fatorSiscomex, [formatComponent('Taxa SISCOMEX processo', globais?.taxaSiscomexProcesso ?? 0, 10), '÷', '(', formatComponent('FOB Total processo', fobTotalProcesso, 10), '×', formatComponent('Cotação USD', cotacaoUSD, 10), ')'], 6)),
                buildRow('Taxa Siscomex (linha)', formatDebugMoney(dados.taxaSiscomexUnit, 6), 'Fator Taxa Siscomex × (FOB Total da linha × Cotação USD).', formatCalcDetail(dados.taxaSiscomexUnit, [formatComponent('Fator Taxa Siscomex', dados.fatorSiscomex, 6), '×', '(', formatComponent('FOB Total linha', fobTotal, 10), '×', formatComponent('Cotação USD', cotacaoUSD, 10), ')'], 6)),
                buildRow('Dif. Cambial Frete', formatDebugMoney(dados.diferencaCambialFrete, 4), '(Frete USD da linha × Dif. cambial frete processo) - (Frete USD × cotação).', formatCalcDetail(dados.diferencaCambialFrete, ['(', formatComponent('Frete USD linha', dados.freteUsd, 4), '×', formatComponent('Dif. cambial frete (processo)', dados.diferencaCambialFreteProcesso, 4), ')', '-', '(', formatComponent('Frete USD linha', dados.freteUsd, 4), '×', formatComponent('Cotação USD', cotacaoUSD, 4), ')'], 4)),
                buildRow('Dif. Cambial FOB', formatDebugMoney(dados.diferencaCambialFob, 4), '(Fator Valor FOB × Dif. cambial FOB processo) - (FOB Total × cotação).', formatCalcDetail(dados.diferencaCambialFob, ['(', formatComponent('Fator Valor FOB', dados.fatorVlrFob, 6), '×', formatComponent('Dif. cambial FOB (processo)', dados.diferencaCambialFobProcesso, 4), ')', '-', '(', formatComponent('FOB Total USD', fobTotal, 4), '×', formatComponent('Cotação USD', cotacaoUSD, 4), ')'], 4)),
                buildRow('Redução ICMS', formatDebugPercentage(dados.reducao, 2), 'Percentual informado em Redução na linha.', `Percentual: ${formatDebugPercentage(dados.reducao, 2)} / Fração aplicada: ${formatRawValue(fatorReducaoAplicado, 10)}`),
                buildRow('VLR II', formatDebugMoney(dados.vlrII, 2), 'Valor Aduaneiro BRL × Alíquota de II.', formatCalcDetail(vlrII, [formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '×', formatComponent('Alíquota II', dados.aliquotaIi, 4)], 2)),
                buildRow('BC IPI', formatDebugMoney(dados.bcIpi, 2), 'Valor Aduaneiro BRL + VLR II.', formatCalcDetail(bcIpiVal, [formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '+', formatComponent('VLR II', vlrII, 2)], 2)),
                buildRow('VLR IPI', formatDebugMoney(dados.vlrIpi, 2), 'BC IPI × Alíquota de IPI.', formatCalcDetail(vlrIpi, [formatComponent('BC IPI', bcIpiVal, 2), '×', formatComponent('Alíquota IPI', dados.aliquotaIpi, 4)], 2)),
                buildRow('BC PIS/COFINS', formatDebugMoney(dados.bcPisCofins, 2), 'Base igual ao Valor Aduaneiro BRL.', formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2)),
                buildRow('VLR PIS', formatDebugMoney(dados.vlrPis, 2), 'BC PIS/COFINS × Alíquota PIS.', formatCalcDetail(vlrPis, [formatComponent('BC PIS/COFINS', bcPisCofinsVal, 2), '×', formatComponent('Alíquota PIS', dados.aliquotaPis, 4)], 2)),
                buildRow('VLR COFINS', formatDebugMoney(dados.vlrCofins, 2), 'BC PIS/COFINS × Alíquota COFINS.', formatCalcDetail(vlrCofins, [formatComponent('BC PIS/COFINS', bcPisCofinsVal, 2), '×', formatComponent('Alíquota COFINS', dados.aliquotaCofins, 4)], 2)),
                buildRow('Desp. Aduaneira', formatDebugMoney(dados.despesaAduaneira, 2), `${formulaDespesa} [Nacionalização: ${(globais?.nacionalizacao || '').toUpperCase()}]`, detailDespesa),
                buildRow('Desp. Desembaraço', formatDebugMoney(dados.despesaDesembaraco ?? 0, 2), 
                    'Parte 1 (Campos Externos + Multa + Taxa DEF + Taxa SISCOMEX) - Parte 2 (Multa + Taxa DEF + Taxa SISCOMEX + Capatazia + AFRMM + Honorários).',
                    (function() {
                        if (!dados.despesaDesembaracoDetalhes) return '-';
                        const det = dados.despesaDesembaracoDetalhes;
                        const camposExternosStr = Object.entries(det.camposExternos || {})
                            .map(([campo, valor]) => formatComponent(campo, valor, 2))
                            .join(' + ');
                        const parte1Expr = camposExternosStr + 
                            (camposExternosStr ? ' + ' : '') +
                            formatComponent('Multa', det.multa, 2) + ' + ' +
                            formatComponent('Taxa DEF', det.taxaDef, 2) + ' + ' +
                            formatComponent('Taxa SISCOMEX', det.taxaSiscomex, 2);
                        const parte2Expr = formatComponent('Multa', det.multa, 2) + ' + ' +
                            formatComponent('Taxa DEF', det.taxaDef, 2) + ' + ' +
                            formatComponent('Taxa SISCOMEX', det.taxaSiscomex, 2) + ' + ' +
                            formatComponent('Capatazia', det.capatazia, 2) + ' + ' +
                            formatComponent('AFRMM', det.afrmm, 2) + ' + ' +
                            formatComponent('Honorários', det.honorariosNix, 2);
                        return `(${parte1Expr}) - (${parte2Expr}) = ${formatRawValue(dados.despesaDesembaraco ?? 0, 2)}`;
                    })()),
                buildRow('BC ICMS s/Redução', formatDebugMoney(dados.bcIcmsSemReducao, 2), '[(Base + II + IPI + PIS + COFINS + Despesas)] ÷ (1 - % ICMS).', formatCalcDetail(bcIcmsSemReducaoVal, ['(', formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '+', formatComponent('II', vlrII, 2), '+', formatComponent('IPI', vlrIpi, 2), '+', formatComponent('PIS', vlrPis, 2), '+', formatComponent('COFINS', vlrCofins, 2), '+', formatComponent('Despesas Aduaneiras', despesaAduaneiraVal, 2), ')', '÷', formatComponent('(1 - % ICMS)', fatorIcmsDivisor, 4)], 2)),
                buildRow('VLR ICMS s/Redução', formatDebugMoney(dados.vlrIcmsSemReducao, 2), 'BC ICMS s/Redução × % ICMS.', formatCalcDetail(vlrIcmsSemReducaoVal, [formatComponent('BC ICMS s/Redução', bcIcmsSemReducaoVal, 2), '×', formatComponent('% ICMS', dados.aliquotaIcms, 4)], 2)),
                buildRow('BC ICMS reduzido', formatDebugMoney(dados.bcIcmsReduzido, 2), 'Resultado de BC ICMS após aplicar o percentual de redução.', formatCalcDetail(bcIcmsReduzidoVal, [formatComponent('BC ICMS s/Redução', bcIcmsSemReducaoVal, 2), '×', formatComponent('Fator Redução', fatorReducaoAplicado, 4)], 2)),
                buildRow('VLR ICMS reduzido', formatDebugMoney(dados.vlrIcmsReduzido, 2), 'BC ICMS reduzido × % ICMS.', formatCalcDetail(vlrIcmsReduzidoVal, [formatComponent('BC ICMS reduzido', bcIcmsReduzidoVal, 2), '×', formatComponent('% ICMS', dados.aliquotaIcms, 4)], 2)),
                buildRow('VLR Unit. Prod. NF', formatDebugMoney(dados.vlrUnitProdNf, 2), 'Valor Total Produto NF ÷ Quantidade.', quantidade > 0
                    ? formatCalcDetail(dados.vlrUnitProdNf, [formatComponent('VLR Total Prod. NF', vlrTotalProdNfVal, 2), '÷', formatComponent('Quantidade', quantidade, 4)], 2)
                    : `Quantidade informada igual a 0; sistema assume 1 unidade. ${formatCalcDetail(dados.vlrUnitProdNf, [formatComponent('VLR Total Prod. NF', vlrTotalProdNfVal, 2), '÷', formatComponent('Quantidade assumida', quantidadeSafe, 4)], 2)}`),
                buildRow('VLR Total Prod. NF', formatDebugMoney(dados.vlrTotalProdNf, 2), 'Base Aduaneira BRL + VLR II.', formatCalcDetail(vlrTotalProdNfVal, [formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '+', formatComponent('VLR II', vlrII, 2)], 2)),
                buildRow('VLR Total NF s/ICMS ST', formatDebugMoney(dados.vlrTotalNfSemIcms, 2), 'VLR Total Prod. NF + IPI + PIS + COFINS + Desp. Aduaneira + VLR ICMS reduzido.', formatCalcDetail(vlrTotalNfSemIcmsVal, [formatComponent('VLR Total Prod. NF', vlrTotalProdNfVal, 2), '+', formatComponent('IPI', vlrIpi, 2), '+', formatComponent('PIS', vlrPis, 2), '+', formatComponent('COFINS', vlrCofins, 2), '+', formatComponent('Desp. Aduaneira', despesaAduaneiraVal, 2), '+', formatComponent('VLR ICMS reduzido', vlrIcmsReduzidoVal, 2)], 2)),
                buildRow('BC ICMS-ST', formatDebugMoney(dados.baseIcmsSt, 2), 'VLR Total NF s/ICMS ST × (1 + MVA).', formatCalcDetail(baseIcmsStVal, [formatComponent('VLR Total NF s/ICMS ST', vlrTotalNfSemIcmsVal, 2), '×', formatComponent('(1 + MVA)', fatorMva, 4)], 2)),
                buildRow('VLR ICMS-ST', formatDebugMoney(dados.valorIcmsSt, 2), 'Base ICMS-ST × % ICMS-ST - VLR ICMS reduzido (quando aplicável).', icmsStPercent > 0 ? formatCalcDetail(vlrIcmsStVal, ['(', formatComponent('BC ICMS-ST', baseIcmsStVal, 2), '×', formatComponent('% ICMS-ST', icmsStPercent, 4), ')', '-', formatComponent('VLR ICMS reduzido', vlrIcmsReduzidoVal, 2)], 2) : 'Percentual ICMS-ST não informado.'),
                buildRow('Custo Unit. Final', formatDebugMoney(dados.custoUnitarioFinal, 2), 
                    (globais?.nacionalizacao === 'santos')
                        ? '(Total NF c/ICMS + Desp. Desembaraço + Dif. Cambial FOB + Dif. Cambial Frete) ÷ Quantidade.'
                        : '[(Total NF c/ICMS + Desp. Desembaraço + Dif. Cambial FOB + Dif. Cambial Frete) - ICMS reduzido] ÷ Quantidade.',
                    (globais?.nacionalizacao === 'santos')
                        ? formatCalcDetail(dados.custoUnitarioFinal, ['(', formatComponent('Total NF c/ICMS', dados.vlrTotalNfComIcms ?? dados.vlrTotalNfSemIcms ?? vlrTotalNfSemIcmsVal, 2), '+', formatComponent('Desp. Desembaraço', dados.despesaDesembaraco ?? 0, 2), '+', formatComponent('Dif. Cambial FOB', dados.diferencaCambialFob, 2), '+', formatComponent('Dif. Cambial Frete', dados.diferencaCambialFrete, 2), ')', '÷', formatComponent('Quantidade', quantidadeSafe, 4)], 2)
                        : formatCalcDetail(dados.custoUnitarioFinal, ['(', formatComponent('Total NF c/ICMS', dados.vlrTotalNfComIcms ?? dados.vlrTotalNfSemIcms ?? vlrTotalNfSemIcmsVal, 2), '+', formatComponent('Desp. Desembaraço', dados.despesaDesembaraco ?? 0, 2), '+', formatComponent('Dif. Cambial FOB', dados.diferencaCambialFob, 2), '+', formatComponent('Dif. Cambial Frete', dados.diferencaCambialFrete, 2), '-', formatComponent('ICMS reduzido', vlrIcmsReduzidoVal, 2), ')', '÷', formatComponent('Quantidade', quantidadeSafe, 4)], 2)),
                buildRow('Custo Total Final', formatDebugMoney(dados.custoTotalFinal, 2), 'Custo unitário final × Quantidade.', formatCalcDetail(dados.custoTotalFinal, [formatComponent('Custo Unit. Final', dados.custoUnitarioFinal, 2), '×', formatComponent('Quantidade', quantidade, 4)], 2))
            ];
            if (globais && globais.fobTotalProcesso) {
                rows.splice(5, 0, buildRow(
                    'FOB Total do processo (USD)',
                    formatDebugMoney(globais.fobTotalProcesso, 4),
                    'Soma dos FOB TOTAL USD de todas as linhas, usada como base para rateios.',
                    formatCalcDetail(globais.fobTotalProcesso, [formatComponent('Σ FOB linhas', globais.fobTotalProcesso, 4)], 4)
                ));
            }
            return rows;
        }

        function buildSectionHtml(titulo, linhas) {
            if (!linhas || linhas.length === 0) {
                return '';
            }

            let html = `<div class="debug-section">
                <div class="debug-section-title">${titulo}</div>
                <div class="debug-grid debug-grid-header">
                    <div>Campo</div>
                    <div>Valor</div>
                    <div>Fórmula utilizada</div>
                    <div>Detalhamento</div>
                </div>`;

            linhas.forEach(linha => {
                html += `<div class="debug-grid debug-grid-row">
                    <div class="debug-cell-label">${linha.label}</div>
                    <div class="debug-cell-value">${linha.value ?? '-'}</div>
                    <div class="debug-cell-text">${linha.formula ?? '-'}</div>
                    <div class="debug-cell-text">${linha.detail || '-'}</div>
                </div>`;
            });

            html += '</div>';
            return html;
        }

        function renderDebugModal(rowId) {
            const dados = debugStore[rowId];
            const container = $('#debugLinhaConteudo');

            if (!dados) {
                container.html('<p class="text-muted mb-0">Ainda não há informações calculadas para esta linha. Clique em "Recalcular tabela" e tente novamente.</p>');
                return;
            }

            let html = '<p class="text-muted small mb-3"><i class="fas fa-info-circle mr-2"></i>Os valores abaixo exibem todas as casas decimais utilizadas nos cálculos, sem qualquer arredondamento. Na tabela principal mostramos apenas duas casas para facilitar a leitura.</p>';

            const globais = buildGlobalRows(debugGlobals);
            html += buildSectionHtml('Totais do processo', globais);

            const linhas = buildDebugRows(dados, debugGlobals);
            html += buildSectionHtml('Detalhes da linha', linhas);

            container.html(html);
        }

        $(document).on('click', '.btn-debug-linha', function() {
            const rowId = $(this).data('row');
            const numeroItem = $(`#item-${rowId}`).val();
            const label = numeroItem ? `Cálculo do item ${numeroItem}` : `Cálculo da linha #${rowId}`;
            $('#debugLinhaModalLabel').text(label);
            renderDebugModal(rowId);
            $('#debugLinhaModal').modal('show');
        });

        $(document).on('click', '.btn-close-debug', function() {
            $('#debugLinhaModal').modal('hide');
        });
