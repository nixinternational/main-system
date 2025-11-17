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
    public const SUPER_ADMIN_EMAILS = [
        'administrador@email.com',
        'heryckmota@gmail.com',
    ];

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
        'grupo_id',
        'active',
        'avatar'
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
        'active' => 'boolean',
    ];

    public function grupoPermissao()
    {
        return $this->hasOne(Grupo::class, 'id', 'grupo_id');
    }

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'permissao_usuario')
            ->withTimestamps();
    }

    public function clientesPermitidos()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_user')->withTimestamps();
    }

    public function pertenceAoGrupo(string $grupo): bool
    {
        return $this->hasRole($grupo);
    }

    public function pertenceAPermissao(string $permissao): bool
    {
        return $this->hasPermission($permissao);
    }

    public function hasRole(string $slug): bool
    {
        $current = $this->grupoPermissao?->slug;
        return $current !== null && $current === $slug;
    }

    public function obtemTodosGrupos(): ?string
    {
        return $this->grupoPermissao->slug ?? null;
    }

    public function obtemTodasPermissoes(): array
    {
        return $this->allPermissionSlugs();
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return in_array($slug, $this->allPermissionSlugs(), true);
    }

    public function syncPermissions(?array $permissoesIds): void
    {
        $this->permissoes()->sync($permissoesIds ?? []);
        $this->unsetRelation('permissoes');
    }

    public function syncClientes(?array $clientesIds): void
    {
        $this->clientesPermitidos()->sync($clientesIds ?? []);
    }

    public function canAccessCliente(?int $clienteId): bool
    {
        if ($clienteId === null) {
            return true;
        }

        if ($this->canAccessAllClientes()) {
            return true;
        }

        return $this->clientesPermitidos()
            ->where('cliente_id', $clienteId)
            ->exists();
    }

    public function accessibleClienteIds(): ?array
    {
        if ($this->canAccessAllClientes()) {
            return null;
        }

        return $this->clientesPermitidos()
            ->select('cliente_id')
            ->pluck('cliente_id')
            ->toArray();
    }

    public function canAccessAllClientes(): bool
    {
        return $this->isSuperUser() || $this->hasPermission('admin');
    }

    public function isSuperUser(): bool
    {
        $role = $this->obtemTodosGrupos();
        return $role === 'root' || $this->hasDirectPermission('root') || in_array($this->email, self::SUPER_ADMIN_EMAILS, true);
    }

    protected function hasDirectPermission(string $slug): bool
    {
        return $this->permissoes()
            ->where('slug', $slug)
            ->exists();
    }

    protected function allPermissionSlugs(): array
    {
        if (!$this->relationLoaded('permissoes')) {
            $this->load('permissoes');
        }

        $direct = $this->permissoes->pluck('slug')->toArray();
        $group = $this->grupoPermissao?->roles->pluck('slug')->toArray() ?? [];

        return array_values(array_unique(array_merge($direct, $group)));
    }
}
