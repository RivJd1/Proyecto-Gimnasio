<?php

namespace Database\Seeders;

use App\Models\SupportProvider;
use Illuminate\Database\Seeder;

class SupportProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            ['nombre' => 'TecnoFit Mantenimiento',  'telefono' => '2234-5678', 'email' => 'tecnofit@mail.com',   'servicio' => 'Mantenimiento de máquinas de gimnasio', 'notas' => 'Disponible lunes a viernes.',     'activo' => true],
            ['nombre' => 'CoolAir Servicios',        'telefono' => '2235-6789', 'email' => 'coolair@mail.com',    'servicio' => 'Mantenimiento de aires acondicionados',  'notas' => 'Servicio de emergencia 24/7.',    'activo' => true],
            ['nombre' => 'ElectroServ Honduras',     'telefono' => '2236-7890', 'email' => 'electroserv@mail.com','servicio' => 'Reparaciones eléctricas generales',      'notas' => 'Certificados y asegurados.',      'activo' => true],
            ['nombre' => 'VentilaPro',               'telefono' => '2237-8901', 'email' => 'ventilapro@mail.com', 'servicio' => 'Instalación y reparación de ventiladores','notas' => 'Atención los fines de semana.',  'activo' => false],
            ['nombre' => 'AquaServ Plomería',        'telefono' => '2238-9012', 'email' => 'aquaserv@mail.com',   'servicio' => 'Plomería general y reparaciones',        'notas' => 'Presupuesto sin costo.',          'activo' => true],
        ];

        foreach ($providers as $provider) {
            SupportProvider::create($provider);
        }
    }
}
