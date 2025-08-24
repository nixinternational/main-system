<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('processo_produtos', function (Blueprint $table) {
            $table->integer('item')->nullable()->after('id'); // ou onde quiser
        });
    }

    public function down(): void
    {
        Schema::table('processo_produtos', function (Blueprint $table) {
            $table->dropColumn('item');
        });
    }
};
