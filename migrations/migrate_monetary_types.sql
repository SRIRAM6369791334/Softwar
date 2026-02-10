-- Supermarket OS: Financial Precision Migration
-- Phase 8, Risk #28: Systematic conversion to DECIMAL(10,2)

-- product_batches table (Primary pricing storage)
ALTER TABLE product_batches 
MODIFY COLUMN purchase_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN mrp DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN sale_price DECIMAL(10,2) NOT NULL DEFAULT 0.00;

-- invoices table
ALTER TABLE invoices 
MODIFY COLUMN sub_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN tax_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN discount_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN grand_total DECIMAL(10,2) NOT NULL DEFAULT 0.00;

-- invoice_items table
ALTER TABLE invoice_items 
MODIFY COLUMN unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
MODIFY COLUMN total DECIMAL(10,2) NOT NULL DEFAULT 0.00;

-- refund_requests table
ALTER TABLE refund_requests 
MODIFY COLUMN amount DECIMAL(10,2) NOT NULL DEFAULT 0.00;

-- audit_logs table (ensure detail isn't cutting off if we put prices there)
ALTER TABLE audit_logs MODIFY COLUMN description TEXT;
