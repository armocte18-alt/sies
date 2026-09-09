<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'nombre_oficial', 'clave_financiera', 'centro_distribucion',
    'estatus_operativo', 'titular_empleado_id', 'created_by', 'updated_by',
])]
class Sucursal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sucursales';

    // --- Relaciones de personal ---

    public function titular(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'titular_empleado_id');
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // --- Secciones por coordinación (una a una) ---

    public function ubicacion(): HasOne
    {
        return $this->hasOne(SucursalUbicacion::class);
    }

    public function operacion(): HasOne
    {
        return $this->hasOne(SucursalOperacion::class);
    }

    public function inmueble(): HasOne
    {
        return $this->hasOne(SucursalInmueble::class);
    }

    public function equipamiento(): HasOne
    {
        return $this->hasOne(SucursalEquipamiento::class);
    }

    public function finanzas(): HasOne
    {
        return $this->hasOne(SucursalFinanza::class);
    }

    public function activosTi(): HasMany
    {
        return $this->hasMany(ActivoTi::class);
    }
}
