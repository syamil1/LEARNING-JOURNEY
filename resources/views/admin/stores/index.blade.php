<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stores
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4">

            {{-- FLASH MESSAGE --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search & Filter -->
            <form method="GET" class="flex gap-3 mb-4">

                <input 
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search store name..."
                    class="border px-3 py-2 rounded w-64"
                >

                <select name="region" class="border px-3 py-2 rounded">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}"
                            {{ $filterRegion == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>

                <button class="border px-4 py-2 rounded">
                    Search
                </button>

                <div class="flex items-center ms-auto">
                    <a href="{{ route('admin.stores.create') }}"
                        class="border border-green-700 bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 transition">
                        + Add Store
                    </a>
                </div>

            </form>

            <!-- TABLE -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b bg-gray-100">
                            <th class="p-2 text-left">Store Name</th>
                            <th class="p-2 text-left">Region</th>
                            <th class="p-2 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($stores as $store)
                        <tr class="border-b">
                            <td class="p-2">
                                <div class="font-medium text-gray-800">
                                    {{ $store->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Store ID: {{ $store->id }}
                                </div>
                            </td>

                            <td class="p-2">
                                <div class="font-medium text-gray-800">
                                    {{ $store->region->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Region ID: {{ $store->region->id }}
                                </div>
                            </td>


                            <td class="p-2 flex gap-2 flex-wrap">

                                {{-- EDIT --}}
                                <a href="{{ route('admin.stores.edit', $store->id) }}"
                                    class="px-3 py-1 border border-yellow-500 text-yellow-500 rounded hover:bg-yellow-500 hover:text-white transition">
                                    Edit
                                </a>

                                {{-- RESET PASSWORD --}}
                                @if($store->user)
                                <form method="POST"
                                    action="{{ route('admin.stores.reset-password', $store->id) }}"
                                    onsubmit="return confirm('Reset password to default?');">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition">
                                        Reset Password
                                    </button>
                                </form>
                                @endif

                                {{-- DELETE --}}
                                <form method="POST"
                                    action="{{ route('admin.stores.destroy', $store->id) }}"
                                    onsubmit="return confirm('Delete this store?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="px-3 py-1 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $stores->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
