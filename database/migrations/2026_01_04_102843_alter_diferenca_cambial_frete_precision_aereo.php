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
        // Alterar precisão do campo diferenca_cambial_frete na tabela processo_aereos usando SQL direto
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN diferenca_cambial_frete TYPE NUMERIC(15,4)');

        // Alterar precisão do campo diferenca_cambial_frete na tabela processo_aereo_produtos usando SQL direto
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN diferenca_cambial_frete TYPE NUMERIC(15,4)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverter precisão do campo diferenca_cambial_frete na tabela processo_aereos usando SQL direto
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN diferenca_cambial_frete TYPE NUMERIC(15,2)');

        // Reverter precisão do campo diferenca_cambial_frete na tabela processo_aereo_produtos usando SQL direto
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN diferenca_cambial_frete TYPE NUMERIC(15,2)');
    }
};
