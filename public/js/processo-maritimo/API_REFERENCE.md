# API Reference - Sistema de Processos Marítimos

Referência completa da API da nova arquitetura.

## Core

### Store

**Classe:** `ProcessoStore`

**Métodos:**

#### `getState()`
Retorna o estado atual (cópia imutável).

**Retorno:** `Object` - Estado atual

#### `setState(updates)`
Atualiza o estado de forma imutável.

**Parâmetros:**
- `updates` (Object): Objeto com atualizações

**Exemplo:**
```javascript
store.setState({ nacionalizacao: 'santa_catarina' });
```

#### `updateProduto(rowId, dados)`
Atualiza um produto específico.

**Parâmetros:**
- `rowId` (string|number): ID da linha
- `dados` (Object): Dados do produto

#### `updateCabecalho(campo, valor)`
Atualiza um campo do cabeçalho.

**Parâmetros:**
- `campo` (string): Nome do campo
- `valor` (*): Valor do campo

#### `subscribe(callback)`
Inscreve um observador para mudanças no estado.

**Parâmetros:**
- `callback` (Function): Função callback `(previousState, currentState) => void`

**Retorno:** `Function` - Função para desinscrever

**Exemplo:**
```javascript
const unsubscribe = store.subscribe((prev, curr) => {
    console.log('Estado mudou');
});
// Para desinscrever:
unsubscribe();
```

### EventBus

**Classe:** `EventBus`

**Métodos:**

#### `on(event, callback, options)`
Registra um listener para um evento.

**Parâmetros:**
- `event` (string): Nome do evento
- `callback` (Function): Função callback
- `options` (Object): Opções
  - `debounce` (number): Delay em ms para debouncing
  - `priority` (number): Prioridade do listener
  - `once` (boolean): Executar apenas uma vez

**Retorno:** `Function` - Função para remover o listener

#### `once(event, callback, options)`
Registra um listener que executa apenas uma vez.

#### `emit(event, data, options)`
Emite um evento.

**Parâmetros:**
- `event` (string): Nome do evento
- `data` (*): Dados do evento
- `options` (Object): Opções
  - `immediate` (boolean): Executar imediatamente (ignorar debounce)
  - `queue` (boolean): Adicionar à fila

#### `off(event)`
Remove todos os listeners de um evento.

## Services

### ProcessoCalculator

**Classe:** `ProcessoCalculator`

**Métodos:**

#### `recalcularTodaTabela(produtos, cabecalho, moedas, getInputValue)`
Recalcula toda a tabela de produtos.

**Parâmetros:**
- `produtos` (Array<Object>): Array de produtos
- `cabecalho` (Object): Valores do cabeçalho
- `moedas` (Object): Cotações de moedas
- `getInputValue` (Function): Função para obter valores de input

**Retorno:** `Object` - Resultado completo ou `{ erro: Array<string> }` se houver erros

**Estrutura do retorno:**
```javascript
{
    produtos: [
        {
            rowId: 1,
            fobTotal: 1000,
            valoresNacionalizacao: { ... },
            impostos: { ... },
            despesas: { ... },
            custoFinal: { ... }
        }
    ],
    totais: {
        fobTotalGeral: 5000,
        pesoTotalGeral: 250,
        // ...
    },
    valoresBase: { ... }
}
```

#### `validarDados(produtos)`
Valida dados antes de calcular.

**Retorno:** `{ valido: boolean, erros: Array<string> }`

#### `calcularValoresBase(produtos)`
Calcula valores base (FOB, pesos, quantidades).

#### `distribuirValoresCabecalho(cabecalho, produtos, metodo)`
Distribui valores do cabeçalho proporcionalmente.

**Parâmetros:**
- `metodo` (string): 'fob' ou 'peso'

### CalculadoraImpostos

**Classe:** `CalculadoraImpostos`

**Métodos:**

#### `calcularAliquotas(rowId, getInputValue)`
Calcula as alíquotas de impostos.

**Parâmetros:**
- `rowId` (string|number): ID da linha
- `getInputValue` (Function): Função para obter valor de input

**Retorno:** `Object` - `{ ii, ipi, pis, cofins, icms }`

#### `calcularValores(base, aliquotas, quantidade)`
Calcula os valores dos impostos.

**Retorno:** `Object` - Valores calculados

### HelperService

**Classe:** `HelperService`

**Métodos:**

- `getNacionalizacaoAtual()` - Obtém nacionalização atual
- `getCotacaoesProcesso()` - Obtém cotações
- `obterValoresBase(rowId)` - Obtém valores base de uma linha
- `calcularPesoTotal()` - Calcula peso total
- `calcularTaxaSiscomex()` - Calcula taxa Siscomex
- `calcularSeguro(fobTotalLinha, fobTotalGeral)` - Calcula seguro proporcional
- `calcularAcrescimoFrete(...)` - Calcula acréscimo de frete

## Strategies

### BaseStrategy

**Classe:** `BaseStrategy` (abstrata)

**Métodos abstratos:**

- `calcularImpostos(produto, moedas)` - Calcula impostos
- `calcularDespesas(produto, cabecalho)` - Calcula despesas
- `calcularCustoFinal(produto, totais)` - Calcula custo final
- `calcularBcIcmsSemReducao(produto, impostos, despesas)` - Base ICMS sem redução
- `calcularBcIcmsReduzido(produto, impostos, despesas)` - Base ICMS reduzido
- `calcularCFR(produto, moedas)` - Calcula CFR
- `calcularValorAduaneiro(produto, moedas)` - Calcula valor aduaneiro
- `getColunasVisiveis()` - Retorna colunas visíveis
- `getCamposCabecalho()` - Retorna campos do cabeçalho

### NacionalizacaoFactory

**Classe:** `NacionalizacaoFactory`

**Métodos:**

#### `get(tipo)`
Obtém a estratégia para um tipo de nacionalização.

**Parâmetros:**
- `tipo` (string): Tipo de nacionalização

**Retorno:** `BaseStrategy` - Instância da estratégia

## Components

### TabelaProdutos

**Classe:** `TabelaProdutos`

**Métodos:**

#### `atualizarLinha(rowId, dados)`
Atualiza uma linha específica.

**Parâmetros:**
- `rowId` (string|number): ID da linha
- `dados` (Object): Dados a serem atualizados

#### `atualizarLinhasBatch(updates)`
Atualiza múltiplas linhas em batch.

**Parâmetros:**
- `updates` (Object): `{ rowId: { campo: valor } }`

#### `obterValor(rowId, campo)`
Obtém o valor de um campo.

**Retorno:** `number` - Valor parseado

#### `obterValoresLinha(rowId, campos)`
Obtém todos os valores de uma linha.

**Retorno:** `Object` - Objeto com valores parseados

### TabelaVirtualizada

**Classe:** `TabelaVirtualizada`

**Métodos:**

#### `init()`
Inicializa a virtualização.

#### `update()`
Atualiza a renderização baseado no scroll.

#### `isActive()`
Verifica se a virtualização está ativa.

**Retorno:** `boolean`

#### `destroy()`
Desativa virtualização.

## Utils

### MoneyUtils

**Objeto:** `MoneyUtils`

**Métodos:**

- `parseMoney(value)` - Converte string formatada para número
- `formatMoney(value, decimals)` - Formata valor monetário
- `parsePercentage(value)` - Converte porcentagem para decimal
- `formatPercentage(value, decimals)` - Formata como porcentagem
- `formatUSD(value, decimals)` - Formata em USD
- `formatMoneyExato(value)` - Formata sem truncar zeros

### Validador

**Classe:** `Validador`

**Métodos estáticos:**

- `isNumeroValido(value)` - Valida se é número válido
- `validarDiferencaCambial(valor)` - Valida diferença cambial
- `validarQuantidade(quantidade)` - Valida quantidade
- `validarPeso(peso)` - Valida peso
- `validarPorcentagem(porcentagem)` - Valida porcentagem

## Adapters

### LegacyAdapter

**Classe:** `LegacyAdapter`

Wrappers de compatibilidade para funções antigas:

- `calcularImpostos(rowId, base)`
- `calcularDespesas(rowId, ...)`
- `calcularBcIcmsSemReducao(...)`
- `calcularBcIcmsReduzido(...)`
- `calcularTotais(...)`
- `getNacionalizacaoAtual()`
- `getCotacaoesProcesso()`
- E outros helpers

### RecalcularTabelaAdapter

**Classe:** `RecalcularTabelaAdapter`

**Métodos:**

#### `recalcular(callbackAntigo)`
Recalcula usando nova arquitetura com fallback.

**Parâmetros:**
- `callbackAntigo` (Function): Função de fallback

**Retorno:** `Object|null` - Resultado ou null

## Diagrama de Fluxo

```
┌─────────────────┐
│  User Input     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   EventBus      │ (debounce)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ ProcessoCalculator│
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌────────┐ ┌──────────┐
│Strategy│ │ Services │
└────┬───┘ └────┬─────┘
     │          │
     └────┬─────┘
          │
          ▼
     ┌────────┐
     │ Store  │
     └───┬────┘
         │
         ▼
┌─────────────────┐
│ TabelaProdutos  │
└─────────────────┘
```

## Constantes

### Config.NACIONALIZACOES

```javascript
{
    SANTA_CATARINA: 'santa_catarina',
    SANTOS: 'santos',
    ANAPOLIS: 'anapolis',
    MATO_GROSSO: 'mato_grosso',
    OUTROS: 'outros'
}
```

### Config.DECIMAIS

```javascript
{
    MOEDA: 2,
    MOEDA_PRECISA: 6,
    MOEDA_MUITO_PRECISA: 8,
    PORCENTAGEM: 2,
    PESO: 4,
    FATOR_FOB: 8
}
```
