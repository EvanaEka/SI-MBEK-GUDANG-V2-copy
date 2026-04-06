<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User dasar
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@peternakan.com',
            'password' => Hash::make('Password'),
        ]);

        // 2. Setting & master
        $this->call([
            SiteSettingSeeder::class,
            SupplierSeeder::class,
            MaterialSeeder::class,
            OwnerSeeder::class,
        ]);

        // 3. Role / user level
        $this->call([
            SuperAdminSeeder::class,
            AdminSeeder::class,
        ]);

        // 4. Data utama
        $this->call([
            DombaSeeder::class,
            KambingSeeder::class,
            ProductSeeder::class,
        ]);

        // 5. History (TERAKHIR)
        $this->call([
            DombaHistorySeeder::class,
            KambingHistorySeeder::class,
            PurchaseOrderSeeder::class,
        ]);

        //6. Indikator QC
        $this->call([
            QcIndicatorSeeder::class,
            ProductionQcSeeder::class,
            OrderSeeder::class,
            StockMovementSeeder::class,
        ]);
    }

}
