<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\Processo;
use App\Models\ProcessoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProcessoController extends Controller
{

    public static function getBid()
    {
        $cachedBid = Cache::get('bid');
        if ($cachedBid) {
            return $cachedBid;
        }

        $endpoint = env('AWESOME_API_URL', 'https://economia.awesomeapi.com.br') . "/last/USD-BRL";
        $response = Http::get($endpoint);
        $data = $response->json();
        $valorDolar = 0;
        if (isset($data['USDBRL']['bid'])) {
            $valorDolar = floatval($data['USDBRL']['bid']);
        } else {
            $valorDolar = null;
        }
        Cache::put('bid', $valorDolar, now()->addHours(24));

        return $valorDolar;
    }

    public function index()
    {
        $processos = Processo::when(request()->search != '', function ($query) {
            // $query->where('name','like','%'.request()->search.'%');
        })->paginate(request()->paginacao ?? 10);;
        return view('processo.index', compact('processos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::select(['id', 'nome'])->get();

        return view('processo.form', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cliente_id' => 'required',
            ], [
                'cliente_id.required' => 'O campo cliente do tipo de documento é obrigatório!',

            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $cliente_id = $request->cliente_id;
            $processo = Processo::create([
                'codigo_interno' => $cliente_id . 'processo',
                'cliente_id' => $cliente_id
            ]);

            return redirect(route('processo.edit', $processo->id))->with('messages', ['success' => ['Processo criado com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
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
        $processo = Processo::find($id);
        $clientes = Cliente::select(['id', 'nome'])->get();
        $catalogo = Catalogo::where('cliente_id', $processo->cliente_id)->first();
        $productsClient = $catalogo->produtos;
        $dolar = self::getBid();

        return view('processo.form', compact('processo', 'clientes', 'productsClient', 'dolar'));
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
            $validator = Validator::make($request->all(), [], []);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }

            $dadosProcesso = [
                "frete_internacional" => $request->frete_internacional,
                "seguro_internacional" => $request->seguro_internacional,
                "acrescimo_frete" => $request->acrescimo_frete,
                "thc_capatazia" => $request->thc_capatazia,
                "peso_bruto" => $request->peso_bruto,
            ];
            Processo::where('id', $id)->update($dadosProcesso);

            foreach ($request->produtos as $key => $produto_id) {
                ProcessoProduto::updateOrCreate(
                    [
                        'processo_id' => $id,
                        'produto_id' => $produto_id,
                    ],
                    []
                );
            }
            return redirect(route('processo.edit', $id))->with('messages', ['success' => ['Processo atualizado com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
        }
    }

    public function destroy(int $id)
    {
        try {

            return back()->with('messages', ['success' => ['Tipo de documento desativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o tipo de documento!']]);
        }
    }
}
