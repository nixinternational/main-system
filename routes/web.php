<?php

use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PermissaoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\LoteController;
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
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    Route::get('/clientsByName', [ClienteController::class, 'clientsByName'])->name('cliente.clientes')->middleware('permission:admin|root');
    Route::get('/motoristaByName', [MotoristaController::class, 'motoristaByName'])->name('motorista.motorista')->middleware('permission:admin|root');

    Route::post('/relatorioMotorista', [MotoristaController::class, 'relatorioMotorista'])->name('motorista.relatorio')->middleware('permission:admin|root');
    Route::get('/motorista-relatorio', [MotoristaController::class, 'relatorioMotoristaIndex'])->name('motorista.relatorio.index')->middleware('permission:admin|root');

    Route::post('/relatorioCliente', [ClienteController::class, 'relatorioCliente'])->name('cliente.relatorio')->middleware('permission:admin|root');
    Route::get('/cliente-relatorio', [ClienteController::class, 'relatorioClienteIndex'])->name('cliente.relatorio.index')->middleware('permission:admin|root');

    Route::post('/relatorioProduto', [ProdutoController::class, 'relatorioProduto'])->name('produto.relatorio')->middleware('permission:admin|root');;
    Route::get('/produto-relatorio', [ProdutoController::class, 'relatorioProdutoIndex'])->name('produto.relatorio.index')->middleware('permission:admin|root');

    Route::get('/relatorioProducao', [ProdutoController::class, 'relatorioProducaoIndex'])->name('producao.relatorio')->middleware('permission:admin|root');
    Route::post('/processRelatorioProducao', [ProdutoController::class, 'processRelatorioProducao'])->name('producao.relatorio.processar')->middleware('permission:admin|root');

    Route::post('/pedidosDelete', [PedidoController::class, 'pedidosDelete'])->name('pedidos.multipleDelete');

    Route::delete('/removeProdutoPedido/{pedido_produto_id}', [PedidoController::class, 'removeProdutoPedido'])->name('pedidos.removeProdutoPedido');

    Route::get('/pedidoAtualiza', [PedidoController::class, 'baixaPedido'])->name('pedido.atualiza')->middleware('permission:admin|root|motorista');;
    Route::get('/getPedidoBaixa/{pedido_id}', [PedidoController::class, 'getPedidoBaixa'])->name('pedido.getBaixa')->middleware('permission:admin|root|motorista');
    Route::post('/atualizaPedido/{pedido_id}', [PedidoController::class, 'atualizaPedido'])->name('pedido.troca')->middleware('permission:admin|root|motorista');
    Route::get('/getPedidosByYear', [HomeController::class, 'getPedidosByYear']);

    Route::resource('categoria', CategoriaController::class)->middleware('permission:admin|root');
    Route::resource('grupo', GrupoController::class)->middleware('permission:admin|root');;
    Route::resource('permissao', PermissaoController::class)->middleware('permission:admin|root');;
    Route::resource('user', UserController::class)->middleware('permission:admin|root');;
    Route::resource('produto', ProdutoController::class)->middleware('permission:admin|root');
    Route::resource('lancamento', LancamentoController::class)->middleware('permission:admin|root');;
    Route::resource('producao', ProducaoController::class)->middleware('permission:admin|root');;
    Route::resource('producaoBaixa', ProducaoBaixaController::class)->middleware('permission:admin|root|producao');;


    Route::resource('cliente', ClienteController::class)->middleware('permission:admin|root');;
    Route::resource('pedido', PedidoController::class)->middleware('permission:admin|root');;
    Route::resource('motorista', MotoristaController::class)->middleware('permission:admin|root');;

    Route::post('/motorista-entrega', [MotoristaController::class, 'motoristaEntrega'])->name('motorista.entrega')->middleware('permission:admin|root|motorista');
    Route::get('/motorista-entrega', [MotoristaController::class, 'motoristaEntregaIndex'])->name('motorista.entrega.index')->middleware('permission:admin|root|motorista');


    Route::post('atualizar', [PedidoController::class, 'atualizarPedidos'])->name('pedido.atualizar');
    Route::post('confirmarProducao', [ProducaoBaixaController::class, 'confirmarProducao'])->name('producao.confirmar');

    


    Route::group(['prefix' => 'ativar'], function () {
        Route::put('/cliente/{categoria_id}', [ClienteController::class, 'ativar'])->name('cliente.ativar');
        Route::put('/marca/{marca_id}', [MarcaController::class, 'ativar'])->name('marca.ativar');
        Route::put('/produto/{produto_id}', [ProdutoController::class, 'ativar'])->name('produto.ativar');
        Route::put('/fornecedor/{fornecedor_id}', [FornecedorController::class, 'ativar'])->name('fornecedor.ativar');
        Route::put('/motorista/{motorista_id}', [MotoristaController::class, 'ativar'])->name('motorista.ativar');
    });
});
