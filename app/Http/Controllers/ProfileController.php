<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Upload da foto de perfil
        if ($request->hasFile('avatar')) {
            // Deletar foto antiga se existir
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            // Salvar nova foto
            $file = $request->file('avatar');
            $filename = $user->id . '.' . $file->getClientOriginalExtension();
            $caminho = 'avatars/' . $filename;
            $path = $file->storeAs('avatars', $filename, 'public');
            
            // Garantir que o arquivo foi salvo
            if (!Storage::disk('public')->exists($caminho)) {
                return redirect()->back()->with('messages', ['error' => ['Erro ao salvar a imagem.']])->withInput($request->all());
            }
            
            $url = Storage::disk('public')->url($caminho);
            $data['avatar'] = $filename;
        }
        // Atualizar o usuário
        $user->update($data);
        
        // Recarregar o usuário do banco para garantir que os dados estão atualizados
        $user->refresh();
        
        // Verificar se o avatar foi salvo no banco
        if ($request->hasFile('avatar') && !$user->avatar) {
            return redirect()->back()->with('messages', ['error' => ['Erro ao salvar o avatar no banco de dados.']])->withInput($request->all());
        }
        
        // Atualizar o usuário na sessão para refletir as mudanças imediatamente
        Auth::login($user);

        return redirect()->back()->with('messages', ['success' => ['Perfil atualizado com sucesso!']]);
    }
}
