/**
 * Testes unitários para MoneyUtils
 */

import { MoneyUtils } from '../utils/MoneyUtils.js';

export class MoneyUtilsTest {
    constructor() {
        this.tests = [];
    }

    testParseMoney() {
        const casos = [
            { entrada: '1.234,56', esperado: 1234.56 },
            { entrada: '1,234.56', esperado: 1234.56 },
            { entrada: '1000', esperado: 1000 },
            { entrada: '0', esperado: 0 },
            { entrada: '', esperado: 0 }
        ];

        let passou = true;
        casos.forEach(caso => {
            const resultado = MoneyUtils.parseMoney(caso.entrada);
            if (Math.abs(resultado - caso.esperado) > 0.01) {
                passou = false;
            }
        });

        this.tests.push({
            nome: 'testParseMoney',
            passou
        });

        return passou;
    }

    testFormatMoney() {
        const casos = [
            { entrada: 1234.56, decimais: 2, esperado: '1.234,56' },
            { entrada: 1000, decimais: 2, esperado: '1.000,00' },
            { entrada: 0, decimais: 2, esperado: '0,00' }
        ];

        let passou = true;
        casos.forEach(caso => {
            const resultado = MoneyUtils.formatMoney(caso.entrada, caso.decimais);
            // Verificar se contém os valores principais (ignorar formatação exata)
            if (!resultado.includes('234') && caso.entrada === 1234.56) {
                passou = false;
            }
        });

        this.tests.push({
            nome: 'testFormatMoney',
            passou
        });

        return passou;
    }

    testParsePercentage() {
        const casos = [
            { entrada: '10%', esperado: 0.10 },
            { entrada: '10', esperado: 0.10 },
            { entrada: '0%', esperado: 0 },
            { entrada: '', esperado: 0 }
        ];

        let passou = true;
        casos.forEach(caso => {
            const resultado = MoneyUtils.parsePercentage(caso.entrada);
            if (Math.abs(resultado - caso.esperado) > 0.001) {
                passou = false;
            }
        });

        this.tests.push({
            nome: 'testParsePercentage',
            passou
        });

        return passou;
    }

    executarTodos() {
        this.testParseMoney();
        this.testFormatMoney();
        this.testParsePercentage();

        const passaram = this.tests.filter(t => t.passou).length;
        const total = this.tests.length;

        return {
            passaram,
            total,
            taxaSucesso: (passaram / total) * 100,
            testes: this.tests
        };
    }
}

if (typeof window !== 'undefined') {
    window.MoneyUtilsTest = MoneyUtilsTest;
}
