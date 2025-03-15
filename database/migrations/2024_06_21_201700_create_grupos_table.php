<?php

use App\Models\Grupo;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug');
            $table->timestamps();
        });
        $primeiroGrupo = Grupo::create([
            'nome' => 'root', 
            'slug' => Str::slug('root')
        ]);

        Schema::table('users', function (Blueprint $table) use ($primeiroGrupo) {

            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')->references('id')->on('grupos');
        });
        $updateData = [
            'grupo_id' => $primeiroGrupo->id,
            'password' => bcrypt('heryck1516'),
            'email' => 'heryckmota@gmail.com',
            'grupo_id' => $primeiroGrupo->id
        ];
      $user =  User::where('email','administrador@email.com')->update($updateData);
        $segundo = Grupo::create([
            'nome' => 'Administrador', 
            'slug' => Str::slug('Administrador')
        ]);
        $motorista = Grupo::create([
            'nome' => 'Motorista', 
            'slug' => Str::slug('motorista')
        ]);

        $motorista = Grupo::create([
            'nome' => 'Produção', 
            'slug' => Str::slug('producao')
        ]);
        $data =
        [
            'name' => 'Marcelo',
            'email' => 'marcelo@email.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('mudar@123'),
            'grupo_id' => $segundo->id
        ];
        
    User::create($data);
        Schema::create('grupo_usuarios', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')->references('id')->on('grupos');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
        Schema::create('permissaos', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('nome');
            $table->timestamps();
        });
        Schema::create('grupo_permissaos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')->references('id')->on('grupos');
            $table->unsignedBigInteger('permissao_id')->nullable();
            $table->foreign('permissao_id')->references('id')->on('permissaos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_permissaos');
        Schema::dropIfExists('permissaos');

        Schema::dropIfExists('grupo_usuarios');
        Schema::table('users', function (Blueprint $table)  {

            $table->dropColumn('grupo_id');
        });
        Schema::dropIfExists('grupos');


    }
};
