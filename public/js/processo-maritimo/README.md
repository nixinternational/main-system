# Sistema de Processos Marítimos - Arquitetura Refatorada

## Visão Geral

Este diretório contém a arquitetura refatorada do sistema de processos marítimos, transformando um arquivo Blade monolítico de 5590 linhas em uma estrutura modular, testável e manutenível.

## Estrutura de Diretórios

```
processo-maritimo/
├── core/                    # Módulos centrais
│   ├── Store.js            # Gerenciamento de estado
│   ├── EventBus.js         # Sistema de eventos
│   ├── Config.js           # Constantes e configurações
│   ├── ContainerDependencias.js  # Injeção de dependências
│   └── CalculoCache.js     # Cache de cálculos
├── services/               # Serviços de negócio
│   ├── CalculadoraImpostos.js
│   ├── CalculadoraFrete.js
│   ├── CalculadoraMoedas.js
│   ├── DistribuidorValores.js
│   └── ProcessoCalculator.js  # Orquestrador principal
├── strategies/             # Padrão Strategy para nacionalizações
│   ├── BaseStrategy.js
│   ├── NacionalizacaoFactory.js
│   ├── SantaCatarinaStrategy.js
│   ├── SantosStrategy.js
│   ├── AnapolisStrategy.js
│   └── MatoGrossoStrategy.js
├── utils/                  # Utilitários
│   ├── MoneyUtils.js
│   ├── DOMUtils.js
│   ├── Validador.js
│   ├── normalizeNumericValue.js
│   └── truncateNumber.js
├── components/             # Componentes UI
│   └── TabelaProdutos.js
├── tests/                  # Testes unitários
│   ├── CalculadoraImpostos.test.js
│   └── MoneyUtils.test.js
├── index.js                # Ponto de entrada
├── README.md
└── EXEMPLO_INTEGRACAO.md
```

## Estatísticas

- **Total de arquivos JavaScript**: 25
- **Total de linhas de código**: ~2956
- **Estratégias implementadas**: 4/4 (100%)
- **Serviços criados**: 5/5 (100%)
- **Testes unitários**: Estrutura básica criada

## Uso Básico

### Inicialização

O sistema é inicializado automaticamente no arquivo Blade `form-maritimo.blade.php`:

```javascript
// O sistema já está disponível globalmente via window.ProcessoMaritimo
const { store, eventBus, tabelaProdutos, processoCalculator } = window.ProcessoMaritimo;
```

### Exemplo: Usar Store

```javascript
// Atualizar estado
store.updateProduto(1, {
    fobTotal: 1000,
    quantidade: 10
});

// Ler estado
const estado = store.getState();
const produto = estado.produtos.find(p => p.rowId === 1);

// Observar mudanças
store.subscribe((previousState, currentState) => {
    console.log('Estado atualizado');
});
```

### Exemplo: Usar EventBus

```javascript
// Registrar listener
eventBus.on('produto:atualizado', (dados) => {
    console.log('Produto atualizado:', dados);
}, { debounce: 300 });

// Emitir evento
eventBus.emit('produto:atualizado', { rowId: 1, dados: {...} });
```

### Exemplo: Usar Estratégias

```javascript
const factory = window.ProcessoMaritimo.factory;
const estrategia = factory.get(store.getState().nacionalizacao);

// Calcular despesas usando a estratégia
const despesas = estrategia.calcularDespesas(produto, cabecalho);

// Calcular custo final
const custoFinal = estrategia.calcularCustoFinal(produto, totais);
```

### Exemplo: Usar ProcessoCalculator

```javascript
const calculator = window.ProcessoMaritimo.processoCalculator;

// Recalcular toda a tabela
const resultado = calculator.recalcularTodaTabela(
    produtos,
    cabecalho,
    moedas,
    (selector) => $(selector).val() // Função para obter valores de input
);
```

### Exemplo: Atualizar Tabela

```javascript
const tabela = window.ProcessoMaritimo.tabelaProdutos;

// Atualizar uma linha
tabela.atualizarLinha(1, {
    fob_total_usd: 1000,
    fob_total_brl: 5000
});

// Atualizar múltiplas linhas em batch
tabela.atualizarLinhasBatch({
    1: { fob_total_usd: 1000 },
    2: { fob_total_usd: 2000 }
});
```

## Migração Gradual

A arquitetura foi projetada para permitir migração gradual:

1. **Compatibilidade**: O código antigo continua funcionando
2. **Coexistência**: Novos módulos podem ser usados junto com código antigo
3. **Substituição incremental**: Funções podem ser substituídas uma de cada vez

### Exemplo de Migração

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

## Testes

Execute os testes básicos:

```javascript
// No console do navegador
import { CalculadoraImpostosTest } from './tests/CalculadoraImpostos.test.js';
const test = new CalculadoraImpostosTest();
const resultado = test.executarTodos();
console.log(resultado);
```

## Cache de Cálculos

O sistema inclui cache automático para evitar recálculos desnecessários:

```javascript
const cache = window.ProcessoMaritimo.calculoCache;

// Invalidar cache quando um valor base mudar
cache.invalidarPorDependencia('fob_total');

// Ver estatísticas
const stats = cache.getEstatisticas();
console.log(stats);
```

## Adicionar Nova Nacionalização

Para adicionar uma nova nacionalização:

1. Criar nova classe em `strategies/` estendendo `BaseStrategy`
2. Implementar todos os métodos abstratos
3. Registrar na `NacionalizacaoFactory`

Exemplo:

```javascript
import { BaseStrategy } from './BaseStrategy.js';

export class NovaNacionalizacaoStrategy extends BaseStrategy {
    calcularImpostos(produto, moedas) {
        // Implementação específica
    }
    
    // ... outros métodos
}

// Registrar na Factory
factory.registerStrategy('nova_nacionalizacao', NovaNacionalizacaoStrategy);
```

## Benefícios da Refatoração

1. **Manutenibilidade**: Código organizado em módulos com responsabilidades claras
2. **Testabilidade**: Funções puras e isoladas, fáceis de testar
3. **Extensibilidade**: Adicionar nova nacionalização leva horas, não semanas
4. **Performance**: Cache de cálculos e batch updates
5. **Legibilidade**: Funções pequenas (< 50 linhas) e bem documentadas

## Próximos Passos

1. Expandir cobertura de testes
2. Migrar mais funções do código antigo
3. Implementar virtualização de tabela para grandes volumes
4. Adicionar Web Workers para cálculos pesados
5. Criar documentação de API completa

## Suporte

Para dúvidas ou problemas, consulte:
- `EXEMPLO_INTEGRACAO.md` - Exemplos de integração
- Código fonte com comentários JSDoc
- Testes unitários como exemplos de uso
