<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        // Alterar precisão do campo fob_unit_usd na tabela processo_aereo_produtos usando SQL direto
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_unit_usd TYPE NUMERIC(15,7)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverter precisão do campo fob_unit_usd na tabela processo_aereo_produtos usando SQL direto
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_unit_usd TYPE NUMERIC(15,2)');
    }
};
