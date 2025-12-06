<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_producto',
        'aplica_descuento_trago',
        'precio_venta',
        'impuesto',
        'descripcion',
        'fecha_vencimiento',
        'marca_id',
        'presentacione_id',
        'img_path'
    ];

    public function compras()
    {
        return $this->belongsToMany(Compra::class)->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }

    public function ventas()
    {
        return $this->belongsToMany(Venta::class)->withTimestamps()
            ->withPivot('cantidad', 'precio_venta', 'descuento');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class)->withTimestamps();
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function presentacione()
    {
        return $this->belongsTo(Presentacione::class);
    }

    public function handleUploadImage($image)
    {
        $file = $image;
        $originalName = $file->getClientOriginalName();
        
        // Sanitizar el nombre: remover espacios y caracteres especiales
        $sanitizedName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $originalName);
        $sanitizedName = str_replace(' ', '_', $sanitizedName);
        
        // Agregar timestamp
        $name = time() . '_' . $sanitizedName;
        
        // Guardar en public/imagenes-productos (acceso directo sin symlink)
        $file->move(public_path('imagenes-productos'), $name);

        return $name;
    }
}
