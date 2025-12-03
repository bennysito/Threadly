# Received Bids System - Testing Guide

## Quick Start Testing

### Prerequisites
- Threadly application running on XAMPP
- At least 2 user accounts (1 seller, 1 customer)
- Product with bidding enabled on seller account

## Test Scenario 1: Place a Bid and View in Received Bids Tab

### Step 1: Login as Customer
```
1. Navigate to login.php
2. Enter customer credentials
3. Click "Login"
```

### Step 2: Browse to a Product with Bidding Enabled
```
1. Navigate to index.php (Homepage)
2. Look for "Bidding Deals" section
3. Click on a product that has bidding enabled
4. Should see product_info.php with bid form
```

### Step 3: Place a Bid
```
1. Scroll down to "Place a Bid" section
2. Enter bid amount (must be ≥ product price)
3. (Optional) Enter a message
4. Click "Place Bid" button
5. Should see success message: "Bid placed successfully!"
```

### Step 4: Logout and Login as Seller
```
1. Logout from customer account
2. Login with seller account (owner of the product)
3. Navigate to seller dashboard
4. Should automatically show "My products" tab
```

### Step 5: View Received Bids
```
1. In seller dashboard, click "Received Bids" tab
2. Should see bid card with:
   - Product image and name
   - Original price: ₱[amount]
   - Status badge showing "PENDING"
   - Bid Amount: ₱[customer bid]
   - Bidder name and username
   - Contact email and phone
   - Bid date/time
   - Bid message (if provided)
3. Should see two action buttons:
   - "✓ Approve Bid" (green)
   - "✗ Reject Bid" (red)
```

## Test Scenario 2: Approve a Bid

### Step 1: (From previous scenario, in Received Bids tab)
```
1. Find the bid you want to approve
2. Click "✓ Approve Bid" button
3. Confirm dialog appears asking to confirm
4. Click "OK" to confirm
```

### Step 2: Verify Approval
```
1. Page should reload
2. Success alert: "Bid accepted successfully!"
3. Bid card status should now show "ACCEPTED" (green badge)
4. Action buttons should be replaced with disabled "ACCEPTED" button
```

## Test Scenario 3: Reject a Bid

### Step 1: Place another bid (From customer account)
```
1. Logout from seller
2. Login as different customer (or same, but place on different product)
3. Place another bid on seller's product
4. Logout and login as seller
```

### Step 2: Reject the Bid
```
1. Navigate to seller dashboard
2. Click "Received Bids" tab
3. Find the new pending bid
4. Click "✗ Reject Bid" button
5. Confirm dialog appears
6. Click "OK" to confirm
```

### Step 3: Verify Rejection
```
1. Page should reload
2. Success alert: "Bid rejected successfully!"
3. Bid card status should show "REJECTED" (red badge)
4. Action buttons should show disabled "REJECTED" button
```

## Test Scenario 4: Multiple Bids Display

### Prerequisites
- 3+ pending bids from different customers

### Steps
```
1. Login as seller
2. Go to Received Bids tab
3. Should see all bids displayed in chronological order (newest first)
4. Each bid should have correct details
5. Should be able to approve/reject individually
6. Can scroll through multiple bid cards
```

## Test Scenario 5: Empty Bids State

### Prerequisites
- Seller with no received bids yet

### Steps
```
1. Login as seller with no bids
2. Go to Received Bids tab
3. Should see message: "You haven't received any bids yet."
4. Should also see: "When customers place bids on your products, they will appear here."
5. No bid cards should be visible
```

## Test Scenario 6: Responsive Design

### Desktop View (1920x1080)
```
1. Login as seller
2. Go to Received Bids tab
3. Verify:
   - Product image on left (80x80px)
   - Product info next to image
   - Bid details in 4-column grid
   - Buttons aligned to right
```

### Tablet View (768px)
```
1. Resize browser to 768px width
2. Verify:
   - Bid details in 2-column grid
   - Layout remains readable
   - Buttons still clickable
```

### Mobile View (375px)
```
1. Resize browser to 375px width or use mobile device
2. Verify:
   - Bid details stack vertically
   - Product image at top
   - All text readable
   - Buttons stack vertically
   - All functionality works
```

## Test Scenario 7: Bid Message Display

### Prerequisites
- Bid with message from customer

### Steps
```
1. Login as seller
2. Go to Received Bids tab
3. Find bid with message
4. Verify message displays in styled box:
   - Gray background
   - Amber left border
   - Italic text
   - Full message visible
5. Message should show: "Message from Bidder" label
```

## Test Scenario 8: Tab Navigation

### Steps
```
1. Login as seller
2. Click "My products" tab - should show products
3. Click "Add new product" tab - should show form
4. Click "Sold products" tab - should show sold products message
5. Click "Received Bids" tab - should show bids
6. Verify tab highlighting changes
7. Verify content sections switch properly
```

## Test Scenario 9: Data Accuracy

### Verify displayed data matches database
```
1. Login as seller in Received Bids tab
2. Note down:
   - Product name and price
   - Bid amount
   - Customer name and email
   - Bid timestamp
3. Compare with:
   - phpMyAdmin bids table
   - phpMyAdmin products table
   - phpMyAdmin users table
4. All data should match exactly
```

## Test Scenario 10: Security Check

### Seller Can Only See Own Bids
```
1. Login as Seller A
2. Go to Received Bids
3. Verify only bids on Seller A's products show
4. Logout, login as Seller B
5. Go to Received Bids
6. Verify only bids on Seller B's products show
7. Should NOT see Seller A's bids
```

### Bid Update Verification
```
1. Change bid status to 'accepted'
2. In database (phpMyAdmin):
   - Check bids table
   - Verify bid_status = 'accepted'
   - Verify updated_at timestamp changed
```

## Troubleshooting

### Bids Not Showing
- [ ] Verify bid is in database
- [ ] Verify bid.product_id matches seller's product
- [ ] Check seller_id matches logged-in user
- [ ] Check browser console for JavaScript errors

### Tab Not Switching
- [ ] Open browser developer tools (F12)
- [ ] Check Console tab for errors
- [ ] Verify CSS is loading (check Network tab)
- [ ] Verify JavaScript variables are defined

### Approve/Reject Not Working
- [ ] Check browser console for AJAX errors
- [ ] Verify update_bid_status.php exists
- [ ] Check that seller owns the product
- [ ] Verify update_bid_status.php returns JSON

### Styling Issues
- [ ] Clear browser cache (Ctrl+Shift+Del)
- [ ] Verify CSS classes are applied
- [ ] Check for conflicting styles
- [ ] Verify Tailwind CSS is loaded

## Expected Behaviors

### When Bid Placed
```
✓ Bid stored in database
✓ Bid status = 'pending'
✓ Customer receives confirmation
✓ Bid appears in Received Bids tab
```

### When Bid Approved
```
✓ Status changes to 'accepted'
✓ Status badge turns green
✓ Action buttons disabled
✓ Page reloads automatically
```

### When Bid Rejected
```
✓ Status changes to 'rejected'
✓ Status badge turns red
✓ Action buttons disabled
✓ Page reloads automatically
```

### Tab Navigation
```
✓ Only one tab content visible at a time
✓ Active tab highlighted (dark gray text)
✓ Inactive tabs light gray text
✓ Content switches immediately on click
```

## Success Criteria

The implementation is working correctly when:

- [x] Customers can place bids on products with bidding enabled
- [x] Sellers can see all bids on their products in Received Bids tab
- [x] Bids display with all customer and bid information
- [x] Sellers can approve bids (status changes to accepted)
- [x] Sellers can reject bids (status changes to rejected)
- [x] UI is responsive on mobile/tablet/desktop
- [x] Sellers only see bids on their own products
- [x] Tab navigation works smoothly
- [x] Action buttons work and update correctly
- [x] Empty state shows when no bids exist
- [x] All displayed data is accurate from database

---

**Last Updated**: December 3, 2025
**Status**: Ready for Testing
