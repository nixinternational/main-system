<?php

use App\Http\Controllers\BancoNixController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PermissaoController;
use App\Http\Controllers\TipoDocumentoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CotacaoController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProcessoController;
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
    Route::resource('catalogo', CatalogoController::class)->middleware('permission:admin|root|producao');;
    Route::resource('cliente', ClienteController::class)->middleware('permission:admin|root');;
    Route::resource('produto', ProdutoController::class)->middleware('permission:admin|root');;
    Route::resource('tipo-documento', TipoDocumentoController::class)->middleware('permission:admin|root');;
    Route::resource('processo', ProcessoController::class)->middleware('permission:admin|root');;
    Route::resource('fornecedor', FornecedorController::class);

    Route::get('/processos-cliente/{cliente_id}', [ProcessoController::class, 'processoCliente'])->name('processo-cliente');

    Route::post('atualizar', [PedidoController::class, 'atualizarPedidos'])->name('pedido.atualizar');
    Route::post('update-client-emails/{id}', [ClienteController::class, 'updateClientEmail'])->name('cliente.update.email');
    Route::post('update-client-responsaveis/{id}', action: [ClienteController::class, 'updateClientResponsaveis'])->name('cliente.update.responsavel');
    Route::post('update-client-aduanas/{id}', action: [ClienteController::class, 'updateClientAduanas'])->name('cliente.update.aduanas');
    Route::post('update-client-especificidades/{id}', action: [ClienteController::class, 'updateClientEspecificidades'])->name('cliente.update.especificidades');
    Route::post('update-client-documentos/{id}', action: [ClienteController::class, 'updateClientDocument'])->name('cliente.update.documents');
    Route::get('/processo/{id}/esboco-pdf', [ProcessoController::class, 'esbocoPdf'])->name('processo.esboco.pdf');

    //  Route::get('settings/bid', [ProdutoController::class, 'getBid'])->withoutMiddleware('auth:sanctum');

    Route::delete('destroy-bank/{id}', action: [ClienteController::class, 'destroyBancoCliente'])->name('banco.cliente.destroy');
    Route::delete('destroy-document/{id}', action: [ClienteController::class, 'deleteDocument'])->name('documento.cliente.destroy');
    Route::delete('destroy-produto-processo/{id}', action: [ProcessoController::class, 'destroyProduto'])->name('banco.cliente.destroy');

    Route::group(['prefix' => 'ativar'], function () {
        Route::put('/documento/{documento_id}', [TipoDocumentoController::class, 'ativar'])->name('tipo-documento.ativar');
        Route::put('/marca/{marca_id}', [MarcaController::class, 'ativar'])->name('marca.ativar');
        Route::put('/produto/{produto_id}', [ProdutoController::class, 'ativar'])->name('produto.ativar');
        Route::put('/fornecedor/{fornecedor_id}', [FornecedorController::class, 'ativar'])->name('fornecedor.ativar');
        Route::put('/motorista/{motorista_id}', [MotoristaController::class, 'ativar'])->name('motorista.ativar');
    });
    Route::get('obterCotacao/{data_cotacao?}', [CotacaoController::class, 'obterCotacao'])->name('cotacao.obter');
    Route::any('currency-update',[ProcessoController::class, 'updatecurrencies'])->name('currency.update');
});
