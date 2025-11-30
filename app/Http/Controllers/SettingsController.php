<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOwnerRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Registered; // optional for re-verification
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $store = $user->sellerStore;

        if (! $store) abort(404, 'Store tidak ditemukan.');

        return view('seller.settings.index', [
            'user' => $user,
            'store' => $store,
        ]);
    }

    /**
     * Update owner (user) fields: name, email, phone
     * If email changed -> set email_verified_at = null and dispatch verification (optional)
     */
    public function updateOwner(UpdateOwnerRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $emailChanged = $validated['email'] !== $user->email;
            $user->name = $validated['owner_name'];
            $user->email = $validated['email'];
            if($request->hasFile('owner_photo')){
                // Hapus foto lama bila ada
                if ($user->owner_photo && Storage::disk('public')->exists($user->owner_photo)) {
                    Storage::disk('public')->delete($user->owner_photo);
                }

                $path = $request->file('owner_photo')->store('seller_photos', 'public');
                $user->owner_photo = $path;
            }

            if ($emailChanged) {
                // invalidate verification and optionally send new verification
                $user->email_verified_at = null;
            }

            $user->save();
            DB::commit();

            return back()->with('success', 'Informasi pemilik berhasil diperbarui.' . ($emailChanged ? ' Mohon cek email untuk verifikasi ulang.' : ''));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('UpdateOwner error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Gagal memperbarui data pemilik. Silakan coba lagi.']);
        }
    }

    /**
     * Update store (toko) fields: name, address, avatar.
     * Avatar uploaded to storage/app/public/store_avatars
     */
    public function updateStore(UpdateStoreRequest $request)
    {
        $user = Auth::user();
        $store = $user->sellerStore;

        if (! $store) {
            return back()->withErrors(['store' => 'Store tidak ditemukan.']);
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $storeData = [
                'name' => $validated['store_name'],
                'address' => array_key_exists('address', $validated) ? $validated['address'] : $store->address,
                'phone' => array_key_exists('store_phone', $validated) ? $validated['store_phone'] : $store->phone,
            ];

            if ($request->hasFile('store_logo')) {
                // Hapus avatar lama bila ada
                if ($store->store_logo && Storage::disk('public')->exists($store->store_logo)) {
-                    Storage::disk('public')->delete($store->store_logo);
                }

                $path = $request->file('store_logo')->store('store_logo', 'public');
                $storeData['store_logo'] = $path;
            }

            $store->update($storeData);

            DB::commit();

            return back()->with('success', 'Detail toko berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('UpdateStore error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Gagal memperbarui data toko. Silakan coba lagi.']);
        }
    }

    /**
     * Change password for current user
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
