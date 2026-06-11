<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WfhMonitoringAuthController extends Controller
{
    private const ADMIN_ROLES = ['sa', 'hr', 'sv', 'pa'];

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 422);
        }

        if (! in_array($user->user_role, self::ADMIN_ROLES, true)) {
            return response()->json(['message' => 'This account cannot access WFH monitoring.'], 403);
        }

        if (isset($user->active_status) && ! $user->active_status) {
            return response()->json(['message' => 'This account is inactive.'], 403);
        }

        $token = $user->createToken(
            $credentials['device_name'] ?? 'WFH Monitoring Dashboard',
            ['wfh-monitoring'],
            now()->addHours(12)
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->user_role,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! in_array($user->user_role, self::ADMIN_ROLES, true)) {
            return response()->json(['message' => 'WFH monitoring access denied.'], 403);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->user_role,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['ok' => true]);
    }
}
