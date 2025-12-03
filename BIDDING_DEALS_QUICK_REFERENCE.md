# Bidding Deals - Quick Reference Guide

## 🎯 Quick Links

| Purpose | URL |
|---------|-----|
| **View Bidding Deals** | `http://localhost/xampp/htdocs/Threadly/index.php` (scroll to "BIDDING DEALS") |
| **Setup Bidding System** | `http://localhost/xampp/htdocs/Threadly/setup_bidding_helper.php` |
| **Test Bidding Display** | `http://localhost/xampp/htdocs/Threadly/test_bidding_display.php` |
| **Seller Dashboard** | `http://localhost/xampp/htdocs/Threadly/Front_End/seller_dashboard.php` |
| **Add Bidding Column** | `http://localhost/xampp/htdocs/Threadly/Back_End/Models/add_bidding_column.php` |

---

## 🔧 Setup Checklist

- [ ] Add `bidding` column to products table
- [ ] Enable bidding on sample products (for testing)
- [ ] Verify `index.php` displays bidding products
- [ ] Test seller can enable/disable bidding
- [ ] Test customer can click on bidding products

---

## 📝 For Sellers: How to Enable Bidding

1. Go to **Seller Dashboard**
2. Click **"My products"** tab
3. Find your product and click **"Edit"**
4. Check the box: **"Enable bidding for this product"**
5. Click **"Save Changes"**
6. ✅ Your product now appears in the BIDDING DEALS carousel

---

## 👥 For Customers: How to Place a Bid

1. Go to **Homepage** (index.php)
2. Scroll down to **"BIDDING DEALS"** section
3. Click any product with the **BIDDING** badge
4. You'll be taken to the product details page
5. Follow bidding instructions to place your bid

---

## 🗄️ Database Information

### Bidding Column Details
```sql
-- Column name: bidding
-- Type: TINYINT(1)
-- Values: 0 (disabled) or 1 (enabled)
-- Default: 0

-- Add column (if not exists):
ALTER TABLE products ADD COLUMN bidding TINYINT(1) NOT NULL DEFAULT 0;

-- Query bidding products:
SELECT * FROM products WHERE bidding = 1 AND quantity > 0;

-- Enable bidding on a product:
UPDATE products SET bidding = 1 WHERE product_id = 5;

-- Disable bidding on a product:
UPDATE products SET bidding = 0 WHERE product_id = 5;
```

---

## 📊 Database Queries

### Count Bidding Products
```sql
SELECT COUNT(*) FROM products WHERE bidding = 1;
```

### List All Bidding Products
```sql
SELECT product_id, product_name, price, quantity 
FROM products 
WHERE bidding = 1 AND quantity > 0
ORDER BY product_id DESC;
```

### Batch Enable Bidding
```sql
UPDATE products SET bidding = 1 WHERE category_id = 5;
```

### Batch Disable Bidding
```sql
UPDATE products SET bidding = 0 WHERE price < 100;
```

---

## 🐛 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| "No Bidding Products" message | Run `setup_bidding_helper.php` to enable samples |
| Bidding column doesn't exist | Run `Back_End/Models/add_bidding_column.php` |
| Products not showing | Check `test_bidding_display.php` for diagnostics |
| Images not loading | Verify files exist in `Front_End/uploads/` |
| Carousel not working | Check browser console (F12) for JS errors |
| Can't enable bidding | Log in as seller, verify you own the product |

---

## 📁 File Structure

```
Threadly/
├── Front_End/
│   ├── index.php (displays BIDDING DEALS section)
│   ├── Bidding_Swipe.php (carousel component - UPDATED)
│   ├── seller_dashboard.php (manage products - FIXED)
│   ├── update_product_details.php (update handler - FIXED)
│   └── uploads/ (product images)
├── Back_End/
│   ├── Models/
│   │   ├── Database.php (connection)
│   │   ├── Bidding.php (bidding logic)
│   │   └── add_bidding_column.php (migration - UPDATED)
│   └── setup_bidding.php (setup script)
├── setup_bidding_helper.php (NEW - admin tool)
├── test_bidding_display.php (NEW - testing tool)
├── BIDDING_DEALS_SETUP.md (NEW - documentation)
└── BIDDING_DEALS_IMPLEMENTATION.md (NEW - summary)
```

---

## 🚀 Performance Tips

- Bidding display limited to 20 products maximum
- Only queries enabled bidding products
- Database index on `bidding` column recommended for large datasets

### Recommended Index
```sql
ALTER TABLE products ADD INDEX idx_bidding (bidding);
```

---

## 🔐 Security Notes

- ✅ All queries use prepared statements (no SQL injection)
- ✅ HTML output escaped (htmlspecialchars)
- ✅ Seller verification before allowing edits
- ✅ Product ownership validation

---

## 📈 Future Enhancements

- Bidding timer (countdown to auction end)
- Highest bid display on product card
- Automatic winner selection
- Bidding notifications to sellers
- Bid history tracking
- Starting price vs current bid display

---

## 📞 Support

For issues, check:
1. **test_bidding_display.php** - System diagnostics
2. **setup_bidding_helper.php** - Setup assistance
3. Error logs in `Back_End/` directory
4. Browser console (F12) for JavaScript errors

---

**Last Updated:** December 3, 2025
