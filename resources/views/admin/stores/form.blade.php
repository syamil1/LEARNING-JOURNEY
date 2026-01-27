<div class="space-y-4">

    <div>
        <label class="block mb-1 font-medium">Region</label>
        <select name="region_id" class="border rounded w-full px-3 py-2">
            <option value="">Select Region</option>
            @foreach($regions as $reg)
                <option value="{{ $reg->id }}"
                    {{ isset($store) && $store->region_id == $reg->id ? 'selected' : '' }}>
                    {{ $reg->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1 font-medium">Store Name</label>
        <input type="text" name="name" class="border rounded w-full px-3 py-2"
            value="{{ $store->name ?? old('name') }}">
    </div>

</div>
