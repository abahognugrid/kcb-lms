<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/dashboard';

  public function redirectTo()
  {
      return $this->guard()->user()->getRedirectRoute();
  }

    public function showLoginForm()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function logout(Request $request)
  {
    $this->guard()->logout();

    $request->session()->forget('2fa_verified');

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    if ($response = $this->loggedOut($request)) {
      return $response;
    }

    return $request->wantsJson()
      ? new JsonResponse([], 204)
      : redirect('/');
  }

  protected function attemptLogin(Request $request)
  {
    // Attempt to log in with the email and password, and check if the user is active
    return Auth::attempt(
      array_merge($this->credentials($request), ['is_active' => true]),
      $request->filled('remember')
    );
  }

  /**
   * Refresh CSRF token for login page
   *
   * @return JsonResponse
   */
  public function refreshCsrfToken()
  {
    try {
      // Generate a fresh CSRF token
      $token = csrf_token();

      return response()->json([
        'csrf_token' => $token,
        'success' => true,
        'authenticated' => auth()->check(),
        'timestamp' => now()->toISOString()
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'error' => 'Failed to refresh CSRF token',
        'success' => false,
        'timestamp' => now()->toISOString()
      ], 500);
    }
  }

  protected function sendFailedLoginResponse(Request $request)
  {
    // Check if the user exists but is not active
    $user = \App\Models\User::where('email', $request->email)->first();

    if ($user && !$user->is_active) {
      return redirect()->back()
        ->withInput($request->only($this->username(), 'remember'))
        ->withErrors([
          $this->username() => 'Your account has been deactivated. Please contact support for assistance.',
        ]);
    }

    // Default error message for other login failures
    return redirect()->back()
      ->withInput($request->only($this->username(), 'remember'))
      ->withErrors([
        $this->username() => trans('auth.failed'),
      ]);
  }
}
