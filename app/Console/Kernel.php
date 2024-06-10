<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Console\Commands\ProcessCompletedOrders;
use App\Jobs\FetchAndStoreProductsJob;

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
                Log::info('Dispatching job for page: ' . $i);
                FetchAndStoreProductsJob::dispatch($i);
            }
        })->cron('* * * * *');//->cron('*/10 7-19 * * *'); // Chạy mỗi 10 phút, trong khoảng từ 7 giờ sáng đến 7 giờ tối

        $schedule->command('orders:process-completed')->everyMinute();//->dailyAt('00:00');// for processing completed orders 
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
