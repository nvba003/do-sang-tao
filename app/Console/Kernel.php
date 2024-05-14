<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $countProduct = 'https://4ccfe3d9305b4288bb2b5cf9184c8e5d:c9830e0a36b348c786f8df30a72d75c8@do-vat-sang-tao.mysapo.net/admin/products/count.json';
            $response = Http::get($countProduct);
            $count = json_decode($response->body())->count;
            $pages = ceil($count / 250);
    
            for ($i = 1; $i <= $pages; $i++) {
                FetchAndStoreProductsJob::dispatch($i);
            }
        })->everyTenMinutes()->between('7:00', '23:00'); // everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
