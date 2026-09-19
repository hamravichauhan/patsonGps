ALTER TABLE product_catalog 
ADD COLUMN description VARCHAR(255) NULL AFTER product_name,
ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER price,
ADD COLUMN in_stock TINYINT(1) DEFAULT 1 AFTER is_featured,
ADD COLUMN image_url VARCHAR(255) NULL AFTER in_stock;

-- Mark Flagship / Best Seller Products as Featured
UPDATE product_catalog 
SET is_featured = 1 
WHERE product_name IN (
    'Kokum Squash',
    'Kokum Agal (Pure Extract)',
    'Kokum Juice with Whole Fruits',
    'Cashew Mango Jam',
    'Cashew Strawberry Jam',
    'Alphonso Mango Squash',
    'Kesar Elaichi Syrup',
    'Mix Dry Fruits with Honey'
);

-- Add Default Product Descriptions
UPDATE product_catalog 
SET description = 'Authentic Goan Kokum squash crafted with natural fruit pulp. Refreshing & digestive.' 
WHERE product_name = 'Kokum Squash';

UPDATE product_catalog 
SET description = '100% pure unsweetened Kokum extract. Perfect for Solkadhi and traditional Goan curries.' 
WHERE product_name = 'Kokum Agal (PWHERE product_name = 'Kokum Agacatalog 
SET description = 'RefSET description = 'RefSET description = 'RefSET descriptionheSET description = 'RefSET description = 'RefSET descripuiSET description = 'RefSET description = 'RefSET descriptiiptSET description = 'RefSET description = 'RefSET descnchySET description = 'RefSET description = 'RefSET LIKE 'Cashew%Jam';

UPDATE product_catalog 
SET description = 'Rich royal syrup made with aromatic saffron and cardamom. IdealSET description = 'Rich royal syrupoduct_name = 'Kesar ElaichSET description = 'Rich royatalog 
SET description = 'Premium assorted dry fruits soaked in pure natural honey for wholesSET description = 'Premium assortectSET description = 'uits with Honey';
