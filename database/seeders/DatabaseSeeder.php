<?php

namespace Database\Seeders;

use Closure;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\GedungSeeder;
use Database\Seeders\PegawaiSeeder;
use Database\Seeders\RuanganSeeder;
use Database\Seeders\SuplayerSeeder;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\KategoriBarangSeeder;
use Database\Seeders\SumberAnggaranSeeder;
use Illuminate\Database\Eloquent\Collection;
use Database\Seeders\KetegoriInventarisSeeder;
use Symfony\Component\Console\Helper\ProgressBar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        $this->command->warn(PHP_EOL . 'Creating superadmin...');
        $this->withProgressBar(1, fn() => User::factory(1)->create([
            'name' => 'Superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@larafila.com',
            'password' => bcrypt('12345678'),
        ]));
        $this->command->info('Superadmin user has been created successfully.');

        $this->command->warn(PHP_EOL . 'Set user superadmin as role superadmin...');
        Artisan::call('shield:super-admin');
        $this->command->info('The superadmin user has been successfully configured.');

        $this->command->warn(PHP_EOL . 'Generate all permissions...');
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
        ]);
        $this->command->info('Permissions have been generated.');

        $this->call([
            KategoriSuratSeeder::class,
            TahunAjaranSeeder::class,
            UnitSeeder::class,
            JabatanSeeder::class,
            JarakTempuhSeeder::class,
            PekerjaanOrtuSeeder::class,
            PendidikanOrtuSeeder::class,
            PenghasilanOrtuSeeder::class,
            TransportSeeder::class,
            KelasSeeder::class,
            StatusSiswaSeeder::class,
            PegawaiSeeder::class,
            GedungSeeder::class,
            KategoriBarangSeeder::class,
            SumberAnggaranSeeder::class,
            KategoriInventarisSeeder::class,
            RuanganSeeder::class,
            SuplayerSeeder::class,
        ]);
    }

    protected function withProgressBar(int $amount, Closure $createCollectionOfOne): Collection
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $amount);
        $progressBar->start();
        $items = new Collection();

        foreach (range(1, $amount) as $i) {
            $items = $items->merge(
                $createCollectionOfOne()
            );
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->getOutput()->writeln('');

        return $items;
    }
}
