<tr>
    <td class="border px-4 py-2">{{ $location->id }}</td>
    <td class="border px-4 py-2" style="padding-left: {{ $level * 20 }}px;">{{ $location->location_name }}</td>
    <td class="border px-4 py-2">{{ $location->description }}</td>
    <td class="border px-4 py-2">{{ $location->parent ? $location->parent->location_name : '_' }}</td>
    <td class="border px-4 py-2">
        <button onclick="editLocation({{ $location }})" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">Sửa</button>
        <form action="{{ route('locations.destroy', $location->id) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Xóa</button>
        </form>
    </td>
</tr>
@foreach($location->children as $child)
    @include('containers.partial_location', ['location' => $child, 'level' => $level + 1])
@endforeach
