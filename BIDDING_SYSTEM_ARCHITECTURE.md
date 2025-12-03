# Bidding Deals - System Architecture & Flow Diagram

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                         THREADLY                             │
│                       E-COMMERCE PLATFORM                    │
└─────────────────────────────────────────────────────────────┘
                              │
                    ┌─────────┼─────────┐
                    │         │         │
            ┌───────▼──┐ ┌────▼────┐ ┌─▼──────────┐
            │  SELLERS │ │CUSTOMERS│ │ ADMINS     │
            └───────┬──┘ └────┬────┘ └─┬──────────┘
                    │         │        │
        ┌───────────┼─────────┼────────┼───────────┐
        │           │         │        │           │
        ▼           ▼         ▼        ▼           ▼
    ┌──────┐  ┌──────────┐ ┌──────┐ ┌──────────┐ ┌───────┐
    │ Dash │  │BIDDING   │ │VIEW  │ │SETUP    │ │TEST   │
    │BOARD │  │DEALS     │ │PROD  │ │BIDDING  │ │BIDDING│
    └──────┘  └──────────┘ └──────┘ └──────────┘ └───────┘
        │           │         │        │           │
        └───────────┼─────────┼────────┼───────────┘
                    │         │        │
                    └─────────┼────────┘
                              │
                    ┌─────────▼──────────┐
                    │   MYSQL DATABASE   │
                    │   [products table] │
                    │   bidding=1 flag   │
                    └────────────────────┘
```

## 🔄 Data Flow Diagram

### Seller Enabling Bidding
```
SELLER
  │
  └─> seller_dashboard.php
       │
       ├─ Edit Product
       │
       ├─ Check "Enable bidding"
       │
       └─> POST /seller_dashboard.php
            │
            ├─ Verify seller_id matches
            │
            ├─ Update products.bidding = 1
            │
            └─> Success! (Redirect)
```

### Product Display on Homepage
```
CUSTOMER visits index.php
  │
  └─> Includes Bidding_Swipe.php
       │
       ├─ Check if bidding column exists
       │
       ├─ Query: SELECT * FROM products WHERE bidding = 1
       │
       ├─ Fetch up to 20 products
       │
       ├─ Generate carousel HTML
       │
       ├─ Load Swiper library
       │
       └─> Display BIDDING DEALS section
            │
            └─> Customer clicks product
                 │
                 └─> product_info.php?id=[product_id]
```

### Database Query Flow
```
Bidding_Swipe.php
  │
  ├─ Create Database connection
  │
  ├─ Check if bidding column exists
  │    │
  │    ├─ YES: Query WHERE bidding = 1
  │    │
  │    └─ NO: Query recent products (fallback)
  │
  ├─ Execute: SELECT product_id, product_name, 
  │            price, image_url FROM products
  │            WHERE bidding = 1 AND quantity > 0
  │            ORDER BY product_id DESC LIMIT 20
  │
  ├─ Fetch results into PHP array
  │
  ├─ Format array with proper image paths
  │
  └─ Return to template for rendering
```

## 📊 Database Schema

```
products TABLE
┌─────────────────────────────────────────────────────┐
│ Column           │ Type            │ Notes           │
├──────────────────┼─────────────────┼─────────────────┤
│ product_id       │ INT PRIMARY KEY │ Auto increment  │
│ seller_id        │ INT NULL        │ Can be NULL     │
│ product_name     │ VARCHAR(255)    │ Required        │
│ price            │ DECIMAL(10,2)   │ Required        │
│ quantity         │ INT             │ Stock count     │
│ description      │ TEXT            │ Product details │
│ image_url        │ VARCHAR(255)    │ Image file      │
│ category_id      │ INT NULL        │ Category ref    │
│ availability     │ ENUM            │ Status          │
│ bidding          │ TINYINT(1)      │ ✨ NEW FIELD    │
│ created_at       │ TIMESTAMP       │ Auto timestamp  │
└─────────────────────────────────────────────────────┘

Key: bidding column values
  0 = Bidding disabled (default)
  1 = Bidding enabled
```

## 🔍 Component Diagram

```
┌──────────────────────────────────────────────────────────┐
│              index.php (Homepage)                        │
│  ┌────────────────────────────────────────────────────┐  │
│  │ CATEGORIES SECTION                                 │  │
│  │ (Category_carousel.php)                            │  │
│  └────────────────────────────────────────────────────┘  │
│                                                          │
│  ┌────────────────────────────────────────────────────┐  │
│  │ BIDDING DEALS SECTION                              │  │
│  │ ┌──────────────────────────────────────────────┐  │  │
│  │ │ Bidding_Swipe.php                           │  │  │
│  │ ├──────────────────────────────────────────────┤  │  │
│  │ │ • Database connection                      │  │  │
│  │ │ • Fetch bidding products                   │  │  │
│  │ │ • Generate carousel HTML                   │  │  │
│  │ │ • Swiper JavaScript initialization         │  │  │
│  │ └──────────────────────────────────────────────┘  │  │
│  │   [Product Card] [Product Card] [Product Card]   │  │
│  │   ◀ Previous        Next ▶                        │  │
│  └────────────────────────────────────────────────────┘  │
│                                                          │
│  ┌────────────────────────────────────────────────────┐  │
│  │ TOP SELLERS SECTION                                │  │
│  │ (Top_Sellers.php)                                  │  │
│  └────────────────────────────────────────────────────┘  │
│                                                          │
│  ┌────────────────────────────────────────────────────┐  │
│  │ DAILY DISCOVER SECTION                             │  │
│  │ (Daily_Discover.php)                               │  │
│  └────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────┘
```

## 🔐 Security Flow

```
USER REQUEST
  │
  ├─ Authenticate (Session check)
  │   │
  │   ├─ NO SESSION → Redirect to login
  │   │
  │   └─ SESSION EXISTS → Continue
  │
  ├─ Validate Input
  │   │
  │   ├─ Check product_id is integer
  │   │
  │   ├─ Verify seller_id ownership
  │   │
  │   └─ Sanitize strings
  │
  ├─ Database Query (Prepared Statement)
  │   │
  │   ├─ Bind parameters (no string concat)
  │   │
  │   └─ Execute with type checking
  │
  ├─ Process Results
  │   │
  │   ├─ HTML escape output (htmlspecialchars)
  │   │
  │   └─ Format for display
  │
  └─ Send Response
      │
      └─ Return to template
```

## 📱 Responsive Design Breakpoints

```
Mobile (< 640px)
  └─> Bidding_Swipe: 2 slides visible
      (swiper breakpoint: slidesPerView: 2)

Tablet (640px - 1023px)
  ├─> 640px:  3 slides
  │   (swiper breakpoint: 640: { slidesPerView: 3 })
  │
  └─> 768px:  4 slides
      (swiper breakpoint: 768: { slidesPerView: 4 })

Desktop (>= 1024px)
  └─> 5 slides visible
      (swiper breakpoint: 1024: { slidesPerView: 5 })
```

## 🔄 User Journey Map

### Seller Journey
```
LOGIN
  │
  └─> SELLER DASHBOARD
       │
       ├─ Click "My Products"
       │
       ├─ Click "Edit" on Product
       │
       ├─ ✓ Check "Enable bidding"
       │
       ├─ Click "Save Changes"
       │
       └─> ✅ Product appears in BIDDING DEALS
```

### Customer Journey
```
HOMEPAGE (index.php)
  │
  ├─> SCROLL DOWN
  │
  ├─> SEE "BIDDING DEALS"
  │
  ├─> CLICK PRODUCT
  │
  └─> product_info.php?id=X
       │
       ├─> VIEW PRODUCT DETAILS
       │
       └─> PLACE BID (if implemented)
```

## 🛠️ Admin Setup Journey

```
ADMIN SETUP
  │
  ├─> Visit setup_bidding_helper.php
  │
  ├─> Step 1: Add Bidding Column
  │    │
  │    └─> Click "Add Bidding Column"
  │         │
  │         └─> ALTER TABLE products...
  │              │
  │              └─> ✅ Column added
  │
  ├─> Step 2 (Optional): Enable Samples
  │    │
  │    └─> Click "Enable on 5 Products"
  │         │
  │         └─> UPDATE products SET bidding=1...
  │              │
  │              └─> ✅ 5 products enabled
  │
  └─> Verify Setup
       │
       └─> Visit test_bidding_display.php
            │
            └─> ✅ System status check
```

## 📈 Performance Optimization

```
OPTIMIZATIONS IMPLEMENTED:

1. Query Optimization
   └─> LIMIT 20 products max (not all)
   └─> WHERE bidding = 1 filter (indexed)
   └─> Only select needed columns

2. Caching Strategy
   └─> Browser cache for images
   └─> Static CSS/JS libraries (CDN)

3. Lazy Loading
   └─> Images load on demand
   └─> Swiper carousel lazy loads slides

4. Code Efficiency
   └─> Prepared statements (one-time parse)
   └─> Early termination on errors
   └─> Fallback handling (no page crash)
```

---

**Document:** System Architecture & Flow Diagrams
**Version:** 1.0
**Status:** Complete
**Last Updated:** December 3, 2025
