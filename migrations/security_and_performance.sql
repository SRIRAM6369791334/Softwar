-- ================================================
-- Security & Performance Migration
-- Adds constraints, indexes, and data integrity rules
-- ================================================

-- ================================================
-- Section 1: Add UNIQUE Constraints
-- ================================================

-- Prevent duplicate SKUs
ALTER TABLE products ADD CONSTRAINT uk_products_sku UNIQUE (sku, branch_id);

-- Prevent duplicate invoice numbers
ALTER TABLE invoices ADD CONSTRAINT uk_invoices_invoice_no UNIQUE (invoice_no);

-- Prevent duplicate email addresses
ALTER TABLE users ADD CONSTRAINT uk_users_email UNIQUE (email);

-- Prevent duplicate usernames
ALTER TABLE users ADD CONSTRAINT uk_users_username UNIQUE (username);

-- Prevent multiple clock-ins on same day
ALTER TABLE attendance_logs ADD CONSTRAINT uk_attendance_user_date UNIQUE (user_id, date, clock_in);

-- ================================================
-- Section 2: Add NOT NULL Constraints
-- ================================================

ALTER TABLE products MODIFY COLUMN name VARCHAR(255) NOT NULL;
ALTER TABLE products MODIFY COLUMN sku VARCHAR(100) NOT NULL;
ALTER TABLE products MODIFY COLUMN branch_id INT NOT NULL;

ALTER TABLE invoices MODIFY COLUMN invoice_no VARCHAR(50) NOT NULL;
ALTER TABLE invoices MODIFY COLUMN user_id INT NOT NULL;
ALTER TABLE invoices MODIFY COLUMN branch_id INT NOT NULL;

ALTER TABLE users MODIFY COLUMN username VARCHAR(100) NOT NULL;
ALTER TABLE users MODIFY COLUMN password_hash VARCHAR(255) NOT NULL;
ALTER TABLE users MODIFY COLUMN role_id INT NOT NULL;

-- ================================================
-- Section 3: Add CHECK Constraints (MySQL 8.0.16+)
-- ================================================

-- Ensure stock quantity is never negative
ALTER TABLE product_batches 
ADD CONSTRAINT chk_batches_stock_positive CHECK (stock_qty >= 0);

-- Ensure prices are positive
ALTER TABLE product_batches 
ADD CONSTRAINT chk_batches_price_positive CHECK (sale_price >= 0 AND purchase_price >= 0);

-- Ensure invoice totals are positive
ALTER TABLE invoices 
ADD CONSTRAINT chk_invoices_totals_positive CHECK (grand_total >= 0 AND sub_total >= 0);

-- Ensure tax percentages are valid
ALTER TABLE tax_groups 
ADD CONSTRAINT chk_tax_percentage_valid CHECK (percentage >= 0 AND percentage <= 100);

-- Ensure grace period is reasonable
ALTER TABLE users 
ADD CONSTRAINT chk_grace_period_valid CHECK (grace_period_minutes >= 0 AND grace_period_minutes <= 60);

-- ================================================
-- Section 4: Add Performance Indexes
-- ================================================

-- Invoice queries
CREATE INDEX idx_invoices_created_at ON invoices(created_at);
CREATE INDEX idx_invoices_branch_status ON invoices(branch_id, status);
CREATE INDEX idx_invoices_user ON invoices(user_id);

-- Product searches
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_active_branch ON products(is_active, branch_id);

-- Product batch queries
CREATE INDEX idx_batches_product ON product_batches(product_id);
CREATE INDEX idx_batches_branch_stock ON product_batches(branch_id, stock_qty);
CREATE INDEX idx_batches_expiry ON product_batches(expiry_date);

-- Attendance queries
CREATE INDEX idx_attendance_user_date ON attendance_logs(user_id, date);
CREATE INDEX idx_attendance_date ON attendance_logs(date);

-- Leave queries
CREATE INDEX idx_leaves_user ON employee_leaves(user_id);
CREATE INDEX idx_leaves_status ON employee_leaves(status);
CREATE INDEX idx_leaves_date_range ON employee_leaves(start_date, end_date);

-- Audit log queries
CREATE INDEX idx_audit_user ON audit_logs(user_id);
CREATE INDEX idx_audit_created ON audit_logs(created_at);
CREATE INDEX idx_audit_action ON audit_logs(action);

-- Notification queries
CREATE INDEX idx_notifications_read ON notifications(is_read);
CREATE INDEX idx_notifications_created ON notifications(created_at);

-- ================================================
-- Section 5: Add Soft Delete Support
-- ================================================

-- Add deleted_at column to key tables if not exists
ALTER TABLE products ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE invoices ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Index for soft deletes
CREATE INDEX idx_products_deleted ON products(deleted_at);
CREATE INDEX idx_users_deleted ON users(deleted_at);
CREATE INDEX idx_invoices_deleted ON invoices(deleted_at);

-- ================================================
-- Section 6: Add Security-Related Columns
-- ================================================

-- Add password reset fields to users
ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN reset_token_expires TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN failed_login_attempts INT NOT NULL DEFAULT 0;
ALTER TABLE users ADD COLUMN account_locked_until TIMESTAMP NULL DEFAULT NULL;

-- ================================================
-- Section 7: Create Missing Tables
-- ================================================

-- Failed login tracking
CREATE TABLE IF NOT EXISTS failed_logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL COMMENT 'IP or username',
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    INDEX idx_identifier (identifier),
    INDEX idx_attempted_at (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Session management
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_last_activity (last_activity),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- ROLLBACK INSTRUCTIONS (if needed)
-- ================================================

/*
-- Remove unique constraints
ALTER TABLE products DROP INDEX uk_products_sku;
ALTER TABLE invoices DROP INDEX uk_invoices_invoice_no;
ALTER TABLE users DROP INDEX uk_users_email;
ALTER TABLE users DROP INDEX uk_users_username;
ALTER TABLE attendance_logs DROP INDEX uk_attendance_user_date;

-- Remove check constraints
ALTER TABLE product_batches DROP CONSTRAINT chk_batches_stock_positive;
ALTER TABLE product_batches DROP CONSTRAINT chk_batches_price_positive;
ALTER TABLE invoices DROP CONSTRAINT chk_invoices_totals_positive;
ALTER TABLE tax_groups DROP CONSTRAINT chk_tax_percentage_valid;
ALTER TABLE users DROP CONSTRAINT chk_grace_period_valid;

-- Drop indexes
DROP INDEX idx_invoices_created_at ON invoices;
DROP INDEX idx_invoices_branch_status ON invoices;
... (continue for all indexes)

-- Drop new tables
DROP TABLE IF EXISTS failed_logins;
DROP TABLE IF EXISTS sessions;
*/
