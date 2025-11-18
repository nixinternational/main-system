<?php

namespace Tests\Unit;

use App\Http\Controllers\ProcessoController;
use App\Models\ProcessoProduto;
use ReflectionClass;
use Tests\TestCase;

class ProcessoControllerParsingTest extends TestCase
{
    private ProcessoController $controller;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProcessoController();
        $this->reflection = new ReflectionClass($this->controller);
    }

    private function callPrivate(string $method, array $parameters = [])
    {
        $methodInstance = $this->reflection->getMethod($method);
        $methodInstance->setAccessible(true);

        return $methodInstance->invokeArgs($this->controller, $parameters);
    }

    public function test_parse_money_to_float_accepts_brazilian_format(): void
    {
        $result = $this->callPrivate('parseMoneyToFloat', ['1.234,56']);

        $this->assertSame(1234.56, $result);
    }

    public function test_parse_money_to_float_accepts_decimal_format(): void
    {
        $result = $this->callPrivate('parseMoneyToFloat', ['9876.54']);

        $this->assertSame(9876.54, $result);
    }

    public function test_parse_money_to_float_returns_null_for_empty_values(): void
    {
        $this->assertNull($this->callPrivate('parseMoneyToFloat', [null]));
        $this->assertNull($this->callPrivate('parseMoneyToFloat', ['']));
    }

    public function test_parse_percentage_to_float_handles_symbol_and_comma(): void
    {
        $result = $this->callPrivate('parsePercentageToFloat', ['45,5%']);

        $this->assertSame(45.5, $result);
    }

    public function test_safe_percentage_preserves_numeric_values(): void
    {
        $this->assertSame(33.78, $this->callPrivate('safePercentage', ['33.78']));
        $this->assertNull($this->callPrivate('safePercentage', ['']));
    }

    public function test_parse_model_fields_from_model_converts_numeric_strings(): void
    {
        $produto = new ProcessoProduto([
            'fob_unit_usd' => '123.4500',
            'cotacao_frete_internacional' => '4,1234',
        ]);

        /** @var ProcessoProduto $resultado */
        $resultado = $this->callPrivate('parseModelFieldsFromModel', [$produto]);

        $this->assertSame(123.45, $resultado->fob_unit_usd);
        // campo listado como exceção no método deve permanecer inalterado
        $this->assertSame('4,1234', $resultado->cotacao_frete_internacional);
    }
}

