-- ================================================
-- Phase 2: Authentication & Authorization Migration
-- Adds 2FA support and enhanced session tracking
-- ================================================

-- Add 2FA columns to users table
ALTER TABLE users ADD COLUMN two_factor_secret VARCHAR(32) NULL DEFAULT NULL 
    COMMENT 'Base32 encoded TOTP secret';
    
ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 
    COMMENT '1 if 2FA is enabled for this user';
    
ALTER TABLE users ADD COLUMN two_factor_backup_codes TEXT NULL DEFAULT NULL 
    COMMENT 'JSON array of hashed backup codes';

-- Add IP whitelist support for admin users
ALTER TABLE users ADD COLUMN ip_whitelist TEXT NULL DEFAULT NULL 
    COMMENT 'JSON array of allowed IP addresses';

-- Add login tracking
ALTER TABLE users ADD COLUMN login_count INT NOT NULL DEFAULT 0 
    COMMENT 'Total number of successful logins';

-- Index for 2FA lookups
CREATE INDEX idx_users_two_factor ON users(two_factor_enabled);

-- Login history table for audit
CREATE TABLE IF NOT EXISTS login_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    two_factor_used TINYINT(1) DEFAULT 0,
    status ENUM('success', 'failed', 'blocked') NOT NULL,
    failure_reason VARCHAR(255) NULL,
    INDEX idx_user (user_id),
    INDEX idx_login_at (login_at),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Session activity tracking
CREATE TABLE IF NOT EXISTS session_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    user_id INT NULL,
    activity_type VARCHAR(50) NOT NULL COMMENT 'page_view, action, etc',
    url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin action log (for critical actions)
CREATE TABLE IF NOT EXISTS admin_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50) COMMENT 'user, product, invoice, etc',
    target_id INT NULL,
    details TEXT COMMENT 'JSON details of the action',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
