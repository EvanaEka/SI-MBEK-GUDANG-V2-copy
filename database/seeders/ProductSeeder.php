<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Formula;
use App\Models\Admin;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Buat admin kalau belum ada
        $admin = Admin::factory()->create();

        /*
        |--------------------------------------------------------------------------
        | PRODUK PAKAN (PRODUKSI)
        |--------------------------------------------------------------------------
        */

        $formulaStarter = Formula::factory()->create(['nama_formula' => 'Formula Starter']);
        $formulaGrower  = Formula::factory()->create(['nama_formula' => 'Formula Grower']);

        Product::create([
            'kode' => 'PKN-STARTER',
            'nama' => 'Pakan Starter',
            'harga' => 85000,
            'stok' => 100,
            'rop' => 20,
            'formula_id' => $formulaStarter->id,
            'type' => 'pakan',
            'source' => 'produksi',
            'created_by' => $admin->id,
        ]);

        Product::create([
            'kode' => 'PKN-GROWER',
            'nama' => 'Pakan Grower',
            'harga' => 90000,
            'stok' => 80,
            'rop' => 15,
            'formula_id' => $formulaGrower->id,
            'type' => 'pakan',
            'source' => 'produksi',
            'created_by' => $admin->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | PRODUK OBAT (PEMBELIAN)
        |--------------------------------------------------------------------------
        */

        Product::create([
            'kode' => 'OBT-VITC',
            'nama' => 'Vitamin C Ternak',
            'harga' => 45000,
            'stok' => 50,
            'rop' => 10,
            'formula_id' => null,
            'type' => 'obat',
            'source' => 'pembelian',
            'created_by' => $admin->id,
        ]);

        Product::create([
            'kode' => 'OBT-ANTB',
            'nama' => 'Antibiotik Ternak',
            'harga' => 75000,
            'stok' => 40,
            'rop' => 8,
            'formula_id' => null,
            'type' => 'obat',
            'source' => 'pembelian',
            'created_by' => $admin->id,
        ]);
    }
}