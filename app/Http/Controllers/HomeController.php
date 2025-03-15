<?php

namespace App\Http\Controllers;

use App\DataTables\AgendamentosDataTable;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Produto;
use App\Repositories\CategoriaRepository;
use App\Repositories\ProdutoRepository;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        if($user->grupoPermissao != null){
            $user_group = $user->grupoPermissao->slug ;
        
    
            if($user_group == 'motorista'){
                
                return redirect('/motorista-entrega');
        
            }else if($user_group == 'producao'){
                return redirect('/producaoBaixa');
        
            }
        }
        try {
            $inicio = Carbon::now()->startOfDay();
            $fim = Carbon::now()->endOfDay();

            $pedidosHoje = Pedido::whereBetween('dt_previsao', [$inicio, $fim])->get()->count();

            $primeiroDiaDoMes = Carbon::now()->startOfMonth()->toDateString();
            $ultimoDiaDoMes = Carbon::now()->endOfMonth()->toDateString();

            $pedidosEntregues = Pedido::whereBetween('dt_previsao', [$primeiroDiaDoMes, $ultimoDiaDoMes])
                ->where('status', '=', 'ENTREGUE')
                ->get()
                ->count();
            $pedidosAtrasados = Pedido::where('dt_previsao', '<',Carbon::now())
                ->where('status', '=', 'AGENDADO')
                ->get()
                ->count();
            $pedidosCancelados = Pedido::whereBetween('dt_previsao', [$primeiroDiaDoMes, $ultimoDiaDoMes])
                ->where('status', '=', 'CANCELADO')
                ->get()
                ->count();


            return view('home', compact('pedidosHoje', 'pedidosEntregues','pedidosCancelados','pedidosAtrasados'));
        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível acessar a página principal!']]);
        }
    }

    public function getPedidosByYear()
    {

        $year = Carbon::now()->year;

        $firstDateOfYear = Carbon::createFromDate($year, 1, 1)->toDateString();
        $lastDateOfYear = Carbon::createFromDate($year, 12, 31)->toDateString();
        $meses = collect(range(1, 12))->map(function ($mes) {
            return ['mes' => $mes, 'quantidade' => 0];
        });
        $pedidos = Pedido::whereBetween('dt_previsao', [$firstDateOfYear, $lastDateOfYear])
            ->select(DB::raw('EXTRACT(MONTH FROM dt_previsao) as mes'), DB::raw('COUNT(*) as quantidade'))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM dt_previsao)'))
            ->get();
        $quantidadePedidosPorMes = $meses->map(function ($item) use ($pedidos) {
            $quantidade = $pedidos->filter(function ($element) use ($item) {
                return $element->mes == $item['mes'];
            });
            $quantidade = $quantidade->isNotEmpty() ?  $quantidade->first()->quantidade : 0;

            return [
                'mes' => $item['mes'],
                'quantidade' => $quantidade
            ];
        });


        return response()->json($quantidadePedidosPorMes);
    }

    public function produtosCategoriaEmEstoque(int $categoria_id): View|RedirectResponse
    {

        try {
            $produtoRepository = new ProdutoRepository(new Produto());
            $produtosCategoria = $produtoRepository->getIndexHome($categoria_id);
            $categoria = Categoria::findOrFail($categoria_id);
            return view('produtosCategoria.index', compact('produtosCategoria', 'categoria'));
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível encontrar a categoria!']]);
        }
    }
}
