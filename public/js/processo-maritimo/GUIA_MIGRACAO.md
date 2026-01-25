# Guia de Migração - Código Antigo para Nova Arquitetura

Este guia ajuda a migrar código do sistema antigo para a nova arquitetura modular.

## Mapeamento de Funções

### Funções de Cálculo

| Função Antiga | Nova Arquitetura | Status |
|--------------|------------------|--------|
| `calcularImpostos(rowId, base)` | `legacyAdapter.calcularImpostos(rowId, base)` | ✅ Migrado |
| `calcularDespesas(rowId, ...)` | `legacyAdapter.calcularDespesas(rowId, ...)` | ✅ Migrado |
| `calcularBcIcmsSemReducao(...)` | `legacyAdapter.calcularBcIcmsSemReducao(...)` | ✅ Migrado |
| `calcularBcIcmsReduzido(...)` | `legacyAdapter.calcularBcIcmsReduzido(...)` | ✅ Migrado |
| `calcularTotais(...)` | `legacyAdapter.calcularTotais(...)` | ✅ Migrado |
| `recalcularTodaTabela()` | `recalcularAdapter.recalcular()` | ✅ Migrado (híbrido) |

### Funções Helper

| Função Antiga | Nova Arquitetura | Status |
|--------------|------------------|--------|
| `getNacionalizacaoAtual()` | `legacyAdapter.getNacionalizacaoAtual()` | ✅ Migrado |
| `getCotacaoesProcesso()` | `legacyAdapter.getCotacaoesProcesso()` | ✅ Migrado |
| `obterValoresBase(rowId)` | `legacyAdapter.obterValoresBase(rowId)` | ✅ Migrado |
| `calcularPesoTotal()` | `legacyAdapter.calcularPesoTotal()` | ✅ Migrado |
| `calcularTaxaSiscomex()` | `legacyAdapter.calcularTaxaSiscomex()` | ✅ Migrado |
| `calcularSeguro(...)` | `legacyAdapter.calcularSeguro(...)` | ✅ Migrado |
| `calcularAcrescimoFrete(...)` | `legacyAdapter.calcularAcrescimoFrete(...)` | ✅ Migrado |

### Variáveis Globais

| Variável Antiga | Nova Arquitetura | Status |
|----------------|------------------|--------|
| `window.valoresBrutosPorLinha` | `store.getState().valoresBrutosPorLinha` | ✅ Sincronizado |
| `window.valoresBrutosCamposExternos` | `store.getState().valoresBrutosCamposExternos` | ✅ Sincronizado |
| `MoneyUtils` | `window.ProcessoMaritimo.MoneyUtils` | ✅ Disponível |

## Exemplos de Migração

### Exemplo 1: Substituir calcularImpostos

**ANTES:**
```javascript
const impostos = calcularImpostos(rowId, vlrAduaneiroBrl);
```

**DEPOIS:**
```javascript
// Automaticamente usa nova arquitetura se disponível
const impostos = calcularImpostos(rowId, vlrAduaneiroBrl);
// Ou diretamente:
const impostos = window.ProcessoMaritimo.legacyAdapter.calcularImpostos(rowId, vlrAduaneiroBrl);
```

### Exemplo 2: Substituir atualização de campos

**ANTES:**
```javascript
$(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotal, 7));
$(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotal * dolar, 7));
```

**DEPOIS:**
```javascript
window.ProcessoMaritimo.tabelaProdutos.atualizarLinha(rowId, {
    fob_total_usd: fobTotal,
    fob_total_brl: fobTotal * dolar
});
```

### Exemplo 3: Usar estratégias de nacionalização

**ANTES:**
```javascript
if (nacionalizacao === 'santa_catarina') {
    // lógica específica
} else if (nacionalizacao === 'mato_grosso') {
    // outra lógica
}
```

**DEPOIS:**
```javascript
const estrategia = window.ProcessoMaritimo.factory.get(nacionalizacao);
const despesas = estrategia.calcularDespesas(produto, cabecalho);
```

### Exemplo 4: Usar Store para estado

**ANTES:**
```javascript
window.valoresBrutosPorLinha[rowId] = {
    fobTotal: 1000,
    quantidade: 10
};
```

**DEPOIS:**
```javascript
// Opção 1: Via Store (recomendado)
window.ProcessoMaritimo.store.updateValoresBrutosPorLinha(rowId, {
    fobTotal: 1000,
    quantidade: 10
});

// Opção 2: Via window (ainda funciona, sincronizado automaticamente)
window.valoresBrutosPorLinha[rowId] = {
    fobTotal: 1000,
    quantidade: 10
};
```

## Checklist de Migração

### Fase 1: Preparação
- [x] Instalar módulos JavaScript
- [x] Verificar que `window.ProcessoMaritimo` está disponível
- [x] Testar funções wrapper básicas

### Fase 2: Migração de Funções
- [x] Substituir `calcularImpostos()`
- [x] Substituir `calcularDespesas()`
- [x] Substituir `calcularBcIcmsSemReducao()`
- [x] Substituir `calcularBcIcmsReduzido()`
- [x] Substituir `calcularTotais()`
- [ ] Substituir outras funções específicas

### Fase 3: Migração de DOM
- [ ] Identificar todas as chamadas `$('#campo-${rowId}').val(...)`
- [ ] Substituir por `tabelaProdutos.atualizarLinha()`
- [ ] Agrupar atualizações em batch quando possível

### Fase 4: Migração de Estado
- [x] Sincronizar `window.valoresBrutosPorLinha`
- [x] Sincronizar `window.valoresBrutosCamposExternos`
- [ ] Migrar referências diretas para Store

### Fase 5: Otimizações
- [ ] Ativar virtualização se necessário
- [ ] Usar cache de cálculos
- [ ] Aplicar debouncing em eventos

## Troubleshooting

### Problema: Função não encontrada

**Solução:** Verificar se `window.ProcessoMaritimo` está inicializado:
```javascript
if (window.ProcessoMaritimo) {
    // Usar nova arquitetura
} else {
    // Fallback para código antigo
}
```

### Problema: Valores diferentes entre antigo e novo

**Solução:** 
1. Verificar se está usando valores brutos
2. Comparar fórmulas nas estratégias
3. Verificar precisão de casas decimais

### Problema: Performance piorou

**Solução:**
1. Ativar virtualização: `tabelaVirtualizada.init()`
2. Usar cache: `calculoCache.obter(chave)`
3. Agrupar atualizações DOM em batch

### Problema: Erros de importação

**Solução:** Verificar que os arquivos estão sendo servidos corretamente:
```javascript
// Verificar no console do navegador
console.log(window.ProcessoMaritimo);
```

## Próximos Passos

1. Migrar funções restantes gradualmente
2. Remover código antigo após validação
3. Expandir testes
4. Documentar casos de uso específicos
