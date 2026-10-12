-- Migration 005: Add missing columns to orders, order_items, inventory_reservations, and inventory_movements for SQLite compatibility

ALTER TABLE orders ADD COLUMN shipping_address TEXT;
ALTER TABLE orders ADD COLUMN billing_address TEXT;
ALTER TABLE orders ADD COLUMN shipping_method TEXT DEFAULT 'standard';
ALTER TABLE orders ADD COLUMN tracking_number TEXT;
ALTER TABLE orders ADD COLUMN admin_notes TEXT;

ALTER TABLE order_items ADD COLUMN product_sku TEXT;
ALTER TABLE order_items ADD COLUMN item_total REAL;

ALTER TABLE inventory_reservations ADD COLUMN product_id INTEGER;
ALTER TABLE inventory_reservations ADD COLUMN variant_id INTEGER;
ALTER TABLE inventory_reservations ADD COLUMN user_id INTEGER;
ALTER TABLE inventory_reservations ADD COLUMN session_id TEXT;
ALTER TABLE inventory_reservations ADD COLUMN status TEXT DEFAULT 'active';

ALTER TABLE inventory_movements ADD COLUMN reference_type TEXT;
ALTER TABLE inventory_movements ADD COLUMN created_by INTEGER;
