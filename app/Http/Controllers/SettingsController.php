<?php

namespace App\Http\Controllers\Seller;

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
            $emailChanged = array_key_exists('email', $validated) && $validated['email'] !== $user->email;

            $user->name = $validated['owner_name'];
            $user->email = $validated['email'];
            if (array_key_exists('phone', $validated)) {
                // pastikan kolom phone ada di users table
                $user->phone = $validated['phone'];
            }

            if ($emailChanged) {
                // invalidate verification and optionally send new verification
                $user->email_verified_at = null;
            }

            $user->save();

            DB::commit();

            // Optional: send verification email if email changed (depends on app setup)
            if ($emailChanged) {
                // Jika menggunakan Laravel's MustVerifyEmail:
                if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
                    $user->sendEmailVerificationNotification();
                }
            }

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
                'address' => $validated['address'] ?? $store->address,
            ];

            if ($request->hasFile('avatar')) {
                // Hapus avatar lama bila ada
                if ($store->avatar_path && Storage::disk('public')->exists($store->avatar_path)) {
                    Storage::disk('public')->delete($store->avatar_path);
                }

                $path = $request->file('avatar')->store('store_avatars', 'public');
                $storeData['avatar_path'] = $path;
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
