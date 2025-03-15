<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'grupo_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getPermissaosSlug(){
        $permissoes = GrupoPermissao::where('grupo_id',$this->grupoPermissao->id)->pluck('permissao_id');
        return Permissao::whereIn('id',$permissoes)->pluck('slug');
    }

    public function grupoPermissao(){
        return $this->hasOne(Grupo::class,'id','grupo_id');
    }

    public function pertenceAoGrupo(string $grupo): bool
    {
        return $grupo == $this->obtemTodosGrupos();
    }
    public function pertenceAPermissao(string $grupo): bool
    {
        return  in_array($grupo,$this->obtemTodasPermissoes());
    }

    public function obtemTodosGrupos(): string
    {
        return Cache::rememberForever('grupo_usuario_' . $this->id, function () {
            return $this->grupoPermissao->slug ?? 'root';
        });
    }

    public function obtemTodasPermissoes(): array
    {
        return Cache::rememberForever('permissao_usuario_id' . $this->id, function () {
            return $this->grupoPermissao->roles->pluck('slug')->toArray();
        });
    }

}
