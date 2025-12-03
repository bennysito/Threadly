# Received Bids System - Complete Integration Guide

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                      THREADLY PLATFORM                           │
└─────────────────────────────────────────────────────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
            ┌───────▼────────┐  ┌──────▼──────────┐
            │    CUSTOMERS   │  │    SELLERS      │
            └────────────────┘  └─────────────────┘
                    │                   │
                    │ Places Bid        │ Receives Bids
                    │                   │
                    └────────┬──────────┘
                             │
                    ┌────────▼──────────┐
                    │   BIDS DATABASE   │
                    │   (MySQL Table)   │
                    └───────────────────┘
```

## Data Flow Diagram

### Complete Bidding Flow

```
1. CUSTOMER ACTION:
   ┌──────────────────────┐
   │ Navigate to Product  │
   │ (product_info.php)   │
   └─────────────┬────────┘
                 │
   ┌─────────────▼────────────────┐
   │ See Bidding Section (if      │
   │ product has bidding=1)        │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ Fill Bid Form:               │
   │ - Bid Amount                 │
   │ - Optional Message           │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ Click "Place Bid"            │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ place_bid.php (AJAX)         │
   │ - Validate bid amount        │
   │ - Insert into bids table     │
   │ - Return JSON success        │
   └─────────────┬────────────────┘
                 │
                 │ ✓ Success
                 │
   ┌─────────────▼────────────────┐
   │ Bid Stored in Database       │
   │ - bid_status = 'pending'     │
   │ - created_at = NOW()         │
   └──────────────────────────────┘


2. SELLER ACTION:
   ┌──────────────────────┐
   │ Login to Account     │
   └─────────────┬────────┘
                 │
   ┌─────────────▼────────────────┐
   │ Open Seller Dashboard        │
   │ (seller_dashboard.php)       │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ Click "Received Bids" Tab    │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ JavaScript showTab('bids')    │
   │ - Hide other tabs             │
   │ - Show bids content           │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ Display All Bids for         │
   │ Seller's Products:           │
   │ - Bid card HTML rendered     │
   │ - Customer info displayed    │
   │ - Status badges shown        │
   │ - Action buttons visible     │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ Approve or Reject Bid        │
   │ (Click button)               │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ updateBidStatus() function   │
   │ - Show confirmation dialog   │
   │ - Send AJAX to               │
   │   update_bid_status.php      │
   └─────────────┬────────────────┘
                 │
   ┌─────────────▼────────────────┐
   │ update_bid_status.php        │
   │ - Verify seller owns product │
   │ - Update bid_status field    │
   │ - Return JSON success        │
   └─────────────┬────────────────┘
                 │
                 │ ✓ Success
                 │
   ┌─────────────▼────────────────┐
   │ Page Reloads                 │
   │ - Bid status updated         │
   │ - Card shows new status      │
   │ - Buttons become disabled    │
   └──────────────────────────────┘
```

## File Interconnections

```
SELLER DASHBOARD FLOW:
┌──────────────────────────────────────────────────────────┐
│ seller_dashboard.php (Entry Point)                       │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ ├─ includes: nav_bar.php                               │
│ │  └─ Sets $isSeller variable                          │
│ │                                                       │
│ ├─ requires: Users.php                                 │
│ │  └─ User class for auth check                        │
│ │                                                       │
│ ├─ requires: Categories.php                            │
│ │  └─ Category class for product form                  │
│ │                                                       │
│ ├─ requires: Bidding.php                               │
│ │  └─ Bidding class for bid operations                 │
│ │                                                       │
│ ├─ Backend PHP:                                         │
│ │  ├─ Fetches seller's products                        │
│ │  └─ Fetches seller's received bids (NEW)            │
│ │                                                       │
│ ├─ Displays:                                            │
│ │  ├─ Account Details Tab                              │
│ │  ├─ My Products Tab                                  │
│ │  ├─ Add Product Tab                                  │
│ │  ├─ Sold Products Tab                                │
│ │  └─ Received Bids Tab (NEW)                          │
│ │                                                       │
│ └─ JavaScript:                                          │
│    ├─ Tab navigation functions                         │
│    └─ updateBidStatus() function (NEW)                │
│       └─ Makes AJAX call to:                           │
│          update_bid_status.php                         │
│                                                       │
└──────────────────────────────────────────────────────────┘
```

## Database Schema Integration

```
BIDS TABLE:
┌──────────────────────────────────────────────────────────┐
│ bid_id (PK)          │ INT AUTO_INCREMENT                │
│ product_id (FK)      │ → products.product_id             │
│ user_id (FK)         │ → users.id                        │
│ bid_amount           │ DECIMAL(10,2)                     │
│ bid_status           │ ENUM('pending','accepted',        │
│                      │      'rejected','withdrawn')      │
│ bid_message          │ TEXT (optional)                   │
│ created_at           │ TIMESTAMP (bid placed date)       │
│ updated_at           │ TIMESTAMP (last status change)    │
└──────────────────────────────────────────────────────────┘
                      │ │ │
                      │ │ └─────────────────────────┐
                      │ └───────────────┐           │
                      └────────────┐    │           │
                                   ▼    ▼           ▼
                      ┌──────────┐ ┌──────┐ ┌──────────┐
                      │ products │ │ users│ │   ...   │
                      └──────────┘ └──────┘ └──────────┘
```

## Query Execution Flow

### Fetching Received Bids (seller_dashboard.php)

```
1. User authenticates (checked in if statement)
   └─ $user_id = $_SESSION['user_id']

2. Create database connection
   └─ $conn = $db->threadly_connect

3. Execute multi-table JOIN:
   ┌─────────────────────────────────────────────────┐
   │ SELECT * FROM bids b                            │
   │ JOIN products p                                 │
   │   ON b.product_id = p.product_id               │
   │ JOIN users u                                    │
   │   ON b.user_id = u.id                          │
   │ WHERE p.seller_id = ?                          │
   │ ORDER BY b.created_at DESC                     │
   └─────────────────────────────────────────────────┘
            ↓
   Filter by seller_id ensures seller only
   sees bids on their own products (SECURITY)

4. Bind parameters
   └─ $bidsStmt->bind_param('i', $user_id)

5. Execute query
   └─ $bidsStmt->execute()

6. Fetch all results
   ├─ Loop through result set
   ├─ Add each bid to $sellerBids array
   └─ Close statement

7. Pass data to view
   └─ $sellerBids available in HTML (foreach loop)
```

### Updating Bid Status (update_bid_status.php)

```
1. Receive AJAX request:
   ├─ POST data: bid_id, bid_status
   └─ Session: user_id

2. Validate input:
   ├─ bid_id must be > 0
   └─ bid_status in ['accepted', 'rejected']

3. SECURITY CHECK:
   ┌─────────────────────────────────────────────────┐
   │ SELECT b.bid_id, p.seller_id FROM bids b       │
   │ JOIN products p ON b.product_id = p.product_id │
   │ WHERE b.bid_id = ? AND p.seller_id = ?         │
   │                                                │
   │ This ensures only product owner can update    │
   │ bids on their products                        │
   └─────────────────────────────────────────────────┘

4. If authorized:
   ┌─────────────────────────────────────────────────┐
   │ UPDATE bids                                    │
   │ SET bid_status = ?,                            │
   │     updated_at = CURRENT_TIMESTAMP             │
   │ WHERE bid_id = ?                               │
   └─────────────────────────────────────────────────┘

5. Return JSON response:
   ├─ success: true/false
   ├─ message: result message
   └─ new_status: updated status

6. Frontend:
   ├─ On success: reload page
   └─ On error: show alert
```

## CSS Cascade

```
GLOBAL STYLES (Tailwind via CDN)
    ↓
SELLER_DASHBOARD.php <style> block
    ├─ .input-bg (form inputs)
    ├─ .bid-card (main card container)
    ├─ .bid-header (product section)
    ├─ .bid-details (details grid)
    ├─ .bid-actions (buttons section)
    ├─ .bid-status-badge (status indicators)
    ├─ .bid-*-btn (action buttons)
    └─ @media queries (responsive design)
        ├─ max-width: 768px (tablet)
        ├─ max-width: 375px (mobile)
        └─ Adjusts grid columns & layout
```

## JavaScript Execution Order

```
1. Page Load
   ├─ PHP generates HTML with bid data
   └─ All CSS/JS files load

2. Window Load Event
   ├─ Initialize tab variables:
   │  ├─ tabMy, tabAdd, tabSold, tabBids
   │  ├─ myContent, addContent, soldContent, receivedBidsContent
   │  └─ Other modal elements
   │
   ├─ Attach event listeners:
   │  ├─ tabMy.onclick → showTab('my')
   │  ├─ tabAdd.onclick → showTab('add')
   │  ├─ tabSold.onclick → showTab('sold')
   │  └─ tabBids.onclick → showTab('bids')
   │
   └─ Open products section by default
      └─ showTab('my')

3. User clicks "Received Bids" tab
   ├─ JavaScript event triggered
   ├─ showTab('bids') called
   │  ├─ hideContents() hides all tabs
   │  ├─ clearTabStyles() removes highlighting
   │  ├─ receivedBidsContent.style.display = 'block'
   │  └─ tabBids gets highlighted (dark gray text)
   └─ Bid cards become visible

4. User clicks Approve/Reject button
   ├─ updateBidStatus(bidId, status) called
   ├─ Confirmation dialog shown
   ├─ If OK:
   │  ├─ AJAX fetch() called
   │  ├─ POST to update_bid_status.php
   │  ├─ Wait for response
   │  ├─ Parse JSON response
   │  └─ location.reload() on success
   └─ Alert shown (success or error)
```

## Security Measures

### 1. Authentication
```
✓ Session check on page load
✓ Redirect to login if not authenticated
✓ $user_id verified from $_SESSION
```

### 2. Authorization
```
✓ Bids fetched only for seller's products
✓ WHERE p.seller_id = ? prevents data leakage
✓ update_bid_status verifies seller ownership
```

### 3. Input Validation
```
✓ bid_id must be integer > 0
✓ bid_status checked against whitelist
✓ SQL prepared statements (parameterized)
✓ No string concatenation in queries
```

### 4. Output Sanitization
```
✓ htmlspecialchars() for all user data display
✓ e() helper function wraps htmlspecialchars()
✓ Prevents XSS attacks in bid messages
✓ JSON response headers set properly
```

## Performance Considerations

```
OPTIMIZATION TECHNIQUES:

1. Database Efficiency
   ├─ LIMIT results if many bids
   ├─ Index on seller_id in products table
   ├─ Index on product_id in bids table
   └─ JOIN is efficient with indexed foreign keys

2. Frontend Efficiency
   ├─ CSS loaded from CDN (cached)
   ├─ Tab content pre-rendered (no AJAX)
   ├─ Only AJAX call is for status update
   └─ Minimal JavaScript execution

3. Caching
   ├─ Browser cache for images
   ├─ Static assets (CSS/JS) cached
   └─ Page reload on update (simple approach)

4. Lazy Loading (future enhancement)
   ├─ Could paginate bids (show 10 at a time)
   ├─ Could lazy load images
   └─ Could debounce tab switching
```

## Error Handling

```
CLIENT-SIDE ERRORS:
├─ Missing required fields: Alert user
├─ Bid too low: "Bid amount must be at least..."
├─ Network error: "Error: [error message]"
└─ AJAX error: "Error: Failed to update bid status"

SERVER-SIDE ERRORS:
├─ Database connection fails: catch(Exception)
├─ Prepare statement fails: error_log() + JSON error
├─ Update fails: error_log() + JSON error
├─ Unauthorized update: "Not authorized" JSON
└─ Invalid input: "Invalid bid ID or status" JSON
```

## Testing Integration Points

```
TEST ENDPOINTS:

1. place_bid.php
   ├─ Input: product_id, bid_amount, bid_message
   ├─ Output: JSON {success, message, bid_id}
   └─ Verify: Bid inserted in database

2. update_bid_status.php
   ├─ Input: bid_id, bid_status
   ├─ Output: JSON {success, message, new_status}
   └─ Verify: Bid status updated in database

3. seller_dashboard.php
   ├─ Query: Seller's bids display correctly
   ├─ Tab: Received Bids tab functional
   ├─ Buttons: Approve/Reject buttons work
   └─ Verify: No bids from other sellers visible

4. Database Integrity
   ├─ Foreign key constraints enforced
   ├─ Seller can only see own bids
   ├─ Status values are valid
   └─ Timestamps accurate
```

---

**Integration Guide**
**Version**: 1.0
**Status**: Complete Implementation
**Last Updated**: December 3, 2025
