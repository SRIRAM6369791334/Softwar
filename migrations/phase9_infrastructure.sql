-- Supermarket OS: Phase 9 Absolute Perfection Migration
-- Addressing remaining structural risks

-- 1. Background Jobs (Queue System) [#72, #75]
CREATE TABLE IF NOT EXISTS background_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type VARCHAR(50) NOT NULL,
    payload TEXT NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    attempts INT DEFAULT 0,
    last_error TEXT,
    run_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status_run (status, run_at)
);

-- 2. Vendor Payments (Reconciliation) [#57]
CREATE TABLE IF NOT EXISTS vendor_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    po_id INT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_mode ENUM('cash', 'cheque', 'bank_transfer', 'other') NOT NULL,
    reference_no VARCHAR(100),
    remarks TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id),
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id)
);

-- 3. System Configuration & Feature Flags [#98, #99, #103]
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    data_type ENUM('string', 'int', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Initial Settings
INSERT IGNORE INTO system_settings (setting_key, setting_value, data_type, description) VALUES 
('currency_code', 'USD', 'string', 'Main system currency code'),
('currency_symbol', '$', 'string', 'Main system currency symbol'),
('feature_dark_mode', 'true', 'boolean', 'Enable/Disable dark mode globally'),
('feature_offline_mode', 'false', 'boolean', 'Enable/Disable service worker offline caching'),
('budget_po_limit', '5000', 'int', 'Purchase Order limit for standard managers'),
('retention_period_days', '730', 'int', 'Data retention period for logs (2 years)');

-- 4. Compliance: Soft Delete for PII tracking [#81]
-- 5. Gapless Numbering Sequence [#87]
CREATE TABLE IF NOT EXISTS invoice_sequences (
    branch_id INT PRIMARY KEY,
    prefix VARCHAR(10) DEFAULT 'INV',
    last_val INT DEFAULT 0,
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);

-- Seed sequences for existing branches
INSERT IGNORE INTO invoice_sequences (branch_id, prefix, last_val)
SELECT id, 'INV', 0 FROM branches;
