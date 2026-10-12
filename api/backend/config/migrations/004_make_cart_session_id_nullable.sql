CREATE TABLE carts_new (
  cart_id INTEGER PRIMARY KEY AUTOINCREMENT,
  session_id TEXT,
  user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
  product_id INTEGER NOT NULL REFERENCES products(product_id) ON DELETE CASCADE,
  variant_id INTEGER REFERENCES product_variants(variant_id) ON DELETE CASCADE,
  quantity INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
  created_at TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now')),
  updated_at TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ','now')),
  expires_at TEXT
);

INSERT INTO carts_new (cart_id, session_id, user_id, product_id, variant_id, quantity, created_at, updated_at, expires_at)
SELECT cart_id, session_id, user_id, product_id, variant_id, quantity, created_at, updated_at, expires_at FROM carts;

DROP TABLE carts;

ALTER TABLE carts_new RENAME TO carts;

CREATE INDEX IF NOT EXISTS idx_carts_user ON carts(user_id, product_id, variant_id);
CREATE INDEX IF NOT EXISTS idx_carts_session ON carts(session_id, product_id, variant_id);
