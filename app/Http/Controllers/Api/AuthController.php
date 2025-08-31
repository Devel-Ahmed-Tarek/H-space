<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HelperFunc;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:Admin,Project Manager,Developer',
        ]);

        if ($validator->fails()) {
            return HelperFunc::sendResponse(422, 'فشل في التحقق من البيانات', $validator->errors());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role
        $user->assignRole($request->role);

        $token = $user->createToken('auth_token')->plainTextToken;

        return HelperFunc::sendResponse(201, 'تم تسجيل المستخدم بنجاح', [
            'user'       => $user->load('roles'),
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return HelperFunc::sendResponse(422, 'فشل في التحقق من البيانات', $validator->errors());
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return HelperFunc::sendResponse(401, 'بيانات الاعتماد غير صحيحة');
        }

        $user  = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return HelperFunc::sendResponse(200, 'تم تسجيل الدخول بنجاح', [
            'user'       => $user->load('roles'),
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return HelperFunc::sendResponse(200, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'managedProjects', 'assignedTasks']);

        return HelperFunc::sendResponse(200, 'تم جلب الملف الشخصي بنجاح', $user);
    }
}
