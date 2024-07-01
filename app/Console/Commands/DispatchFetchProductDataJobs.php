<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchProductDataJob;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DispatchFetchProductDataJobs extends Command
{
    protected $signature = 'dispatch:fetchproductdatajobs';
    protected $description = 'Dispatch jobs to fetch product data from API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Dispatching fetch product data jobs...');

        $links = DB::table('product_supplier_links')
            ->whereNull('updated_at')
            ->limit(10)
            ->get();

        foreach ($links as $link) {
            FetchProductDataJob::dispatch($link);
        }

        // Đặt lại `updated_at` về null khi đã xử lý hết danh sách
        $remaining = DB::table('product_supplier_links')
            ->whereNull('updated_at')
            ->count();

        if ($remaining == 0) {
            DB::table('product_supplier_links')
                ->update(['updated_at' => null]);
            $this->info('All records processed. Resetting updated_at to null.');
        }

        $this->info('Fetch product data jobs dispatched successfully.');
    }
}
