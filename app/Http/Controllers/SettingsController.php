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

        return view('seller.store-setting', [
            'user' => $user,
            'store' => $store,
        ]);
    }

    public function updateOwner(UpdateOwnerRequest $request)
    {
            $user = Auth::user();
            $validated = $request->validated();

            DB::beginTransaction();
            try {
                $emailChanged = (isset($validated['owner_email']) && $validated['owner_email'] !== $user->email);
                $user->name = $validated['owner_name'] ?? $user->name;
                $user->email = $validated['owner_email'] ?? $user->email;

                if ($request->hasFile('owner_photo')) {
                    if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                        Storage::disk('public')->delete($user->avatar_path);
                    }

                    $path = $request->file('avatar_path')->store('seller_photos', 'public');
                    $user->avatar_path = $path;
                }

                if ($emailChanged) {
                    $user->email_verified_at = null;
                    // optionally: Notification::send(...) or event(new Registered($user));
                }

                $user->save();
                DB::commit();

                $msg = 'Informasi pemilik berhasil diperbarui.' . ($emailChanged ? ' Mohon cek email untuk verifikasi ulang.' : '');
                return back()->with('success', $msg);
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
                // Map fields: store_name -> name (DB), store_address -> address, store_phone -> phone, store_logo -> store_logo
                $storeData = [];
                if (array_key_exists('store_name', $validated)) {
                    $storeData['store_name'] = $validated['store_name'];
                }
                if (array_key_exists('store_address', $validated)) {
                    $storeData['store_address'] = $validated['store_address'];
                }
                if (array_key_exists('store_phone', $validated)) {
                    $storeData['store_phone'] = $validated['store_phone'];
                }

                if ($request->hasFile('store_logo')) {
                    if ($store->store_logo && Storage::disk('public')->exists($store->store_logo)) {
                        Storage::disk('public')->delete($store->store_logo);
                    }
                    $path = $request->file('store_logo')->store('store_logos', 'public');
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
