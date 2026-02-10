-- Phase 2: Performance Indexes
-- Adding explicit indexes to Foreign Keys that might be missing them or for optimization

-- 1. Users & Roles
CREATE INDEX IF NOT EXISTS idx_users_role_id ON users(role_id);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);

-- 2. Products
CREATE INDEX IF NOT EXISTS idx_products_tax_group ON products(tax_group_id);
CREATE INDEX IF NOT EXISTS idx_products_name ON products(name); -- For search
CREATE INDEX IF NOT EXISTS idx_products_sku ON products(sku);

-- 3. Product Batches
CREATE INDEX IF NOT EXISTS idx_batches_product_id ON product_batches(product_id);
CREATE INDEX IF NOT EXISTS idx_batches_stock ON product_batches(stock_qty); -- For "In Stock" filtering

-- 4. Invoices
CREATE INDEX IF NOT EXISTS idx_invoices_user_id ON invoices(user_id);
CREATE INDEX IF NOT EXISTS idx_invoices_date ON invoices(created_at);
CREATE INDEX IF NOT EXISTS idx_invoices_inv_no ON invoices(invoice_no);

-- 5. Invoice Items
CREATE INDEX IF NOT EXISTS idx_inv_items_invoice_id ON invoice_items(invoice_id);
CREATE INDEX IF NOT EXISTS idx_inv_items_product_id ON invoice_items(product_id);

-- 6. Audit Logs
CREATE INDEX IF NOT EXISTS idx_audit_user_id ON audit_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_logs(action);
