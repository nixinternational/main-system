# Exemplo de Integração dos Módulos Refatorados

Este documento mostra como integrar os novos módulos no código Blade existente.

## 1. Incluir os scripts no Blade

No arquivo `form-maritimo.blade.php`, adicione antes do script principal:

```html
<!-- Módulos refatorados -->
<script type="module">
    import { init, store, eventBus, getFactory, MoneyUtils, TabelaProdutos } from '{{ asset('js/processo-maritimo/index.js') }}';
    
    // Inicializar o sistema
    const sistema = init({
        nacionalizacao: '{{ $processo->nacionalizacao ?? 'santa_catarina' }}',
        moedas: { /* cotações */ }
    });
    
    // Criar instância da tabela
    const tabelaProdutos = new TabelaProdutos(store);
    
    // Expor para uso global (compatibilidade com código existente)
    window.ProcessoMaritimo = {
        store,
        eventBus,
        tabelaProdutos,
        MoneyUtils,
        getFactory: () => getFactory(store, eventBus)
    };
</script>
```

## 2. Migração gradual de funções

### Exemplo: Substituir função calcularImpostos

**ANTES:**
```javascript
function calcularImpostos(rowId, base) {
    return {
        ii: $(`#ii_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#ii_percent-${rowId}`).val()) : 0,
        // ...
    };
}
```

**DEPOIS:**
```javascript
import { CalculadoraImpostos } from '{{ asset('js/processo-maritimo/index.js') }}';

const calculadoraImpostos = new CalculadoraImpostos();

function calcularImpostos(rowId, base) {
    const getInputValue = (selector) => $(selector).val();
    return calculadoraImpostos.calcularAliquotas(rowId, getInputValue);
}
```

## 3. Usar estratégias de nacionalização

```javascript
import { getFactory } from '{{ asset('js/processo-maritimo/index.js') }}';

const factory = getFactory(store, eventBus);
const estrategia = factory.get(store.getState().nacionalizacao);

// Calcular despesas usando a estratégia
const despesas = estrategia.calcularDespesas(produto, cabecalho);

// Calcular custo final
const custoFinal = estrategia.calcularCustoFinal(produto, totais);
```

## 4. Atualizar valores na tabela

**ANTES:**
```javascript
$(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(valor));
$(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(valor * dolar));
```

**DEPOIS:**
```javascript
tabelaProdutos.atualizarLinha(rowId, {
    fob_total_usd: valor,
    fob_total_brl: valor * dolar
});
```

## 5. Usar EventBus para comunicação

```javascript
// Registrar listener
eventBus.on('produto:atualizado', (dados) => {
    console.log('Produto atualizado:', dados);
}, { debounce: 300 });

// Emitir evento
eventBus.emit('produto:atualizado', { rowId: 1, dados: {...} });
```

## 6. Usar Store para estado

```javascript
// Atualizar estado
store.updateProduto(rowId, {
    fobTotal: 1000,
    quantidade: 10
});

// Ler estado
const estado = store.getState();
const produto = estado.produtos.find(p => p.rowId === rowId);

// Observar mudanças
store.subscribe((previousState, currentState) => {
    if (previousState.produtos !== currentState.produtos) {
        console.log('Produtos atualizados');
    }
});
```

## Notas Importantes

1. **Migração gradual**: Não é necessário migrar tudo de uma vez. Os módulos podem coexistir com o código antigo.

2. **Compatibilidade jQuery**: Os módulos foram projetados para funcionar com jQuery existente.

3. **Valores brutos**: A Store mantém `valoresBrutosPorLinha` e `valoresBrutosCamposExternos` para precisão máxima.

4. **Performance**: Use `atualizarLinhasBatch` para atualizar múltiplas linhas de uma vez.
