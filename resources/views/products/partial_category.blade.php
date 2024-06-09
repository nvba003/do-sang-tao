<tr>
    <td class="border px-4 py-2">{{ $category->id }}</td>
    <td class="border px-4 py-2" style="padding-left: {{ $level * 20 }}px;">{{ $category->name }}</td>
    <td class="border px-4 py-2">{{ $category->definition_id }}</td>
    <td class="border px-4 py-2">{{ $category->parent ? $category->parent->name : '_' }}</td>
    <td class="border px-4 py-2">{{ $category->notes }}</td>
    <td class="border px-4 py-2">
        <button onclick="editCategory({{ $category }})" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">Sửa</button>
        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Xóa</button>
        </form>
    </td>
</tr>
@foreach($category->children as $child)
    @include('products.partial_category', ['category' => $child, 'level' => $level + 1])
@endforeach
