@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-3 lg:px-4" x-data="purchaseSummaryPage()">
    <div class="flex justify-between mb-4">
        <button @click="calculateOrderList" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Tính toán đặt hàng</button>
    </div>
    @include('purchases.partial_purchase_table')
</div>

<script>
    function purchaseSummaryPage() {
        return {
            products: @json($products),
            calculateOrderList() {
                console.log('Tính toán danh sách đặt hàng');
                this.products.forEach(product => {
                    // Sử dụng dữ liệu mẫu để tính toán
                    const demand = product.sales_last_month * 12; // Dự báo nhu cầu hàng năm
                    const orderCost = 50; // Chi phí đặt hàng mỗi lần (giả sử)
                    const holdingCost = 2; // Chi phí lưu kho mỗi đơn vị mỗi năm (giả sử)
                    
                    // Tính EOQ
                    const eoq = Math.sqrt((2 * demand * orderCost) / holdingCost);
                    
                    // Tính mức độ an toàn
                    const z = 1.65; // Hệ số tin cậy cho mức độ tin cậy 95%
                    const stdDev = product.sales_std_dev; // Độ lệch chuẩn của nhu cầu (cần có dữ liệu)
                    const safetyStock = z * stdDev;
                    
                    // Tính số lượng đặt hàng
                    const orderQuantity = eoq + safetyStock;
                    
                    product.order_quantity = Math.ceil(orderQuantity); // Làm tròn lên số lượng đặt
                });
                console.log(this.products);
            }
        };
    }
</script>
@endsection
