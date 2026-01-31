<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ============================================
        // TABELA: processo_produto_multa
        // Alterar apenas campos com ≤7 casas decimais para numeric(20, 7)
        // A maioria já é numeric(20, 7), apenas alguns campos são numeric(20, 5)
        // ============================================
        
        // Campos de Quantidade e Peso (de numeric(20, 5) para numeric(20, 7))
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN quantidade TYPE numeric(20, 7);');
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN peso_liquido_unitario TYPE numeric(20, 7);');
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN peso_liquido_total TYPE numeric(20, 7);');
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN fator_peso TYPE numeric(20, 7);');
        
        // NOTA: Todos os outros campos já são numeric(20, 7) ou devem permanecer como estão
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN quantidade TYPE numeric(20, 5);');
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN peso_liquido_unitario TYPE numeric(20, 5);');
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN peso_liquido_total TYPE numeric(20, 5);');
        DB::statement('ALTER TABLE processo_produto_multa ALTER COLUMN fator_peso TYPE numeric(20, 5);');
    }
};
