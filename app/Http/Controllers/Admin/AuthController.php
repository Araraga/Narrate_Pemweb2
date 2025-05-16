<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Display admin login view
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming authentication request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || $user->role !== 'admin') {
            return back()->withErrors([
                'email' => 'Kredensial ini tidak memiliki hak akses admin.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            activity_log('login', 'Admin berhasil masuk.', $request);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak sesuai dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */

    public function logout(Request $request)
    {
        activity_log('logout', "Admin berhasil keluar.", $request);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Show the admin profile form.
     */

    public function showProfile()
    {
        return view('admin.profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the admin profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->bio = $request->bio;

        $user->save();

        activity_log('profile_update', 'Admin memperbarui informasi profil.', $request);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    /**
     * Shwo the change password form.
     */
    public function showChangePasswordForm()
    {
        return view('admin.auth.change-password');
    }

    /**
     * Update the user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::find(Auth::id());
        $user->password = Hash::make($request->password);
        $user->save();

        activity_log('password_change', 'Admin mengubah kata sandi.', $request);

        return back()->with('status', 'Password berhasil diperbarui.');
    }
}
