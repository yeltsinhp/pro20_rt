<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;  // Agregar esta línea

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;  // Agregar HasApiTokens aquí

    // Protección de campos
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',  // Relación con la tabla roles
    ];

    // Configurar la relación con el modelo Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Método para obtener el nombre del rol
    public function getRoleName()
    {
        return $this->role ? $this->role->name : null;
    }

    // Comprobar si el usuario tiene un permiso
    public function hasPermission($permissionName)
    {
        return $this->role->permissions->contains('name', $permissionName);
    }

    // Establecer un acceso para el password
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
