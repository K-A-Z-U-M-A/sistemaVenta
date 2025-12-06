<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'monto_inicial',
        'minimo_tragos_descuento',
        'monto_final',
        'ingresos_efectivo',
        'ingresos_tarjeta',
        'egresos',
        'diferencia',
        'observaciones',
        'fecha_apertura',
        'fecha_cierre',
        'estado'
    ];


    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'ingresos_efectivo' => 'decimal:2',
        'ingresos_tarjeta' => 'decimal:2',
        'egresos' => 'decimal:2',
        'diferencia' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Verificar si hay una caja abierta
     */
    public static function cajaAbierta()
    {
        return self::where('estado', 'abierta')->first();
    }

    /**
     * Calcular el total esperado en caja
     */
    public function calcularTotalEsperado()
    {
        return $this->monto_inicial + $this->ingresos_efectivo - $this->egresos;
    }

    /**
     * Calcular la diferencia entre lo esperado y lo real
     */
    public function calcularDiferencia()
    {
        if ($this->monto_final !== null) {
            return $this->monto_final - $this->calcularTotalEsperado();
        }
        return null;
    }
}
