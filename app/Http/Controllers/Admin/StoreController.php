<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $region = $request->input('region');

        $stores = Store::with('region')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->when($region, fn($q) => $q->where('region_id', $region))
            ->paginate(20);

        return view('admin.stores.index', [
            'stores' => $stores,
            'regions' => Region::all(),
            'search' => $search,
            'filterRegion' => $region,
        ]);
    }

    public function create()
    {
        return view('admin.stores.create', [
            'regions' => Region::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'region_id' => 'required',
            'name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name . ' Store',
            'email' => strtolower(str_replace(' ', '', $request->name)) . '@store.com',
            'password' => Hash::make('store123'), 
            'role' => 'user',
        ]);

        Store::create([
            'region_id' => $request->region_id,
            'name' => $request->name,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store & akun berhasil dibuat');
    }

    public function edit(Store $store)
    {
        $store->load('user');

        return view('admin.stores.edit', [
            'store' => $store,
            'regions' => Region::all()
        ]);
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'region_id' => 'required',
            'name' => 'required|string|max:255',
        ]);

        $store->update($request->all());

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store updated successfully.');
    }

    public function destroy(Store $store)
    {
        if ($store->employees()->exists()) {
            return back()->with(
                'error',
              'Cannot delete store. Employees are still assigned to this store.'
            );
        }

        if ($store->user) {
            $store->user->delete();
        }

        $store->delete();

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store dan akun berhasil dihapus.');
    }

    
    public function resetPassword(Store $store)
    {
        if (!$store->user) {
            return back()->with('error', 'User account not found');
        }

        $defaultPassword = 'password123';

        $store->user->update([
            'password' => Hash::make('store123')
        ]);

        return back()->with(
            'success',
            'Password reset successfully. Default password: store123'
        );
    }
}
