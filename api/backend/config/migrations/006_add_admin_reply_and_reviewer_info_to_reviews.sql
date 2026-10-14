-- Migration 006: Add admin reply, reply timestamp, reviewer name, and indexes to product_reviews
ALTER TABLE product_reviews ADD COLUMN admin_reply TEXT;
ALTER TABLE product_reviews ADD COLUMN admin_reply_at TEXT;
ALTER TABLE product_reviews ADD COLUMN reviewer_name TEXT;

CREATE INDEX IF NOT EXISTS idx_product_reviews_prod_status ON product_reviews(product_id, status);
CREATE INDEX IF NOT EXISTS idx_product_reviews_user ON product_reviews(user_id);
