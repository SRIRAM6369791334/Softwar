-- Phase 6: Advanced Concurrency & Data Integrity

-- 1. Optimistic Locking Support
ALTER TABLE products ADD COLUMN version_id INT NOT NULL DEFAULT 1;
ALTER TABLE product_batches ADD COLUMN version_id INT NOT NULL DEFAULT 1;

-- 2. Safe Invoice Sequencing
CREATE TABLE IF NOT EXISTS invoice_sequences (
    branch_id INT PRIMARY KEY,
    last_val INT NOT NULL DEFAULT 0,
    prefix VARCHAR(10) NOT NULL DEFAULT 'INV',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initialize sequences for existing branches
INSERT IGNORE INTO invoice_sequences (branch_id, last_val, prefix)
SELECT id, 0, 'INV' FROM branches;

-- 3. Pessimistic Locking Constraint (Ensuring row-level locking behavior)
-- Note: InnoDB default behavior is row-level with 'FOR UPDATE', no changes needed here.
