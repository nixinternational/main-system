/**
 * Testes unitários para CalculadoraImpostos
 * Estrutura básica para testes - pode ser expandida com framework de testes
 */

import { CalculadoraImpostos } from '../services/CalculadoraImpostos.js';

export class CalculadoraImpostosTest {
    constructor() {
        this.calculadora = new CalculadoraImpostos();
        this.tests = [];
    }

    /**
     * Testa cálculo de alíquotas
     */
    testCalcularAliquotas() {
        const getInputValue = (selector) => {
            const valores = {
                '#ii_percent-1': '10%',
                '#ipi_percent-1': '5%',
                '#pis_percent-1': '1.65%',
                '#cofins_percent-1': '7.6%',
                '#icms_percent-1': '18%'
            };
            return valores[selector] || '';
        };

        const resultado = this.calculadora.calcularAliquotas(1, getInputValue);

        const esperado = {
            ii: 0.10,
            ipi: 0.05,
            pis: 0.0165,
            cofins: 0.076,
            icms: 0.18
        };

        const passou = JSON.stringify(resultado) === JSON.stringify(esperado);
        this.tests.push({
            nome: 'testCalcularAliquotas',
            passou,
            resultado,
            esperado
        });

        return passou;
    }

    /**
     * Testa cálculo de valores de impostos
     */
    testCalcularValores() {
        const base = 1000;
        const aliquotas = {
            ii: 0.10,
            ipi: 0.05,
            pis: 0.0165,
            cofins: 0.076,
            icms: 0.18
        };
        const quantidade = 10;

        const resultado = this.calculadora.calcularValores(base, aliquotas, quantidade);

        const esperado = {
            vlrII: 100,
            bcIpi: 1100,
            vlrIpi: 55,
            bcPisCofins: 1000,
            vlrPis: 16.5,
            vlrCofins: 76,
            vlrTotalProdutoNf: 1100,
            vlrUnitProdutNf: 110
        };

        const passou = Math.abs(resultado.vlrII - esperado.vlrII) < 0.01 &&
                      Math.abs(resultado.vlrIpi - esperado.vlrIpi) < 0.01;

        this.tests.push({
            nome: 'testCalcularValores',
            passou,
            resultado,
            esperado
        });

        return passou;
    }

    /**
     * Executa todos os testes
     */
    executarTodos() {
        this.testCalcularAliquotas();
        this.testCalcularValores();

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

// Executar testes se rodado diretamente
if (typeof window !== 'undefined') {
    window.CalculadoraImpostosTest = CalculadoraImpostosTest;
}
