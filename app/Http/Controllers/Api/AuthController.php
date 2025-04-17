<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Candidate\CandidateResource;
use App\Http\Resources\Company\CompanyResource;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use App\Models\VerificationCode;
use App\Notifications\Admin\NewUserRegisteredNotification;
use App\Notifications\Api\ResetPassword;
use App\Notifications\CandidateCreateApprovalPendingNotification;
use App\Notifications\CandidateCreateNotification;
use App\Notifications\CompanyCreateApprovalPendingNotification;
use App\Notifications\CompanyCreatedNotification;
use F9Web\ApiResponseHelpers;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseHelpers;

    public function login(Request $request)
    {
        // $request->validate([
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages(), 'status' => false], 422
            );
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('job-pilot')->plainTextToken;

            return $this->respondWithSuccess([
                'status' => true,
                'message' => 'Login Succeeded',
                'data' => [
                    'token' => $token,
                    'user' => $user->role == 'candidate' ? new CandidateResource($user->candidate) : new CompanyResource($user->company),
                ],
            ]);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
            ], 404);

            // return $this->respondUnAuthenticated('Invalid Credentials');
        }
    }

    public function getUserInfo(Request $request)
    {
        $user = auth('sanctum')->user();

        if ($user) {
            $token = $request->bearerToken();

            return $this->respondWithSuccess([
                'status' => true,
                'message' => 'User data retrieved successfully',
                'data' => [
                    'token' => $request->bearerToken(),
                    'user' => $user->role == 'candidate' ? new CandidateResource($user->candidate) : new CompanyResource($user->company),

                ],
            ]);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
            ], 401);
            // return $this->respondUnAuthenticated('Unauthenticated User');

        }
    }

    public function register(Request $request)
    {
        // $request->validate([
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nic' =>    ['required', 'int', ],
            'phone' =>  ['required', 'string', 'max:20',],
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages(), 'status' => false], 422
            );
        }

        $newUsername = Str::slug($request->name);
        $oldUserName = User::where('username', $newUsername)->first();

        if ($oldUserName) {
            $username = Str::slug($newUsername).'_'.Str::random(5);
        } else {
            $username = Str::slug($newUsername);
        }

        $user = User::create([
            'role' => $request->role == 'candidate' ? 'candidate' : 'company',
            'name' => $request->name,
            'nic'   => $request['nic'],
            'phone' => $request['phone'],
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            // $admins = Admin::all();
            // foreach ($admins as $admin) {
            //     Notification::send($admin, new NewUserRegisteredNotification($admin, $user));
            // }
        } catch (\Throwable $th) {
        }

        // if mail configured, send notification to candidate and company
        if (checkMailConfig()) {
            if ($user->role == 'candidate') {
                $candidate_account_auto_activation_enabled = Setting::where('candidate_account_auto_activation', 1)->count();

                if ($candidate_account_auto_activation_enabled) {
                    // Notification::route('mail', $user->email)->notify(new CandidateCreateNotification($user, $request->password));
                } else {
                    Notification::route('mail', $user->email)->notify(new CandidateCreateApprovalPendingNotification($user, $request->password));
                }
            } elseif ($user->role == 'company') {
                $employer_auto_activation_enabled = Setting::where('employer_auto_activation', 1)->count();

                if ($employer_auto_activation_enabled) {
                    // Notification::route('mail', $user->email)->notify(new CompanyCreatedNotification($user, $request->password));
                } else {
                    Notification::route('mail', $user->email)->notify(new CompanyCreateApprovalPendingNotification($user, $request->password));
                }
            }
        }

        if ($user) {
            return $this->respondWithSuccess([
                'status' => true,
                'message' => 'Registration Succeeded',
                'data' => $user,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Registration Failed',
        ], 422);

        // return $this->respondError('Registration Failed');
    }

    public function profile()
    {
        $user = Auth::user();

        return $this->respondWithSuccess([
            'status' => true,
            'message' => '',
            'data' => $user,
        ]);
    }

    public function sendResetCodeEmail(Request $request)
    {
        // $this->validate($request, [
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages(),  'status' => false], 422
            );
        }

        $customer = User::where('email', $request->email)->first();
        $code = rand(100000, 999999);

        $customer->verificationCodes()->create([
            'code' => $code,
            'type' => 'reset_password',
        ]);

        if (checkMailConfig()) {
            $customer->notify(new ResetPassword($code));
        }

        return $this->respondWithSuccess([
            'status' => true,
            'message' => 'We have emailed you password reset code',
            'data' => [
                // testing only should remove in production
                'code' => $code,
            ],
        ]);
    }

    public function reset(Request $request)
    {
        // $this->validate($request, [
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'email' => 'required|string|max:100|email|exists:users,email',
            'password' => 'required|min:8|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages(),  'status' => false], 422
            );
        }

        $customer = User::where('email', $request->email)->first();
        $verificationCode = VerificationCode::reset()
            ->where('user_id', $customer->id)
            ->where('code', $request->code)
            ->first();

        if (! $verificationCode) {
            // return $this->respondError('Invalid code');
            return response()->json([
                'status' => false,
                'message' => 'Invalid code',
            ], 404);
        } elseif ($verificationCode && now()->isAfter($verificationCode->expire_at)) {
            // return $this->respondError('Code expired');
            return response()->json([
                'status' => false,
                'message' => 'Code expired',
            ], 404);
        }

        if ($customer) {
            $customer->update([
                'password' => bcrypt($request->password),
            ]);

            return $this->respondWithSuccess([
                'status' => true,
                'message' => 'Password reset successfully',
                // 'data' => [
                //     'message' => 'Password reset successfully',
                // ],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid code',
        ], 404);
        // return $this->respondNotFound('Invalid code');
    }

    public function socialLogin(Request $request)
    {

        // Launch Firebase Auth
        $auth = app('firebase.auth');

        // Retrieve the Firebase credential's token
        $idTokenString = $request->input('Firebasetoken');

        try {
            // Try to verify the Firebase credential token with Google
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
        } catch (\InvalidArgumentException $e) {
            // If the token has the wrong format
            return response()->json([
                'message' => 'Unauthorized - Can\'t parse the token: '.$e->getMessage(),
            ], 401);
        } catch (InvalidToken $e) {
            // If the token is invalid (expired ...)
            return response()->json([
                'message' => 'Unauthorized - Token is invalide: '.$e->getMessage(),
            ], 401);
        }

        // Retrieve the UID (User ID) from the verified Firebase credential's token
        $uid = $verifiedIdToken->getClaim('sub');

        // Retrieve the user model linked with the Firebase UID
        $user = User::where('firebase_uid', $uid)->first();

        // If the user doesn't exist, create a new user (you may customize this)
        try {
            if (! $user) {
                $user = User::create([
                    'firebase_uid' => $uid,
                    'role' => $request->input('role'),
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user: '.$e->getMessage(),
            ], 500);
        }

        // Create a Personal Access Token using Sanctum
        $token = $user->createToken('job-pilot')->plainTextToken;

        return $this->respondWithSuccess([
            'data' => [
                'token' => $token,
                'message' => 'User data retrieved successfully',
                'user' => $user->role == 'candidate' ? new CandidateResource($user->candidate) : new CompanyResource($user->company),
            ],
        ]);

    }
}
