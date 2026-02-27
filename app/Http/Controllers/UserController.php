<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Mail\TempPasswordMail;
use App\Models\Role;
use App\Models\User;
use App\Models\Partner;
use App\Notifications\TempPasswordNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FAQRCode\Google2FA;

class UserController extends Controller
{
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function index(Request $request)
    {
        $query = User::withTrashed(); // includes both active and soft deleted users
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }
        $user = Auth::user();
        $query->orderBy('id', 'desc');
        if ($user->is_admin) {
            $users = $query->paginate(10)->appends(['search' => $request->search]);
        } else {
            $users = $query->where('partner_id', $user->partner_id)->paginate(10)->appends(['search' => $request->search]);
        }
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
            $roles = Role::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
            $roles = Role::where('name', '!=', 'Super Admin')->where('partner_id', $user->partner_id)->get();
        }
        return view('users.create', compact('partners', 'roles'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'role_id' => 'required|numeric',
                'email' => 'required|string|email|max:255|unique:users',
                // No password validation since it will be generated
            ]);
            $role = Role::find($request->role_id);

            // Generate a random password
            $randomPassword = bin2hex(random_bytes(8)); // 16 chars

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($randomPassword),
                'partner_id' => $request->partner_id,
                'role_id' => $role->id,
            ]);
            $user->assignRole($role->name);
            if (app()->isProduction()) {
                Mail::to($user->email)->send(new TempPasswordMail(
                    $randomPassword,
                    $user->email
                ));
            } else {
                Log::info('User added successfully and message sent successfully');
            }
            return redirect()->route('users.index')->with('success', 'User created successfully. Password: ' . $randomPassword);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser->partner_id !== null && $loggedInUser->partner_id !== $user->partner_id) {
            abort(403, 'You are not authorized to edit this user.');
        }

        if ($loggedInUser->is_admin) {
            $partners = Partner::all();
            $roles = Role::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
            $roles = Role::where('name', '!=', 'Super Admin')->where('partner_id', $user->partner_id)->get();
        }
        return view('users.edit', compact('user', 'partners', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'role_id' => 'required|numeric',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ]);
            $role = Role::find($request->role_id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'partner_id' => $request->partner_id,
                'role_id' => $role->id,
            ]);
            $user->roles()->detach();
            $user->assignRole($role->name);
            $resetTime = Carbon::now()->format('F j, Y \a\t g:i a');
            if (app()->isProduction()) {
                Mail::to($user->email)->send(new PasswordResetMail(
                    $user->name,
                    $resetTime
                ));
            } else {
                Log::info('User updated successfully and message sent successfully');
            }
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                session()->flash('error', $validator->errors()->first());
            }

            // Get the authenticated user
            $user = $request->user();

            // Check if the old password matches the current password
            if (!Hash::check($request->old_password, $user->password)) {
                session()->flash('error', 'The old password entered is incorrect');
            }
            // Update the user's password
            $user->password = Hash::make($request->new_password);
            $user->password_changed_at = now();
            $user->save();
            Auth::logout();

            $request->session()->forget('2fa_verified');

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect('/');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Error updating password: ' . $e->getMessage());
        }
    }

    public function deactivate(User $user)
    {
        try {
            $current_user = Auth::user();

            if ($user->is($current_user)) {
                throw new Exception('You cannot deactivate your own account.');
            }

            if ($current_user->partner_id !== null && $current_user->partner_id !== $user->partner_id) {
                throw new Exception('You are not authorized to deactivate this user.');
            }
            $user->is_active = false;
            $user->save();
            return redirect()->route('users.index')->with('success', 'User deactivated successfully.');
        } catch (Exception $th) {
            return redirect()->back()->with('error', "Failed to deactivate user: " . $th->getMessage());
        }
    }

    public function activate(User $user)
    {
        try {
            $current_user = Auth::user();

            if ($user->is($current_user)) {
                throw new Exception('You cannot activate your own account.');
            }

            if ($current_user->partner_id !== null && $current_user->partner_id !== $user->partner_id) {
                throw new Exception('You are not authorized to activate this user.');
            }
            $user->is_active = true;
            $user->save();
            return redirect()->route('users.index')->with('success', 'User activated successfully.');
        } catch (Exception $th) {
            return redirect()->back()->with('error', "Failed to activate user: " . $th->getMessage());
        }
    }

    public function show2faLoginScreen(Request $request)
    {
        return view('content.authentications.two-factor-authentication');
    }

    public function enable2fa(Request $request, User $user)
    {
        $google2fa = new Google2FA();
        try {
            $secretKey = $google2fa->generateSecretKey();
            $qrCodeUrl = $google2fa->getQRCodeInline(
                "GnuGrid LMS",
                $user->name,
                $secretKey
            );
            $google2fa_url = $qrCodeUrl;
            $user->update([
                'google2fa_secret' => $secretKey,
                'google2fa_url' => $google2fa_url,
            ]);
            session()->flash('success', '2FA initiated successfully. Scan the QR code below to complete your enrolment.');
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
        }

        return redirect()->route('users.show', $user);
    }

    public function confirm2faCode(Request $request, User $user)
    {
        $google2fa = new Google2FA();
        $code = $request->input('code');
        $window = 8; // 8 keys (respectively 4 minutes) past and future
        if (!$google2fa->verifyKey($user->google2fa_secret, $code, $window)) {
            session()->flash('error', 'Invalid 2FA code');
            return redirect()->route('users.show', $user);
        }
        $user->update([
            'has_2fa_enabled' => true,
        ]);
        session()->flash('success', '2FA enabled successfully');
        return redirect()->route('users.show', $user);
    }

    public function verify2faCode(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
        $code = $request->input('code');
        $window = 8; // 8 keys (respectively 4 minutes) past and future
        if (!$google2fa->verifyKey($user->google2fa_secret, $code, $window)) {
            session()->flash('error', 'Invalid 2FA code');
            return redirect()->back();
        }

        // Set the 2FA verification status in the session
        $request->session()->put('2fa_verified', true);

        session()->flash('success', '2FA verified successfully');

        return redirect()->route('dashboard.index');
    }

    public function delete(User $user)
    {
        try {
            $current_user = Auth::user();

            if ($user->is($current_user)) {
                throw new Exception('You cannot delete your own account.');
            }

            if ($current_user->partner_id !== null && $current_user->partner_id !== $user->partner_id) {
                throw new Exception('You are not authorized to edit this user.');
            }

            $user->delete();

            session()->flash('success', 'User has been deleted successfully.');

            return redirect('/users');
        } catch (Exception $th) {
            return redirect()->back()->with('error', "Failed to delete user: " . $th->getMessage());
        }
    }

    public function restore(User $user)
    {
        try {
            $current_user = Auth::user();

            if ($user->is($current_user)) {
                throw new Exception('You cannot restore your own account.');
            }

            if ($current_user->partner_id !== null && $current_user->partner_id !== $user->partner_id) {
                throw new Exception('You are not authorized to restore this user.');
            }
            if ($user->trashed()) {
                $user->restore();
                return redirect()->back()->with('success', "User {$user->name} restored successfully.");
            }

            return redirect()->back()->with('info', "User {$user->name} is not deleted.");
        } catch (Exception $th) {
            return redirect()->back()->with('error', "Failed to restore user: " . $th->getMessage());
        }
    }
}
