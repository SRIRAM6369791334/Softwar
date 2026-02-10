<?php

namespace App\Core;

class Auth
{
    public static function login(array $user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!headers_sent()) {
            session_regenerate_id(true); // Prevent session fixation
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['full_name'] = $user['full_name'];
        
        // Set default branch if not already set
        if (!isset($_SESSION['branch_id'])) {
            $_SESSION['branch_id'] = $user['branch_id'] ?? 1;
            $_SESSION['branch_name'] = 'Main Branch';
        }
    }

    /**
     * Check if user has specific role(s)
     * 1=Admin, 2=Manager, 3=Cashier
     */
    public static function hasRole(array|int $roles): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['role_id'])) {
            return false;
        }

        $currentRole = (int)$_SESSION['role_id'];
        
        if (is_int($roles)) {
            return $currentRole === $roles;
        }
        
        return in_array($currentRole, $roles);
    }

    /**
     * Vendor Session Management
     */
    public static function vendorLogin(array $vendor)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        $_SESSION['vendor_id'] = $vendor['id'];
        $_SESSION['vendor_name'] = $vendor['name'];
        $_SESSION['vendor_email'] = $vendor['email'];
        $_SESSION['is_vendor'] = true;
    }

    public static function vendorCheck(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['vendor_id']) && isset($_SESSION['is_vendor']);
    }

    public static function vendorId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['vendor_id'] ?? null;
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }

    public static function user()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }

    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public static function id()
    {
         if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current active branch ID
     */
    public static function getCurrentBranch(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['branch_id'] ?? 1; // Default to Main Branch
    }

    /**
     * Set active branch
     */
    public static function setBranch(int $branchId, string $branchName = ''): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['branch_id'] = $branchId;
        if ($branchName) {
            $_SESSION['branch_name'] = $branchName;
        }
    }

    /**
     * Get current branch name
     */
    public static function getBranchName(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['branch_name'] ?? 'Main Branch';
    }

    // --- CSRF Protection ---

    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
