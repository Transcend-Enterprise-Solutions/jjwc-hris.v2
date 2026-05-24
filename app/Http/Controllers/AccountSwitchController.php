<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountSwitchController extends Controller
{
    /**
     * Get available accounts to switch to
     */
    public function getAvailableAccounts()
    {
        $user = User::find(Auth::user()->id);
        $switchableAccounts = $user->getSwitchableAccounts();
        
        return response()->json([
            'current_account' => [
                'id' => $user->id,
                'name' => $user->name,
                'emp_code' => $user->emp_code,
                'user_role' => $user->user_role,
                'type' => $user->isAdminAccount() ? 'admin' : 'employee',
                'admin_prefix' => $user->admin_prefix
            ],
            'switchable_accounts' => $switchableAccounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'emp_code' => $account->emp_code,
                    'user_role' => $account->user_role,
                    'type' => $account->isAdminAccount() ? 'admin' : 'employee',
                    'admin_prefix' => $account->admin_prefix
                ];
            })
        ]);
    }

    /**
     * Switch to another account
     */
    public function switchAccount(Request $request)
    {
        $request->validate([
            'target_account_id' => 'required|exists:users,id'
        ]);

        $currentUser = Auth::user();
        $targetUser = User::find($request->target_account_id);

        // Verify that the target account is linked to current user
        if (!$this->canSwitchToAccount($currentUser, $targetUser)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to switch to this account.'
            ], 403);
        }

        // Store the account we're switching from (for potential switch back)
        session(['previous_account_id' => $currentUser->id]);

        // Login as the target user
        Auth::login($targetUser);

        // Get the appropriate redirect URL for the new user
        $redirectUrl = $this->getRedirectUrl($targetUser);

        Log::info('Account switch successful', [
            'from_user_id' => $currentUser->id,
            'from_user_role' => $currentUser->user_role,
            'to_user_id' => $targetUser->id,
            'to_user_role' => $targetUser->user_role,
            'redirect_url' => $redirectUrl
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully switched accounts.',
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * Switch back to the original account
     */
    public function switchBackToOriginal()
    {
        $originalAccountId = session('original_account_id');
        
        if (!$originalAccountId) {
            return response()->json([
                'success' => false,
                'message' => 'No original account found in session.'
            ], 400);
        }

        $originalUser = User::find($originalAccountId);
        
        if (!$originalUser) {
            return response()->json([
                'success' => false,
                'message' => 'Original account no longer exists.'
            ], 400);
        }

        Auth::login($originalUser);

        // Clear the previous account session
        session()->forget('previous_account_id');

        return response()->json([
            'success' => true,
            'message' => 'Successfully switched back to original account.',
            'redirect_url' => $this->getRedirectUrl($originalUser)
        ]);
    }

    /**
     * Check if current user can switch to target account
     */
    private function canSwitchToAccount($currentUser, $targetUser)
    {
        if ($currentUser->isAdminAccount()) {
            // Admin trying to switch to employee
            return $currentUser->base_emp_code === $targetUser->emp_code;
        } else {
            // Employee trying to switch to admin
            return $targetUser->base_emp_code === $currentUser->emp_code;
        }
    }

    /**
     * Get appropriate redirect URL based on user role and account type
     */
    private function getRedirectUrl($user)
    {
        // Log for debugging
        Log::info('Determining redirect URL', [
            'user_id' => $user->id,
            'user_role' => $user->user_role,
            'is_admin_account' => $user->isAdminAccount()
        ]);

        // Check user role and redirect accordingly
        switch ($user->user_role) {
            case 'emp':
                // Employee role always goes to home
                return route('home');
                
            case 'sa':
            case 'hr':
            case 'sv':
            case 'pa':
                // Admin roles go to dashboard
                return route('/dashboard');
                
            default:
                // Fallback: determine by account type if role is unclear
                if ($user->isAdminAccount()) {
                    return route('/dashboard');
                } else {
                    return route('home');
                }
        }
    }

    /**
     * Alternative method: Get safe redirect URL that checks route existence
     */
    private function getSafeRedirectUrl($user)
    {
        // Define role-to-route mapping
        $roleRoutes = [
            'emp' => 'home',
            'sa' => '/dashboard',
            'hr' => '/dashboard', 
            'sv' => '/dashboard',
            'pa' => '/dashboard'
        ];

        $routeName = $roleRoutes[$user->user_role] ?? null;

        // Check if route exists and is accessible
        if ($routeName && \Illuminate\Routing\Route::has($routeName)) {
            try {
                return route($routeName);
            } catch (\Exception $e) {
                Log::warning('Failed to generate route', [
                    'route_name' => $routeName,
                    'user_role' => $user->user_role,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Ultimate fallback
        return $user->user_role === 'emp' ? '/' : '/dashboard';
    }
}