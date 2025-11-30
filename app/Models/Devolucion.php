<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devoluciones';

    protected $fillable = [
        'venta_id',
        'user_id',
        'fecha_hora',
        'monto_devuelto',
        'motivo',
        'tipo'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'monto_devuelto' => 'decimal:2'
    ];

    // Relaciones
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(DevolucionItem::class);
    }
}
