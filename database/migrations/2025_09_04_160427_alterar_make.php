<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE processos ALTER COLUMN cotacao_frete_internacional TYPE DECIMAL(15,4) USING cotacao_frete_internacional::DECIMAL(15,4)");
        DB::statement("ALTER TABLE processos ALTER COLUMN cotacao_seguro_internacional TYPE DECIMAL(15,4) USING cotacao_seguro_internacional::DECIMAL(15,4)");
        DB::statement("ALTER TABLE processos ALTER COLUMN cotacao_acrescimo_frete TYPE DECIMAL(15,4) USING cotacao_acrescimo_frete::DECIMAL(15,4)");
    }

    public function down()
    {
        DB::statement("ALTER TABLE processos ALTER COLUMN cotacao_frete_internacional TYPE DECIMAL(15,5) USING cotacao_frete_internacional::DECIMAL(15,5)");
        DB::statement("ALTER TABLE processos ALTER COLUMN cotacao_seguro_internacional TYPE DECIMAL(15,5) USING cotacao_seguro_internacional::DECIMAL(15,5)");
        DB::statement("ALTER TABLE processos ALTER COLUMN cotacao_acrescimo_frete TYPE DECIMAL(15,5) USING cotacao_acrescimo_frete::DECIMAL(15,5)");
    }
};
