<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        $profile = CustomerProfile::where('user_id', $user->id)->first();
        $pfp = $user->avatar_path;
        return view('customer.profile', [
            'user' => $user,
            'pfp' => $pfp,
            'profile' => $profile,
        ]);
    }


    /**
     * Update the user's profile information.
     */
     public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'phone' => ['nullable','string','max:20'],
            'address' => ['nullable','string','max:255'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'updateProfile')->withInput();
        }

        $user->update($validator->validated());

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'updatePassword')->withInput();
        }

        // cek current password
        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'], 'updatePassword')
                ->withInput();
        }

        // update password
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // optional: regenerate session to avoid fixation / logout other sessions
        // $request->session()->regenerate();

        return back()->with('status', 'password-updated');
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'address' => ['required','string','max:255'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'updateAddress')->withInput();
        }

        $user->update(['address' => $validator->validated()['address']]);

        return back()->with('status', 'address-updated');
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
