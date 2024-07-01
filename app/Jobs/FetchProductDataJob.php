<?php

namespace App\Jobs;

use App\Http\Controllers\RapidController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FetchProductDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function handle()
    {
        $rapidController = App::make(RapidController::class);

        $provider = $this->getProviderFromGroupId($this->link->supplier_group_id);
        $productId = $this->extractProductId($this->link->url, $provider);

        if ($productId) {
            $request = new \Illuminate\Http\Request();
            $request->replace([
                'supplier_id' => $this->link->supplier_id,
                'provider' => $provider,
                'product_id' => $productId,
                'product_api_id' => $this->link->product_api_id
            ]);

            $rapidController->fetchData($request);

            // Đánh dấu bản ghi đã được xử lý
            // DB::table('product_supplier_links')
            //     ->where('id', $this->link->id)
            //     ->update(['updated_at' => Carbon::now()]);
        } else {
            \Log::warning("Could not extract product ID from URL: {$this->link->url}");
        }
    }

    protected function getProviderFromGroupId($groupId)
    {
        return $groupId == 1 ? '1688' : 'taobao';
    }

    protected function extractProductId($url, $provider)
    {
        $url1688Pattern = '/offer\/(\d+).html/';
        $urlTaobaoTmallPattern = '/id=(\d+)/';

        if ($provider === '1688') {
            $match = preg_match($url1688Pattern, $url, $matches);
        } else {
            $match = preg_match($urlTaobaoTmallPattern, $url, $matches);
        }

        return $match ? $matches[1] : null;
    }
}
