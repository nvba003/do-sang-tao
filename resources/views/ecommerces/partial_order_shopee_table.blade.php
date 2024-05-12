@forelse ($orders as $order)
    <tr class="bg-white hover:bg-gray-100 border-b" data-order-id="{{ $order->id }}">
        <td class="p-2 w-1/24 text-center mt-2 hidden sm:block">
            <input type="checkbox" class="checkItem">
        </td>
        <td class="text-center w-1/24 cursor-pointer">
            <button class="text-xs md:text-sm sm:text-base xl:w-1/2 w-full h-full text-center text-gray-200 bg-cyan-500 rounded" @click="toggleDetails({{ $order->id }})">
                <span x-text="openDetails.includes({{ $order->id }}) ? '-' : '+'">+</span>
            </button>
        </td>
        <td class="w-3/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->order_code }}</td>
        <td class="w-2/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->customer_account }}</td>
        <td class="w-2/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->customer_phone }}</td>
        <td class="w-3/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->customer_address }}</td>
        <td class="w-2/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->total_amount }}</td>
        <td class="w-3/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->carrier }}</td>
        <!-- <td class="w-3/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->tracking_number }}</td> -->
        <td class="w-6/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $order->order_date }}</td>
        <!-- <td class="w-1/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">
            <span>{{ $order->order_id }}</span>
            @if($order->order_id)
                <span class="text-green-500 ml-1">&#10004;</span>
            @endif
        </td> -->
        <td id="order-id-{{ $order->id }}" class="w-1/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">
            <span>{{ $order->order_id }}</span>
            @if ($order->order_id)
                <span class="text-green-500 ml-1">&#10004;</span>
            @endif
        </td>
    </tr>
    <tr id="details{{ $order->id }}" class="bg-gray-300" x-show="openDetails.includes({{ $order->id }})" x-cloak>
        <td colspan="100%" class="px-2 sm:px-4 py-2 text-xs md:text-base">
            <div class="flex items-center space-x-4 mb-4">
                <div class="flex items-center space-x-2">
                    <label for="carrier_{{ $order->id }}" class="text-gray-700">NVC:</label>
                    <select id="carrier_{{ $order->id }}" class="bg-white text-sm rounded py-2 px-6">
                        <option value="">Chọn</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}" {{ $order->order && $order->order->orderProcess->carrier_id == $carrier->id ? 'selected' : '' }}>
                                {{ $carrier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label for="tracking_{{ $order->id }}" class="text-gray-700">Vận đơn:</label>
                    <input type="text" id="tracking_{{ $order->id }}" class="bg-white text-sm rounded p-2" value="{{ $order->order ? $order->order->orderProcess->tracking_number : '' }}">
                </div>
                <div class="flex items-center space-x-2">
                    <label for="responsible_{{ $order->id }}" class="text-gray-700">Phụ trách:</label>
                    <select id="responsible_{{ $order->id }}" class="bg-white text-sm rounded py-2 px-8">
                        <option value="">Chọn</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                            {{ (Auth::check() && Auth::user()->id == $user->id) || ($order->order && $order->order->orderProcess->responsible_user_id == $user->id) ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                        <!-- @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $order->order && $order->order->orderProcess->responsible_user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach -->
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label for="notes_{{ $order->id }}" class="text-gray-700">Ghi chú:</label>
                    <input type="text" id="notes_{{ $order->id }}" class="bg-white text-sm rounded p-2" value="{{ $order->order ? $order->order->notes : '' }}">
                </div>
                <div>
                    <button class="bg-green-600 hover:bg-green-800 text-white py-2 px-4 rounded mt-5 md:mt-0" @click="sendOrder({{ $order }})">Tạo/Cập nhật</button>
                </div>
                <div class="flex items-center space-x-2">
                    <label for="platform_{{ $order->id }}" class="text-gray-700">Kênh BH:</label>
                    <select id="platform_{{ $order->id }}" class="bg-white text-sm rounded py-2 px-6">
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}" {{ $order->order && $order->platform_id == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-cyan-500 text-white">
                    <tr>
                        <th class="py-2 px-4 text-left">SKU Sendo</th>
                        <th class="py-2 px-4 text-left">SKU Sản phẩm</th>
                        <th class="py-2 px-4 text-left">Tên Sản Phẩm</th>
                        <th class="py-2 px-4 text-left">Image</th>
                        <th class="py-2 px-4 text-left">SL</th>
                        <th class="py-2 px-4 text-left">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->details as $detail)
                        <tr class="border-t border-gray-200">
                            <td class="py-2 px-4">{{ $detail->sku }}</td>
                            <td class="py-2 px-4">
                                <input type="text" id="product_{{ $detail->id }}" class="autocomplete-product bg-gray-200 text-sm rounded p-2" value="{{ $detail->product ? $detail->product->sku : '' }}" data-detail-id="{{ $detail->id }}" data-order-id="{{ $order->id }}" data-product-id="{{ $detail->product ? $detail->product_api_id : '' }}" data-initial-product-id="{{ $detail->product ? $detail->product->id : '' }}" require>
                            </td>
                            <td class="py-2 px-4">{{ $detail->name }}</td>
                            <td class="py-2 px-4">
                                <img src="{{ $detail->image }}" alt="{{ $detail->name }}" class="w-16 h-16 object-cover rounded-lg">
                            </td>
                            <td class="py-2 px-4" data-quantity="{{ $detail->quantity }}">{{ $detail->quantity }}</td>
                            <td class="py-2 px-4">{{ number_format($detail->price) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-xs md:text-lg sm:text-base">Không có chi tiết đơn hàng.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-3 text-xs md:text-lg sm:text-base">Không tìm thấy thông tin nào.</td>
    </tr>
@endforelse