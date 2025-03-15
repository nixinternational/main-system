<?php

namespace App\Http\Controllers;

use App\Http\Requests\MotoristaRequest;
use App\Http\Requests\RelatorioMotorista;
use App\Models\Motorista;
use App\Models\MotoristaUser;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MotoristaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $motoristas = Motorista::withTrashed()->when(request()->search != '', function ($query) {
            $query->where(DB::raw('LOWER(nome)'), 'LIKE', '%' . strtolower(request()->search) . '%');
        })->paginate(request()->paginacao ?? 10);
        return view('motorista.index', compact('motoristas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('motorista.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MotoristaRequest $request)
    {
        try {
            Motorista::create($request->except(['_token']));
            return redirect(route('motorista.index'))->with('messages', ['success' => ['Motorista criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível salvar o motorista. Tente novamente mais tarde!']])->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $motorista = Motorista::findOrFail($id);
            return view('motorista.form', compact('motorista',));
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível encontrar o motorista!']]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            Motorista::findOrFail($id)->update($request->except(['_token']));
            return redirect(route('motorista.index'))->with('messages', ['success' => ['Motorista criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível salvar o motorista. Tente novamente mais tarde!']])->withInput($request->all());
        }
    }


    public function destroy(int $id)
    {
        try {
            Motorista::findOrFail($id)->delete();
            return back()->with('messages', ['success' => ['Motorista excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o motorista!']]);
        }
    }

    public function ativar(int $motorista_id)
    {
        try {
            Motorista::withTrashed()->where('id', $motorista_id)->update(['deleted_at' => null]);
            return back()->with('messages', ['success' => ['Motorista ativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar a categoria!' . $e->getMessage()]]);
        }
    }
    public function motoristaByName()
    {
        try {
            $name = request()->query('nome');

            if ($name == '') {
                return response()->json(['success' => true, 'data' => []], 200);
            }
            $resultados = Motorista::where(DB::raw('LOWER(nome)'), 'LIKE', '%' . strtolower($name) . '%')->select(['nome', 'id', 'turno'])->get();
            return response()->json(['success' => true, 'data' => $resultados], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'data' => null, 'message' => 'Erro ao processar requisição. Tente novamente mais tarde.' . $e->getMessage()], 400);
        }
    }
    public function relatorioMotorista(RelatorioMotorista $request)
    {
        try {
            ini_set('memory_limit', '-1');
            $inicio = Carbon::createFromFormat('d/m/Y', $request->data)->startOfDay();
            $fim = Carbon::createFromFormat('d/m/Y', $request->data)->endOfDay();

            $motoristasId = Motorista::when($request->motorista != null && $request->motorista != '', function ($query) use ($request) {
                $query->where('id', $request->motorista);
            })->whereHas('pedidos', function ($query2) use ($inicio, $fim) {
                $query2->whereBetween('dt_previsao', [$inicio, $fim]);
            })->pluck('id');
            $dados = [];

            foreach ($motoristasId as $motorista_id) {
                $pedidos = Pedido::with(['produtos', 'cliente'])->where('motorista_id',$motorista_id)->whereBetween('dt_previsao', [$inicio, $fim])->orderBy('dt_previsao', 'ASC')->get();
                $dados[$motorista_id]['motorista'] = Motorista::find($motorista_id);
                $dados[$motorista_id]['pedidos'] = $pedidos;
            }


            $pdf =  Pdf::loadView('relatorios.pdf.motorista', [
                'pedidos' => $dados,
                'dia' => $inicio->format('d/m/Y')
            ]);
            return $pdf->download("Relatório entregas {$inicio->format('d/m/Y')}.pdf");
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'data' => null, 'message' => 'Erro ao processar requisição. Tente novamente mais tarde.' . $e->getMessage()], 400);
        }
    }
    public function relatorioMotoristaIndex()
    {
        try {

            return view('relatorios.motorista');
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível abrir os relatórios!' . $e->getMessage()]]);
        }
    }

    public function motoristaEntrega(Request $request){

    }
    public function motoristaEntregaIndex(){
        
        if(request()->motoristas == ''){
            $pedidos = Pedido::where('id','=',0)->paginate(request()->paginacao ?? 50);
            $motoristas = Motorista::select(['nome','id'])->get();
            return view('motorista.entrega',compact('pedidos','motoristas'));
        }
        $dtInicial = Carbon::now()->startOfDay();
        $dtFinal = Carbon::now()->endOfDay();
        $pedidos = Pedido::with(['cliente'])
        ->when(request()->motoristas,function($query){
            $query->whereHas('motorista', function ($queryMotora) {
                $queryMotora->whereIn('id',request()->motoristas);
            });
        })
        ->whereBetween('dt_previsao', [$dtInicial, $dtFinal])
        ->when(request()->search != '', function ($query) {
            $query->whereHas('cliente', function ($queryCliente) {
                $queryCliente->where(DB::raw('lower(name)'), 'like', '%' . strtolower(request()->search) . '%');
            });
        })
        ->when(request()->status != '' && request()->status != '-1', function ($query) {
            $query->where('status', request()->status);
        })
        ->paginate(request()->paginacao ?? 50);
        $motoristas = Motorista::select(['nome','id'])->get();
        return view('motorista.entrega',compact('pedidos','motoristas'));
    }
}