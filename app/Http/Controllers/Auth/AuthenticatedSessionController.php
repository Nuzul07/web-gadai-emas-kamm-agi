<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\History;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validate([
            'user_id' => ['required', 'string'],
            'user_password' => ['required', 'string'],
        ]);

        // Attempt login with the given credentials
        if (Auth::attempt(['user_id' => $credentials['user_id'], 'user_password' => $credentials['user_password']])) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            // Check if the user is active
            if (Auth::user()->AKTIF == 1) {
                $latitude = $request->latitude;
                $longitude = $request->longitude;

                // Log user login history
                $history = new History();
                $maxSeq = History::max('id');
                $history->id = $maxSeq + 1;
                $history->user_id = Auth::user()->USER_ID;
                $history->user_name = Auth::user()->USER_NAME;
                $history->login_at = Carbon::now();
                $history->ip_address = $request->ip();

                // Add location if available
                if ($latitude && $longitude) {
                    $history->location = "{$latitude}, {$longitude}";
                }

                $history->save();

                // Redirect to dashboard
                return redirect()->route('gadaiDashboard');
            } else {
                // If the account is inactive, log out the user
                Auth::logout();
                return back()->withErrors([
                    'user_id' => 'Akun sudah tidak aktif.',
                ]);
            }
        } else {
            // If credentials are invalid, return with an error
            return back()->withErrors([
                'user_id' => 'Username atau Password Salah',
            ]);
        }


        throw ValidationException::withMessages([
            'user_id' => [trans('auth.failed')],
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $history = History::where('user_id', Auth::user()->USER_ID)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();
        if ($history) {
            $history->logout_at = Carbon::now();
            $history->update();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
