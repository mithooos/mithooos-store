-- MITHOOOS PostgreSQL Development Seed Data

INSERT INTO categories (category_name, slug, description, display_order, is_active) VALUES
  ('Sindhi Ajrak','sindhi-ajrak','Traditional hand-block printed Sindhi shawl and textile heritage.',1,true),
  ('Sindhi Topi','sindhi-topi','Classic Sindhi embroidered cap and festive headwear.',2,true),
  ('Sindhi Kajoor','sindhi-kajoor','Premium dried fruit and gifting essentials from Sindh.',3,true),
  ('Sindhi Achaar','sindhi-achaar','Authentic Sindhi pickles and condiments crafted in small batches.',4,true),
  ('Sindhi Shawls','sindhi-shawls','Soft layered shawls inspired by the culture of Sindh.',5,true),
  ('Sindhi Dupattas & Stoles','sindhi-dupattas-stoles','Elegant dupattas and stoles for everyday and festive styling.',6,true),
  ('Sindhi Handicrafts','sindhi-handicrafts','Handmade home and lifestyle pieces with cultural craft heritage.',7,true),
  ('Sindhi Gift Sets','sindhi-gift-sets','Curated Sindhi gift boxes for celebrations and occasions.',8,true),
  ('Sindhi Traditional Clothing','sindhi-traditional-clothing','Cultural attire and festive outfits inspired by Sindhi fashion.',9,true),
  ('Sindhi Cultural Artifacts','sindhi-cultural-artifacts','Authentic collectibles and decor rooted in Sindhi history and heritage.',10,true)
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Ajrak Heritage Shawl', 'ajrak-heritage-shawl', c.category_id, 'A richly patterned Sindhi Ajrak shawl handcrafted with traditional block-print artistry and heritage colors.', 'Signature Sindhi Ajrak textile', 2499.00, 15, 22, 'SIND-AJR-001', true, true, true, 4.8, 38, true
FROM categories c WHERE c.slug = 'sindhi-ajrak'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Classic Sindhi Topi', 'classic-sindhi-topi', c.category_id, 'Traditional Sindhi topi with intricate detailing and a bold cultural silhouette for festive and formal wear.', 'Festive heritage staple', 1299.00, 10, 35, 'SIND-TOP-001', true, true, false, 4.7, 21, true
FROM categories c WHERE c.slug = 'sindhi-topi'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Royal Kajoor Gift Box', 'royal-kajoor-gift-box', c.category_id, 'Premium Sindhi kajoor packed in a celebratory gift box for family gatherings, gifting, and hospitality.', 'Sindhi sweet tradition', 1799.00, 12, 18, 'SIND-KAJ-001', false, true, true, 4.9, 47, true
FROM categories c WHERE c.slug = 'sindhi-kajoor'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Sindhi Mango Pickle', 'sindhi-mango-pickle', c.category_id, 'Small-batch authentic Sindhi achaar made with sun-dried mangoes and house spice blends.', 'Aromatic heritage pickle', 899.00, 8, 40, 'SIND-ACH-001', true, false, false, 4.6, 26, true
FROM categories c WHERE c.slug = 'sindhi-achaar'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Dhamal Heritage Shawl', 'dhamal-heritage-shawl', c.category_id, 'A warm handwoven shawl inspired by the colors and craftsmanship of Sindhi festive attire.', 'Soft woven celebration wrap', 2199.00, 18, 16, 'SIND-SHAW-001', true, true, true, 4.8, 31, true
FROM categories c WHERE c.slug = 'sindhi-shawls'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Sindhi Stole Collection', 'sindhi-stole-collection', c.category_id, 'Lightweight dupatta and stoles with traditional embroidery, perfect for weddings and cultural gatherings.', 'Traditional drape, modern elegance', 1599.00, 10, 28, 'SIND-DUP-001', true, false, false, 4.7, 22, true
FROM categories c WHERE c.slug = 'sindhi-dupattas-stoles'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Handpainted Sindhi Pottery', 'handpainted-sindhi-pottery', c.category_id, 'Handcrafted decorative pottery reflecting Sindhi artistry, making an heirloom-worthy addition to home spaces.', 'Cultural home decor piece', 2899.00, 15, 12, 'SIND-HAND-001', false, true, true, 4.8, 18, true
FROM categories c WHERE c.slug = 'sindhi-handicrafts'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Heritage Gift Set', 'heritage-gift-set', c.category_id, 'A thoughtfully curated gift box featuring cultural essentials and handcrafted treasures from Sindh.', 'Perfect for celebrations', 3499.00, 20, 14, 'SIND-GIFT-001', true, true, true, 4.9, 44, true
FROM categories c WHERE c.slug = 'sindhi-gift-sets'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Sindhi Kurta Set', 'sindhi-kurta-set', c.category_id, 'A festive ensemble inspired by traditional Sindhi tailoring and handcrafted detailing for cultural gatherings.', 'Festive cultural attire', 3999.00, 12, 9, 'SIND-CLOTH-001', true, true, false, 4.7, 29, true
FROM categories c WHERE c.slug = 'sindhi-traditional-clothing'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO products (product_name, slug, category_id, description, short_description, price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_sale, rating, review_count, is_active)
SELECT 'Ralli Quilt Collector Piece', 'ralli-quilt-collector-piece', c.category_id, 'An heirloom-style Ralli-inspired textile artifact reflecting the richness of Sindhi craft and visual storytelling.', 'Collector''s heritage keepsake', 4999.00, 10, 8, 'SIND-ART-001', false, true, true, 4.9, 16, true
FROM categories c WHERE c.slug = 'sindhi-cultural-artifacts'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO coupons (code, description, discount_type, discount_value, minimum_purchase, is_active) VALUES
  ('SUMMER10', '10% off summer collection', 'percentage', 10.00, 0.00, true),
  ('WELCOME20', '20% off first order', 'percentage', 20.00, 0.00, true),
  ('SAVE15', '$15 off orders over $75', 'fixed', 15.00, 75.00, true)
ON CONFLICT (code) DO NOTHING;
