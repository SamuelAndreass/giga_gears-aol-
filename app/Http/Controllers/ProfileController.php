<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\CustomerProfile;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        $profile = CustomerProfile::where('user_id', $user->id)->first();
        $pfp = $user->avatar_path ?? 'images/profile-default.png';
        return view('customer.profile', [
            'user' => $user,
            'pfp' => $pfp,
            'profile' => $profile,
        ]);
    }


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        dd($request->all());
        $user = auth()->user();

        // Tentukan form mana yang dikirim
        $action = $request->input('action');

        /**
         * ========================
         *  UPDATE PROFILE SECTION
         * ========================
         */
        if ($action === 'update_profile') {
            $validated = $request->validateWithBag('updateProfile', [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
            ]);

            $user->update($validated);

            return back()->with('status', 'profile-updated');
        }

        /**
         * ========================
         *  UPDATE PASSWORD SECTION
         * ========================
         */
        if ($action === 'update_password') {
            
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            // Verifikasi password lama
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Your current password is incorrect.'], 'updatePassword');
            }

            // Update password baru
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('status', 'password-updated');
        }

        /**
         * ========================
         *  UPDATE ADDRESS SECTION
         * ========================
         */
        if ($action === 'update_address') {
            $validated = $request->validateWithBag('updateAddress', [
                'address' => ['required', 'string', 'max:255'],
            ]);

            $user->update($validated);

            return back()->with('status', 'address-updated');
        }

        return back()->withErrors(['general' => 'Invalid form submission.']);
    }
    

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
