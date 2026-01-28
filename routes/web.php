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
use App\Http\Controllers\ProcessoProdutoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AuditoriaController;

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

Route::middleware(['auth'])->group(function(){
    Route::any('toogle-ip-protection',[UserController::class, 'toggleIpProtection'])->name('toogle-ip-protection');
});
Route::middleware(['auth','check.ip'])->group(function () {

Route::post('/processo-produtos/batch-delete', [ProcessoProdutoController::class, 'batchDelete'])
    ->middleware('permission.map:processo_produto')
    ->name('processo.produtos.batchDelete');

Route::post('/processo-produtos-multa/batch-delete', [ProcessoController::class, 'batchDeleteMulta'])
    ->middleware('permission.map:processo_produto')
    ->name('processo.produtos.multa.batchDelete');
Route::get('/produtos/buscar', [ProdutoController::class, 'searchByCatalogo'])
    ->middleware('permission.map:processo')
    ->name('produtos.search');
    Route::get('/processo-criar/{cliente_id}', [ProcessoController::class, 'create'])
    ->middleware('permission.map:processo')
    ->name('processo.criar');

    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:admin|root');
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('grupo', GrupoController::class)->middleware('permission:admin|root');
    Route::resource('permissao', PermissaoController::class)->middleware('permission:admin|root');
    Route::resource('user', UserController::class)->middleware('super.admin');
    Route::patch('user/{user}/toggle', [UserController::class, 'toggleStatus'])
        ->middleware('super.admin')
        ->name('user.toggle');
    Route::resource('banco-nix', BancoNixController::class)->middleware('permission:admin|root|producao');
    Route::resource('catalogo', CatalogoController::class)->middleware('permission.map:catalogo');
    Route::resource('cliente', ClienteController::class)->middleware('permission.map:cliente');
    Route::resource('produto', ProdutoController::class)->middleware('permission.map:produto');
    Route::resource('tipo-documento', TipoDocumentoController::class)->middleware('permission:admin|root');
    Route::resource('processo', ProcessoController::class)->middleware('permission.map:processo');
    Route::resource('fornecedor', FornecedorController::class);

    Route::get('/processos-cliente/{cliente_id}', [ProcessoController::class, 'processoCliente'])
        ->middleware('permission.map:processo_extras')
        ->name('processo-cliente');

    Route::post('atualizar', [PedidoController::class, 'atualizarPedidos'])->name('pedido.atualizar');
    Route::post('update-client-emails/{id}', [ClienteController::class, 'updateClientEmail'])
        ->middleware('permission.map:cliente_extras')
        ->name('cliente.update.email');
    Route::post('update-client-responsaveis/{id}', [ClienteController::class, 'updateClientResponsaveis'])
        ->middleware('permission.map:cliente_extras')
        ->name('cliente.update.responsavel');
    Route::post('update-client-aduanas/{id}', [ClienteController::class, 'updateClientAduanas'])
        ->middleware('permission.map:cliente_extras')
        ->name('cliente.update.aduanas');
    Route::post('update-client-especificidades/{id}', [ClienteController::class, 'updateClientEspecificidades'])
        ->middleware('permission.map:cliente_extras')
        ->name('cliente.update.especificidades');
    Route::post('update-client-documentos/{id}', [ClienteController::class, 'updateClientDocument'])
        ->middleware('permission.map:cliente_extras')
        ->name('cliente.update.documents');
    Route::get('/processo/{id}/esboco-pdf', [ProcessoController::class, 'esbocoPdf'])
        ->middleware('permission.map:processo_extras')
        ->name('processo.esboco.pdf');
    Route::post('/atualizar-campos-cabecalho/{id}', [ProcessoController::class, 'camposCabecalho'])
        ->middleware('permission.map:processo_extras')
        ->name('cabecalho.atualizar');


    Route::match(['put', 'post'], 'updateProcesso/{id}',[ProcessoController::class,'updateProcesso'])
        ->middleware('permission.map:processo_extras')
        ->name('update.processo');
    //  Route::get('settings/bid', [ProdutoController::class, 'getBid'])->withoutMiddleware('auth:sanctum');

    Route::delete('destroy-bank/{id}', [ClienteController::class, 'destroyBancoCliente'])
        ->middleware('permission.map:cliente_extras')
        ->name('banco.cliente.destroy');
    Route::delete('destroy-document/{id}', [ClienteController::class, 'deleteDocument'])
        ->middleware('permission.map:cliente_extras')
        ->name('documento.cliente.destroy');
    Route::delete('destroy-produto-processo/{id}', [ProcessoController::class, 'destroyProduto'])
        ->middleware('permission.map:processo_extras')
        ->name('processo.produto.destroy');
    
    // Rota para salvar apenas os campos do cabeçalho (cabecalhoInputs) do processo marítimo
    Route::post('/processo/{id}/salvar-cabecalho-inputs-maritimo', [ProcessoController::class, 'salvarCabecalhoInputsMaritimo'])
        ->middleware('permission.map:processo')
        ->name('processo.salvar.cabecalho.inputs.maritimo');
    // Rota para salvar apenas os campos do cabeçalho (cabecalhoInputs) do processo aéreo
    Route::post('/processo/{id}/salvar-cabecalho-inputs-aereo', [ProcessoController::class, 'salvarCabecalhoInputsAereo'])
        ->middleware('permission.map:processo')
        ->name('processo.salvar.cabecalho.inputs.aereo');
    // Rota para salvar apenas os campos do cabeçalho (cabecalhoInputs) do processo rodoviário
    Route::post('/processo/{id}/salvar-cabecalho-inputs-rodoviario', [ProcessoController::class, 'salvarCabecalhoInputsRodoviario'])
        ->middleware('permission.map:processo')
        ->name('processo.salvar.cabecalho.inputs.rodoviario');

    Route::group(['prefix' => 'ativar'], function () {
        Route::put('/documento/{documento_id}', [TipoDocumentoController::class, 'ativar'])->name('tipo-documento.ativar');
        Route::put('/marca/{marca_id}', [MarcaController::class, 'ativar'])->name('marca.ativar');
        Route::put('/produto/{produto_id}', [ProdutoController::class, 'ativar'])->name('produto.ativar');
        Route::put('/fornecedor/{fornecedor_id}', [FornecedorController::class, 'ativar'])->name('fornecedor.ativar');
        Route::put('/motorista/{motorista_id}', [MotoristaController::class, 'ativar'])->name('motorista.ativar');
    });
    Route::get('obterCotacao/{data_cotacao?}', [CotacaoController::class, 'obterCotacao'])->name('cotacao.obter');
    Route::any('currency-update',[ProcessoController::class, 'updatecurrencies'])
        ->middleware('permission.map:processo_extras')
        ->name('currency.update');
    
    // Rotas de Logs
    Route::get('logs', [LogController::class, 'index'])
        ->middleware('permission:admin|root')
        ->name('logs.index');
    Route::post('logs/clear', [LogController::class, 'clear'])
        ->middleware('permission:admin|root')
        ->name('logs.clear');
    Route::get('logs/download', [LogController::class, 'download'])
        ->middleware('permission:admin|root')
        ->name('logs.download');

    Route::get('auditoria', [AuditoriaController::class, 'index'])
        ->middleware('permission:auditoria_listar')
        ->name('auditoria.index');
});
