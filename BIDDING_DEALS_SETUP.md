# Bidding Deals Integration - Setup Guide

## Overview
The Bidding Deals section on `index.php` now displays products that have bidding enabled. Products with `bidding = 1` will appear in the carousel automatically.

## Database Setup Required

### Step 1: Add Bidding Column to Products Table

If you haven't already, add the `bidding` column to the products table by running:

```php
// Option 1: Via Browser (Quick)
Visit: http://localhost/xampp/htdocs/Threadly/Back_End/Models/add_bidding_column.php
```

Or run manually in your MySQL client:
```sql
ALTER TABLE products ADD COLUMN bidding TINYINT(1) NOT NULL DEFAULT 0;
```

### Step 2: Setup Bidding Session Table (Optional)

If you want to use the bidding session system, run:
```php
// Via Browser
Visit: http://localhost/xampp/htdocs/Threadly/Back_End/setup_bidding.php
```

This creates the `bids` table for tracking individual bids.

## How It Works

### For Sellers (in seller_dashboard.php)
- When editing or adding a product, sellers can enable the "Enable bidding for this product" checkbox
- This sets `bidding = 1` for that product

### For Customers (in index.php)
- The "BIDDING DEALS" section on the homepage automatically fetches all products where `bidding = 1`
- Products display with:
  - Product image
  - "BIDDING" badge (amber colored)
  - Product name
  - Starting price
  - "Click to place bid" text
  - Clickable link to `product_info.php?id=[product_id]`

### Product Carousel Features
- Responsive sliding carousel
- Navigation arrows
- 2-5 slides visible depending on screen size
- Hover effects on product cards

## Files Modified

1. **Front_End/Bidding_Swipe.php**
   - Changed from hardcoded products to dynamic database queries
   - Now fetches products where `bidding = 1`
   - Displays maximum 20 bidding products
   - Handles fallback if bidding column doesn't exist

2. **Front_End/seller_dashboard.php** (previously fixed)
   - Sellers can enable/disable bidding when editing products
   - Updates the `bidding` column value

## Requirements Met

✅ Products with bidding enabled appear in index.php BIDDING DEALS section
✅ Dynamic carousel automatically updates when products are added/edited
✅ Responsive design (works on mobile, tablet, desktop)
✅ Click to bid functionality (links to product_info.php)
✅ Fallback handling if database column doesn't exist

## Troubleshooting

### "No Bidding Products" showing
- Check if bidding column exists: `SHOW COLUMNS FROM products LIKE 'bidding';`
- Check if any products have `bidding = 1`: `SELECT * FROM products WHERE bidding = 1;`
- Verify products have `quantity > 0`

### Products not appearing
1. Make sure seller has enabled bidding when creating/editing product
2. Check product quantity is greater than 0
3. Clear browser cache
4. Check error logs for database errors

### Image not displaying
- Verify image file exists in `Front_End/uploads/` directory
- Check file permissions
- Fallback image will show if original path is invalid

## Next Steps

1. Run the migration scripts to add the bidding column
2. Have sellers enable bidding on their products
3. Test by viewing index.php - bidding products should appear in the carousel
4. Configure bidding rules (minimum bid increment, auction duration, etc.) as needed

