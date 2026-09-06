<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SysfieldExSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't insert if records already exist
        if (DB::table('sysfield_ex')->count() > 0) {
            $this->command->info('sysfield_ex table already contains data. Skipping seeder.');
            return;
        }

        $jsonPath = database_path('data/sysfield_ex.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("Data file not found: {$jsonPath}");
            return;
        }

        $json = File::get($jsonPath);
        $records = json_decode($json, true);

        if (!is_array($records)) {
            $this->command->error('Invalid JSON format in sysfield_ex.json');
            return;
        }

        // Chunk insert to avoid memory/packet size issues
        $chunks = array_chunk($records, 100);
        foreach ($chunks as $chunk) {
            DB::table('sysfield_ex')->insert($chunk);
        }

        $this->command->info('Successfully seeded sysfield_ex table with ' . count($records) . ' records.');
    }
}
