<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'fecha_hora' => 'required',
            'impuesto' => 'required',
            'numero_comprobante' => 'required|unique:ventas,numero_comprobante|max:255',
            'total' => 'required|numeric',
            'cliente_id' => 'nullable|exists:clientes,id', // Cliente frecuente (opcional)
            'nombre_pedido' => 'nullable|string|max:100', // Nombre del pedido para venta rápida (opcional)
            'user_id' => 'required|exists:users,id',
            'comprobante_id' => 'required|exists:comprobantes,id',
            'estado_pedido' => 'nullable|in:pendiente,completado,cancelado',
            'numero_mesa' => 'nullable|string|max:20',
            'notas' => 'nullable|string',
            'forma_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'descuento_tragos' => 'nullable|numeric',
            'ganancia_tragos_a_comida' => 'nullable|numeric'
        ];
    }
}
