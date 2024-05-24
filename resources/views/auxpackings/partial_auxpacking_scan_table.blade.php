@forelse ($scans as $scan)
    <tr class="bg-white hover:bg-gray-100 border-b" data-scan-id="{{ $scan->scan_code }}">
        <td class="p-2 w-1/24 text-center mt-1 hidden sm:block"> <!-- Reduced padding and set a fixed width -->
            <input type="checkbox" class="checkItem">
        </td>
        <td class="text-center w-1/24 cursor-pointer">
            <button class="expand-button text-xs md:text-lg sm:text-base xl:w-1/2 w-full h-full text-center text-gray-200 bg-cyan-500 rounded" data-target="#details{{ $scan->id }}">
                +
            </button>
        </td>
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $scan->created_at }}</td>
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $scan->tracking_number }}</td>
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $scan->user_id }}</td>
        <td class="w-2/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $scan->platform_id }}</td>
        <td class="w-12/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $scan->order_id }}</td>
        <td class="text-center w-1/24 cursor-pointer">
            <button class="remove-scan text-xs md:text-lg sm:text-base xl:w-1/2 w-full h-full text-center text-gray-200 bg-cyan-500 rounded" data-target="#details{{ $scan->id }}">
                Xóa
            </button>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="100%" class="text-center py-3 text-xs md:text-lg sm:text-base">Không tìm thấy đơn đã quét nào.</td>
    </tr>
@endforelse
