<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprobanteTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe un comprobante tipo Ticket
        $ticketExistente = DB::table('comprobantes')->where('tipo_comprobante', 'Ticket')->first();
        
        if (!$ticketExistente) {
            DB::table('comprobantes')->insert([
                'tipo_comprobante' => 'Ticket',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
