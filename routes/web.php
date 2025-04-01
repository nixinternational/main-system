<?php

use App\Http\Controllers\BancoNixController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PermissaoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProducaoBaixaController;
use App\Http\Controllers\ProducaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    $user = Auth::user();
    if($user == null){
        return redirect('/login');
    }
    return redirect('/home');

});

Auth::routes();



Route::middleware('auth')->group(function () {

    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:admin|root');;
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('grupo', GrupoController::class)->middleware('permission:admin|root');;
    Route::resource('permissao', PermissaoController::class)->middleware('permission:admin|root');;
    Route::resource('user', UserController::class)->middleware('permission:admin|root');;
    Route::resource('banco-nix', BancoNixController::class)->middleware('permission:admin|root|producao');;


    Route::resource('cliente', ClienteController::class)->middleware('permission:admin|root');;


    Route::post('atualizar', [PedidoController::class, 'atualizarPedidos'])->name('pedido.atualizar');
    Route::post('update-client-emails/{id}', [ClienteController::class, 'updateClientEmail'])->name('cliente.update.email');
    Route::post('update-client-responsaveis/{id}', action: [ClienteController::class, 'updateClientResponsaveis'])->name('cliente.update.responsavel');
    Route::post('update-client-aduanas/{id}', action: [ClienteController::class, 'updateClientAduanas'])->name('cliente.update.aduanas');
    Route::post('update-client-especificidades/{id}', action: [ClienteController::class, 'updateClientEspecificidades'])->name('cliente.update.especificidades');
    
    
    Route::delete('destroy-bank/{id}', action: [ClienteController::class, 'destroyBancoCliente'])->name('banco.cliente.destroy');


    Route::group(['prefix' => 'ativar'], function () {
        Route::put('/cliente/{categoria_id}', [ClienteController::class, 'ativar'])->name('cliente.ativar');
        Route::put('/marca/{marca_id}', [MarcaController::class, 'ativar'])->name('marca.ativar');
        Route::put('/produto/{produto_id}', [ProdutoController::class, 'ativar'])->name('produto.ativar');
        Route::put('/fornecedor/{fornecedor_id}', [FornecedorController::class, 'ativar'])->name('fornecedor.ativar');
        Route::put('/motorista/{motorista_id}', [MotoristaController::class, 'ativar'])->name('motorista.ativar');
    });
});
