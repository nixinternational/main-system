# Exemplos Avançados de Uso

Este documento contém exemplos avançados de uso da nova arquitetura.

## Padrões de Uso Recomendados

### 1. Observar Mudanças no Estado

```javascript
const { store } = window.ProcessoMaritimo;

// Observar mudanças em produtos
store.subscribe((previousState, currentState) => {
    if (previousState.produtos !== currentState.produtos) {
        console.log('Produtos atualizados:', currentState.produtos);
        // Atualizar UI ou fazer cálculos adicionais
    }
});

// Observar mudanças em nacionalização
store.subscribe((previousState, currentState) => {
    if (previousState.nacionalizacao !== currentState.nacionalizacao) {
        console.log('Nacionalização alterada:', currentState.nacionalizacao);
        // Recarregar colunas ou recalcular
    }
});
```

### 2. Usar EventBus para Comunicação Desacoplada

```javascript
const { eventBus } = window.ProcessoMaritimo;

// Registrar listener com debouncing
eventBus.on('produto:calculado', (dados) => {
    console.log('Produto calculado:', dados);
    // Atualizar UI específica
}, { debounce: 300 });

// Emitir evento após cálculo
eventBus.emit('produto:calculado', {
    rowId: 1,
    fobTotal: 1000,
    custoFinal: 1500
});

// Listener que executa apenas uma vez
eventBus.once('tabela:carregada', () => {
    console.log('Tabela carregada pela primeira vez');
});
```

### 3. Usar Cache de Cálculos

```javascript
const { calculoCache } = window.ProcessoMaritimo;

// Calcular com cache
function calcularComCache(fobTotal, quantidade) {
    const chave = calculoCache.gerarChave('custo_unitario', {
        fobTotal,
        quantidade
    });

    // Verificar cache
    const cached = calculoCache.obter(chave);
    if (cached !== undefined) {
        return cached;
    }

    // Calcular
    const resultado = fobTotal / quantidade;

    // Armazenar no cache
    calculoCache.armazenar(chave, resultado, ['fobTotal', 'quantidade']);

    return resultado;
}

// Invalidar cache quando fobTotal mudar
calculoCache.invalidarPorDependencia('fobTotal');
```

### 4. Atualização em Batch

```javascript
const { tabelaProdutos } = window.ProcessoMaritimo;

// Atualizar múltiplas linhas de uma vez
const updates = {
    1: {
        fob_total_usd: 1000,
        fob_total_brl: 5000,
        peso_liquido_unitario: 10.5
    },
    2: {
        fob_total_usd: 2000,
        fob_total_brl: 10000,
        peso_liquido_unitario: 21.0
    }
};

tabelaProdutos.atualizarLinhasBatch(updates);
```

### 5. Usar Estratégias Dinamicamente

```javascript
const { factory, store } = window.ProcessoMaritimo;

// Obter estratégia atual
const estrategia = factory.get(store.getState().nacionalizacao);

// Calcular usando estratégia
const produto = {
    rowId: 1,
    fobTotal: 1000,
    quantidade: 10,
    vlrAduaneiro: 1200
};

const despesas = estrategia.calcularDespesas(produto, cabecalho);
const custoFinal = estrategia.calcularCustoFinal(produto, totais);

// Verificar colunas visíveis para esta nacionalização
const colunasVisiveis = estrategia.getColunasVisiveis();
console.log('Colunas visíveis:', colunasVisiveis);
```

### 6. ProcessoCalculator Completo

```javascript
const { processoCalculator, store } = window.ProcessoMaritimo;

// Preparar dados
const produtos = [
    {
        rowId: 1,
        fobUnitario: 100,
        quantidade: 10,
        pesoTotal: 50
    }
];

const cabecalho = {
    frete_internacional: 500,
    seguro_internacional: 100
};

const moedas = {
    USD: { venda: 5.0, compra: 4.9 }
};

// Recalcular
const resultado = processoCalculator.recalcularTodaTabela(
    produtos,
    cabecalho,
    moedas,
    (selector) => $(selector).val()
);

if (resultado && !resultado.erro) {
    console.log('Produtos calculados:', resultado.produtos);
    console.log('Totais:', resultado.totais);
}
```

### 7. Virtualização de Tabela

```javascript
const { tabelaVirtualizada } = window.ProcessoMaritimo;

// Verificar se está ativa
if (tabelaVirtualizada.isActive()) {
    console.log('Virtualização ativa');
    console.log('Itens visíveis:', tabelaVirtualizada.calculateVisibleRange());
}

// Atualizar linhas visíveis após mudanças
tabelaVirtualizada.updateVisibleRows(dadosAtualizados);
```

### 8. Validação de Dados

```javascript
const { processoCalculator } = window.ProcessoMaritimo;

const produtos = [
    { rowId: 1, quantidade: 10, pesoTotal: 50, fobUnitario: 100 },
    { rowId: 2, quantidade: -5, pesoTotal: 20, fobUnitario: 200 } // Inválido
];

const validacao = processoCalculator.validarDados(produtos);

if (!validacao.valido) {
    console.error('Erros de validação:', validacao.erros);
    // Tratar erros
}
```

### 9. Distribuição de Valores do Cabeçalho

```javascript
const { processoCalculator } = window.ProcessoMaritimo;

const cabecalho = {
    frete_internacional: 1000
};

const produtos = [
    { rowId: 1, fobTotal: 500, pesoTotal: 10 },
    { rowId: 2, fobTotal: 500, pesoTotal: 10 }
];

// Distribuir por FOB
processoCalculator.distribuirValoresCabecalho(cabecalho, produtos, 'fob');

// Distribuir por peso
processoCalculator.distribuirValoresCabecalho(cabecalho, produtos, 'peso');
```

### 10. Integração com Código Antigo

```javascript
// Exemplo: Migração gradual mantendo compatibilidade

function minhaFuncaoCustomizada(rowId) {
    const { legacyAdapter, tabelaProdutos } = window.ProcessoMaritimo;

    // Usar nova arquitetura para cálculos
    const impostos = legacyAdapter.calcularImpostos(rowId, 1000);
    const despesas = legacyAdapter.calcularDespesas(rowId, 0.5, 0.1, 50, 1200);

    // Calcular algo customizado
    const resultado = impostos.ii * 1000 + despesas.total;

    // Atualizar usando nova arquitetura
    tabelaProdutos.atualizarLinha(rowId, {
        resultado_customizado: resultado
    });

    return resultado;
}
```

## Performance Tips

### 1. Agrupar Atualizações

```javascript
// ❌ Ruim: Múltiplas atualizações individuais
for (let i = 0; i < 100; i++) {
    tabelaProdutos.atualizarLinha(i, { campo: valor });
}

// ✅ Bom: Batch update
const updates = {};
for (let i = 0; i < 100; i++) {
    updates[i] = { campo: valor };
}
tabelaProdutos.atualizarLinhasBatch(updates);
```

### 2. Usar Cache

```javascript
// ❌ Ruim: Recalcular sempre
function calcularCusto(fob, quantidade) {
    return fob / quantidade;
}

// ✅ Bom: Usar cache
function calcularCusto(fob, quantidade) {
    const cache = window.ProcessoMaritimo.calculoCache;
    const chave = cache.gerarChave('custo', { fob, quantidade });
    
    let resultado = cache.obter(chave);
    if (resultado === undefined) {
        resultado = fob / quantidade;
        cache.armazenar(chave, resultado, ['fob', 'quantidade']);
    }
    
    return resultado;
}
```

### 3. Debouncing de Eventos

```javascript
// ✅ Bom: Usar EventBus com debouncing
eventBus.on('input:change', (dados) => {
    recalcularTabela();
}, { debounce: 300 });

// Em vez de:
// $(document).on('input', '.campo', recalcularTabela); // Sempre executa
```

## Casos de Uso Complexos

### Caso 1: Recalcular após mudança de nacionalização

```javascript
const { store, eventBus, factory } = window.ProcessoMaritimo;

eventBus.on('nacionalizacao:alterada', ({ nacionalizacao }) => {
    // Obter nova estratégia
    const estrategia = factory.get(nacionalizacao);
    
    // Recalcular todos os produtos
    const produtos = store.getState().produtos;
    produtos.forEach(produto => {
        const despesas = estrategia.calcularDespesas(produto, cabecalho);
        // Atualizar...
    });
});
```

### Caso 2: Sincronizar com backend

```javascript
const { store, eventBus } = window.ProcessoMaritimo;

// Observar mudanças e salvar
store.subscribe((previousState, currentState) => {
    if (previousState.produtos !== currentState.produtos) {
        // Debounce para evitar muitas requisições
        eventBus.emit('produtos:alterados', currentState.produtos, {
            debounce: 1000,
            queue: true
        });
    }
});

// Listener para salvar
eventBus.on('produtos:alterados', async (produtos) => {
    try {
        await fetch('/api/produtos', {
            method: 'PUT',
            body: JSON.stringify(produtos)
        });
    } catch (error) {
        console.error('Erro ao salvar:', error);
    }
});
```

### Caso 3: Validação em Tempo Real

```javascript
const { Validador } = window.ProcessoMaritimo;

// Validar antes de calcular
function calcularComValidacao(rowId) {
    const valores = obterValoresLinha(rowId);
    
    if (!Validador.validarQuantidade(valores.quantidade)) {
        alert('Quantidade inválida');
        return;
    }
    
    if (!Validador.validarPeso(valores.pesoTotal)) {
        alert('Peso inválido');
        return;
    }
    
    // Prosseguir com cálculo
    return calcular(rowId);
}
```
