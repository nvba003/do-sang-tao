<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

<?php

namespace App\Http\Controllers;

use App\Models\Dlsapoweb;
use App\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DlsapowebController extends Controller
{
    public function laydulieu()//mẫu- không dùng nữa
    {
        $spikey = "4ccfe3d9305b4288bb2b5cf9184c8e5d";
        $apisecret = "c9830e0a36b348c786f8df30a72d75c8";
        $GetProducts = "@do-vat-sang-tao.mysapo.net/admin/products";
        $Fields = ".json?fields=image,name,variants,product_type,alias";
        $Indexs = "&limit=179&page=";
        $Page = "3";
        $Count = "/count.json";
        $LinkGetdataSapo = 'https://'.$spikey.':'.$apisecret.$GetProducts.$Fields.$Indexs.$Page;

        //$client = new \GuzzleHttp\Client();
        //$response = $client->request('GET', $LinkGetdataSapo);
        //dd($response->getBody());
        //echo $response->getStatusCode(); // 200
        //echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        // $dulieu = json_decode($response->getBody()); // '{"id": 1420053, "name": "guzzle", ...}'
        //return $response->getBody().products;
        //var_dump($dulieu);
        $response = Http::get('https://4ccfe3d9305b4288bb2b5cf9184c8e5d:c9830e0a36b348c786f8df30a72d75c8@do-vat-sang-tao.mysapo.net/admin/products.json');
        $dulieu = json_decode($response);
        return $dulieu;
    }

    public function laydulieusapoweb($page)
    {
        $spikey = "4ccfe3d9305b4288bb2b5cf9184c8e5d";
        $apisecret = "c9830e0a36b348c786f8df30a72d75c8";
        $GetProducts = "@do-vat-sang-tao.mysapo.net/admin/products";
        $Fields = ".json?fields=image,name,variants,product_type,alias";
        $Indexs = "&limit=250&page=";//giới hạn 250 bản ghi
        //$Page = "2";
        //$Count = "/count.json";
        $LinkGetdataSapo = 'https://'.$spikey.':'.$apisecret.$GetProducts.$Fields.$Indexs.$page;

        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('GET', $LinkGetdataSapo)->getBody();
        $response = Http::get($LinkGetdataSapo);//lấy dữ liệu nhanh hơn new Guzzle 0.mấy giây
        $dulieu = json_decode($response); // '{"id": 1420053, "name": "guzzle", ...}'
        return $dulieu;
    }

    public function tachsanpham()
    {
        
        $sosanpham = 'https://4ccfe3d9305b4288bb2b5cf9184c8e5d:c9830e0a36b348c786f8df30a72d75c8@do-vat-sang-tao.mysapo.net/admin/products/count.json';
        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('GET', $sosanpham)->getBody();
        $response = Http::get($sosanpham);
        $count = json_decode($response)->count;//lấy tổng số sản phẩm chính
        $page = ceil($count/250);//tính số trang
        $dlweb = collect();
        $id_old_dlweb = DB::table('dlsapowebs')->get()->keyBy('id');//lấy dữ liệu bảng Sapoweb cũ => nhóm theo id
        //dd($old_dlweb);
        //DLsapoweb::truncate();//xóa dữ liệu cũ để ghi mới dữ liệu => dữ liệu không ổn định nên không dùng
        for ($i = 1; $i <= $page; $i++) {//chia nhỏ để ghi dữ liệu với 250 bản ghi/lần có thời gian tối ưu nhất
            $kq = collect();//tạo mảng php trống
            $dulieu = $this->laydulieusapoweb($i)->products;
            foreach ($dulieu as $value) {
                if(empty($value->image)){
                    $hinhanh = '';
                }
                else{
                    $hinhanh = $value->image->src;
                }
                foreach ($value->variants as $giatri) {
                    $kq->push([
                        'id'        => $giatri->id,
                        'sku'       => $giatri->sku,
                        'tensanpham'=> $giatri->title == "Default Title" ? $value->name : $value->name .' '. $giatri->title,
                        'loai'      => $value->product_type,
                        'hinhanh'   => $hinhanh,
                        'alias'     => $value->alias,
                        'soluong'   => $giatri->inventory_quantity,
                        'giaban'    => $giatri->price,
                        'cannang'   => $giatri->weight,
                        'updated_at'=> Carbon::now('Asia/Ho_Chi_Minh')
                    ]);

                }//end foreach $value
            }//end foreach $dulieu
            $dlweb = $dlweb->concat($kq);//tổng hợp dữ liệu từng trang
            //-------cập nhật dữ liệu bảng sapoweb--------
            DB::table('dlsapowebs')->upsert($kq->all(), //hoặc $kq->toArray() đều được
                ['id'], ['sku','tensanpham','loai','hinhanh','alias','soluong','giaban','cannang','updated_at']);
           
            //-------cập nhật dữ liệu bảng Import--------
            $data_import = $kq->map(function ($item, $key) {//thay đổi mảng dữ liệu để cập nhật mới bảng Import
                return [
                    'sapo_id'    => $item['id'],
                    'sku'        => $item['sku'], 
                    'tensanpham' => $item['tensanpham'],
                    'hinhanh'    => $item['hinhanh'],
                    'slsapo'     => $item['soluong'],
                    'giaban'     => $item['giaban']
                ];
            });
            DB::table('imports')->upsert($data_import->all(), 
                ['sapo_id'], ['sku','tensanpham','hinhanh','slsapo','giaban']);//cập nhật nếu đã có id, thêm mới nếu chưa có

            //-------cập nhật dữ liệu bảng Salebook--------
            $data_new = $kq->map(function ($item, $key) {//thay đổi mảng dữ liệu để cập nhật mới bảng Salebook
                return [
                    'sapo_id'    => $item['id'],
                    'sku'        => $item['sku'], 
                    'tensanpham' => $item['tensanpham'],
                    'slsapo'     => $item['soluong']
                ];
            });
            DB::table('salebooks')->upsert($data_new->all(), 
                ['sapo_id'], ['sku','tensanpham','slsapo']);//cập nhật nếu đã có id, thêm mới nếu chưa có
            //------------------------------------------------

        }//end for

        $id_sapo = $dlweb->keyBy('id');//tạo thành mảng dạng [id=>[.....],id=>[.....]]

        $diff_web =  $id_old_dlweb->diffKeys($id_sapo);//lấy id dlweb cũ có nhưng dlweb mới không có
        if($diff_web->count() > 0){//nếu có mới chạy
            $edit_diff_web = $diff_web->map(function ($item, $key) {//diff_web là array of object
                return [
                    'sku_error'  => $item->sku,
                    'sapo_id'    => $item->id, 
                    'tensanpham' => $item->tensanpham,
                    'tenbang'    => 'dlsapowebs',
                    'vande'      => 'has_old_no_new'
                ];
            });
            $dataweb_diff = collect($edit_diff_web->values());//đặt lại mảng khóa chính số nguyên tăng dần
            DB::table('producterrors')->upsert($dataweb_diff->all(), 
            ['sapo_id','tenbang'], ['sku_error','tensanpham','vande']);//thêm mới dữ liệu nếu chưa có, cập nhật nếu có rồi, chỉ tác dụng khi cột "sapo_id" unique
        }//kết thúc tìm id web cũ có nhưng web mới không có

        $sku_trung = $id_sapo->duplicates('sku');//tìm dữ liệu sku trùng trong Dlsapoweb
        if($sku_trung->count() > 0){//nếu có sku trùng thì mới chạy
            foreach ($sku_trung as $key => $value){
                $find_sku = $id_sapo->get($key);//tìm id trong mảng $id_sapo (mảng này trả về dữ liệu sapoweb của id này)
                if(!is_null($find_sku)){//nếu tìm thấy id thì cập nhật ghi lại
                    $edit_find_sku = [
                        'sku_error'  => $find_sku['sku'],
                        'sapo_id'    => $find_sku['id'], 
                        'tensanpham' => $find_sku['tensanpham'],
                        'tenbang'    => 'dlsapowebs',
                        'vande'      => 'duplicate_sku'
                    ];//sửa lại cấu trúc dữ liệu để lưu vào bảng ProductError
                    DB::table('producterrors')->upsert($edit_find_sku, 
                    ['sapo_id','tenbang'], ['sku_error','tensanpham','vande']);//add data if have no, update if already, chỉ tác dụng khi cột "sapo_id" primary
                }
            }
        }//kết thúc tìm dữ liệu sku trùng
        return $dlweb;
    }



    public function kiemtraSalebook($id_sapo)
    {
        $salebook = DB::table('salebooks')->get();//lấy dữ liệu bảng salebook
        $id_salebook = $salebook->keyBy('sapo_id');//tạo thành mảng dạng [sapo_id=>[.....],sapo_id=>[.....]]
        
        //------------Thêm sp cần kiểm tra vào bảng ProductWithoutSapoId nếu Salebook có nhưng dlweb chưa có---------------
        //-----thường do sản phẩm gốc bị xóa, hiện tại không còn trên Sapoweb => cần cập nhật lại sku mới của id này-----
        $diff =  $id_salebook->diffKeys($id_sapo);//lấy id salebook có nhưng web không có
        if($diff->count() > 0){//nếu có mới chạy
            $keyed = $diff->map(function ($item, $key) {//diff_2 là array of object do chưa xử lý $id_salebook toàn bộ về mảng php
                return [
                    'sku_error'  => $item->sku,
                    'sapo_id'    => $item->sapo_id, 
                    'tensanpham' => $item->tensanpham,
                    'tenbang'    => 'salebooks',
                    'vande'      => 'find_no_id_in_web'
                ];
            });
            $datadiff = collect($keyed->values());//đặt lại mảng khóa chính số nguyên tăng dần
            DB::table('producterrors')->upsert($datadiff->all(), 
            ['sapo_id','tenbang'], ['sku_error','tensanpham','vande']);//thêm mới dữ liệu nếu chưa có, cập nhật nếu có rồi, chỉ tác dụng khi cột "sapo_id" unique
        }
        //----------kết thúc thêm sp cần kiểm tra
    }

    public function kiemtraThunghang($id_sapo)
    {
        $dlthunghang = DB::table('dlthunghangs')->get();//lấy dữ liệu bảng dlthunghang
        $id_dlthunghang = $dlthunghang->keyBy('sapo_id');//tạo thành mảng dạng [sapo_id=>[.....],sapo_id=>[.....]]
        
        //------------Thêm sp cần kiểm tra vào bảng ProductWithoutSapoId nếu Dlthunghang có nhưng dlweb chưa có---------------
        //-----thường do sản phẩm gốc bị xóa, hiện tại không còn trên Sapoweb => cần cập nhật lại sku mới của id này-----
        $diff =  $id_dlthunghang->diffKeys($id_sapo);//lấy id dlthunghang có nhưng web không có
        if($diff->count() > 0){//nếu có mới chạy
            $keyed = $diff->map(function ($item, $key) {//diff_2 là array of object do chưa xử lý $id_dlthunghang toàn bộ về mảng php
                return [
                    'sku_error'  => $item->sku,
                    'sapo_id'    => $item->sapo_id, 
                    'tensanpham' => $item->tensanpham,
                    'tenbang'    => 'dlthunghangs',
                    'vande'      => 'find_no_id_in_web'
                ];
            });
            $datadiff = collect($keyed->values());//đặt lại mảng khóa chính số nguyên tăng dần
            DB::table('producterrors')->upsert($datadiff->all(), 
            ['sapo_id','tenbang'], ['sku_error','tensanpham','vande']);//thêm mới dữ liệu nếu chưa có, cập nhật nếu có rồi, chỉ tác dụng khi cột "sapo_id" unique
        }
        //----------kết thúc thêm sp cần kiểm tra
    }

    public function kiemtraImport($id_sapo)
    {
        $import = DB::table('imports')->get();//lấy dữ liệu bảng import
        $id_import = $import->keyBy('sapo_id');//tạo thành mảng dạng [sapo_id=>[.....],sapo_id=>[.....]]
        
        // //------------cập nhật sku import nếu sku sapo web thay đổi (sku thay đổi nhưng id không thay đổi---------------
        // $only_id_import = $id_import->keys();//lấy các giá trị cột id_import
        // $sku_import = $import->pluck('sku');//lấy các giá trị cột sku
        // $combined_import = $sku_import->combine($only_id_import);//tạo thành mảng mới kiểu [sku=>id,sku=>id]

        // $sku_sapo = $dlweb->pluck('sku');//lấy các giá trị cột sku_sapoweb
        // $hasimport_nosapo = $combined_import->except($sku_sapo);//lọc sku import có nhưng web không có
        // if($hasimport_nosapo->count() > 0 ){//nếu tìm thấy có sku khác mới chạy
        //     foreach ($hasimport_nosapo as $item){
        //         $new_sku = $id_sapo->get($item);//tìm id trong mảng $id_sapo (mảng này trả về dữ liệu sapoweb của id này)
        //         if(!is_null($new_sku)){//nếu tìm thấy id thì cập nhật sku mới
        //             DB::table('imports')->where('sapo_id',$item)->update(['sku'=> $new_sku['sku']]);//update sku mới
        //         }
        //     }
        // }
        // //---------kết thúc cập nhật sku import cũ => mới
        
        //------------Thêm sp cần kiểm tra vào bảng ProductError nếu Import có nhưng dlweb chưa có---------------
        //-----thường do sản phẩm gốc bị xóa, hiện tại không còn trên Sapoweb => cần cập nhật lại sku mới của id này-----
        $diff =  $id_import->diffKeys($id_sapo);//lấy id import có nhưng web không có
        if($diff->count() > 0){//nếu có mới chạy
            $keyed = $diff->map(function ($item, $key) {//diff_2 là array of object do chưa xử lý $id_import toàn bộ về mảng php
                return [
                    'sku_error'  => $item->sku,
                    'sapo_id'    => $item->sapo_id, 
                    'tensanpham' => $item->tensanpham,
                    'tenbang'    => 'imports',
                    'vande'      => 'find_no_id_in_web'
                ];
            });
            $datadiff = collect($keyed->values());//đặt lại mảng khóa chính số nguyên tăng dần
            DB::table('producterrors')->upsert($datadiff->all(), 
            ['sapo_id','tenbang'], ['sku_error','tensanpham','vande']);//thêm mới dữ liệu nếu chưa có, cập nhật nếu có rồi, chỉ tác dụng khi cột "sapo_id" unique
        }
        //----------kết thúc thêm sp cần kiểm tra
    }

    public function kiemtraProductError($dlweb)
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Dlsapoweb::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()//đang sử dụng để test
    {
        $arr = '[{"id":"13","tensanpham":"2","hinhanh":"3","alias":"4"},{"id":"12","tensanpham":"2","hinhanh":"3","alias":"4"}]';
        //$array = json_encode(json_decode($arr,true));
        $array = json_decode($arr,true);
        DLsapoweb::insert($array);
        return $array;

        // $array = [//mảng chuẩn php để lưu vào database
        //     [
        //           "id" => "8", 
        //           "tensanpham" => "2", 
        //           "hinhanh" => "3", 
        //           "alias" => "4" 
        //        ], 
        //     [
        //              "id" => "9", 
        //              "tensanpham" => "2", 
        //              "hinhanh" => "3", 
        //              "alias" => "4" 
        //           ] 
        //  ]; 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DLsapoweb::truncate();//xóa dữ liệu cũ để ghi mới dữ liệu
        $dulieu = json_decode($request->data,true);//chuyển đổi mảng json thành mảng php, tham khảo kiểu dl tại https://jsontophp.com/
        //dd($dulieu);
        DLsapoweb::insert($dulieu);
        return $dulieu;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dlsapoweb  $dlsapoweb
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //dd($request->get('id'));
        //dd($request->get('q'));
        $q = $request->get('q');//lấy param query
        //dd($q);
        $tenbang = DB::table('dlsapowebs')->where('tensanpham', 'like',  '%' . $q . '%')->get()->first();
        //dd($tenbang);
        return $tenbang;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dlsapoweb  $dlsapoweb
     * @return \Illuminate\Http\Response
     */
    public function edit(Dlsapoweb $dlsapoweb)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dlsapoweb  $dlsapoweb
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dlsapoweb $dlsapoweb)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dlsapoweb  $dlsapoweb
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dlsapoweb $dlsapoweb)
    {
        //
    }
}
