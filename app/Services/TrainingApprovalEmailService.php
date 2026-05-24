<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TrainingApprovalEmailService
{
    /**
     * Get approval hierarchy emails for training application
     * 
     * @param int|null $userId - Optional user ID, defaults to current authenticated user
     * @return array - Array of email addresses in approval order
     */
    public function getApprovalEmails($userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return [];
        }

        $emails = [];
        
        // Get immediate supervisor first
        $supervisor = $this->getImmediateSupervisor($user);
        if ($supervisor) {
            $emails[] = $supervisor->email;
        }
        
        // Get division manager
        $divisionManager = $this->getDivisionManager($user);
        if ($divisionManager && !in_array($divisionManager->email, $emails)) {
            $emails[] = $divisionManager->email;
        }
        
        // Get AVP (Assistant Vice President)
        $avp = $this->getAssistantVicePresident($user);
        if ($avp && !in_array($avp->email, $emails)) {
            $emails[] = $avp->email;
        }
        
        // Get VP (Vice President)
        $vp = $this->getVicePresident($user);
        if ($vp && !in_array($vp->email, $emails)) {
            $emails[] = $vp->email;
        }
        
        // Always include HRD at the end (you can modify this based on your HRD identification logic)
        $hrd = $this->getHRD();
        if ($hrd && !in_array($hrd->email, $emails)) {
            $emails[] = $hrd->email;
        }
        
        return array_filter($emails); // Remove any null/empty emails
    }
    
    /**
     * Get immediate supervisor
     * Priority: Unit supervisor > Division supervisor
     */
    public function getImmediateSupervisor($user = null)
    {
        $user = $user ?: Auth::user();
        
        if (!$user) {
            return null;
        }
        
        // First try to find unit supervisor if user has unit_id
        if ($user->unit_id) {
            $unitSupervisor = User::where('user_role', 'sv')
                ->where('office_division_id', $user->office_division_id)
                ->where('unit_id', $user->unit_id)
                ->where('active_status', 1)
                ->first();
                
            if ($unitSupervisor) {
                return $unitSupervisor;
            }
        }
        
        // If no unit supervisor found, get division supervisor
        return User::where('user_role', 'sv')
            ->where('office_division_id', $user->office_division_id)
            ->whereNull('unit_id') // Division-level supervisor
            ->where('active_status', 1)
            ->first();
    }
    
    /**
     * Get division manager
     */
    public function getDivisionManager($user = null)
    {
        $user = $user ?: Auth::user();
        
        if (!$user) {
            return null;
        }
        
        return User::where('user_role', 'dm')
            ->where('office_division_id', $user->office_division_id)
            ->where('active_status', 1)
            ->first();
    }
    
    /**
     * Get Assistant Vice President
     * Can be division-specific or overall
     */
    public function getAssistantVicePresident($user = null)
    {
        $user = $user ?: Auth::user();
        
        if (!$user) {
            return null;
        }
        
        // First try division-specific AVP
        $divisionAVP = User::where('user_role', 'avp')
            ->where('office_division_id', $user->office_division_id)
            ->where('active_status', 1)
            ->first();
            
        if ($divisionAVP) {
            return $divisionAVP;
        }
        
        // If no division-specific AVP, get overall AVP (no division)
        return User::where('user_role', 'avp')
            ->whereNull('office_division_id')
            ->where('active_status', 1)
            ->first();
    }
    
    /**
     * Get Vice President
     * Can be division-specific or overall
     */
    public function getVicePresident($user = null)
    {
        $user = $user ?: Auth::user();
        
        if (!$user) {
            return null;
        }
        
        // First try division-specific VP
        $divisionVP = User::where('user_role', 'vp')
            ->where('office_division_id', $user->office_division_id)
            ->where('active_status', 1)
            ->first();
            
        if ($divisionVP) {
            return $divisionVP;
        }
        
        // If no division-specific VP, get overall VP (no division)
        return User::where('user_role', 'vp')
            ->whereNull('office_division_id')
            ->where('active_status', 1)
            ->first();
    }
    
    /**
     * Get HRD (Human Resources Department)
     * You can modify this based on how you identify HRD users
     */
    public function getHRD()
    {
        // Option 1: If HRD has a specific user_role
        // return User::where('user_role', 'hrd')->where('active_status', 1)->first();
        
        // Option 2: If HRD is identified by division
        // return User::where('office_division_id', 'HRD_DIVISION_ID')->where('active_status', 1)->first();
        
        // Option 3: If HRD is identified by specific user IDs or email
        return User::where('email', 'hrd@company.com')->where('active_status', 1)->first();
        
        // You can modify this method based on your HRD identification logic
    }
    
    /**
     * Get approval hierarchy with user details (optional method for debugging/logging)
     */
    public function getApprovalHierarchy($userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return [];
        }
        
        $hierarchy = [];
        
        $supervisor = $this->getImmediateSupervisor($user);
        if ($supervisor) {
            $hierarchy[] = [
                'role' => 'Supervisor',
                'name' => $supervisor->name,
                'email' => $supervisor->email
            ];
        }
        
        $divisionManager = $this->getDivisionManager($user);
        if ($divisionManager && $divisionManager->email !== ($supervisor->email ?? null)) {
            $hierarchy[] = [
                'role' => 'Division Manager',
                'name' => $divisionManager->name,
                'email' => $divisionManager->email
            ];
        }
        
        $avp = $this->getAssistantVicePresident($user);
        if ($avp && !collect($hierarchy)->pluck('email')->contains($avp->email)) {
            $hierarchy[] = [
                'role' => 'Assistant Vice President',
                'name' => $avp->name,
                'email' => $avp->email
            ];
        }
        
        $vp = $this->getVicePresident($user);
        if ($vp && !collect($hierarchy)->pluck('email')->contains($vp->email)) {
            $hierarchy[] = [
                'role' => 'Vice President',
                'name' => $vp->name,
                'email' => $vp->email
            ];
        }
        
        $hrd = $this->getHRD();
        if ($hrd && !collect($hierarchy)->pluck('email')->contains($hrd->email)) {
            $hierarchy[] = [
                'role' => 'HRD',
                'name' => $hrd->name,
                'email' => $hrd->email
            ];
        }
        
        return $hierarchy;
    }
}