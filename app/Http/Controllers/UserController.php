<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Motorista;
use App\Models\MotoristaUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNot('id',1)->paginate();
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $grupos = Grupo::whereNot('id',1)->get();
        $motoristas = Motorista::all();
        return view('users.form', compact('grupos','motoristas'));

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
            DB::beginTransaction();
           $user =  User::create([
                'name' => $request->nome,
                'email' => $request->email,
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($request->senha),
                'grupo_id' => $request->grupo
            ]);

            if($request->motorista != null){
                foreach($request->motorista as $motorista_id){
                    MotoristaUser::create(
                        ['user_id' => $user->id,
                        'motorista_id' => $motorista_id
                        ]
                    );
                }
            }
            DB::commit();
            return redirect(route('user.index'))->with('messages', ['success' => ['Usuário criada com sucesso!']]);
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('messages', ['error' => ['Não foi possível criar o usuário!']])->withInput($request->all());
            ;
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


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $grupos = Grupo::whereNot('id',1)->get();
        $user = User::findOrFail($id);
        return view('users.form', compact('grupos','user'));

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
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->nome,
                'email' => $request->email,
                'email_verified_at' => Carbon::now(),
                'grupo_id' => $request->grupo
            ]);
            if($request->senha != '' && $request->senha != null){
                $user->update([
                    'password' => Hash::make(trim($request->senha)),
                ]);
            }
      
             return redirect(route('user.index'))->with('messages', ['success' => ['Usuário atualizado com sucesso!']]);
         } catch (Exception $e) {
             return back()->with('messages', ['error' => ['Não foi possível criar o usuário!']])->withInput($request->all());
             ;
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
