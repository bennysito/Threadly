# Threadly Bidding System Documentation

## Overview
The bidding system allows users to place bids on products, with all bid details stored in the database. This enables sellers to see offers from buyers before deciding whether to accept.

## Database Structure

### `bids` Table
The system uses a new `bids` table with the following columns:

```sql
CREATE TABLE IF NOT EXISTS bids (
    bid_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    bid_amount DECIMAL(10, 2) NOT NULL,
    bid_status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
    bid_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (bid_status)
)
```

## Setup Instructions

### Step 1: Create the Bidding Table
1. Open your browser and navigate to: `http://localhost/Threadly/Back_End/setup_bidding.php`
2. You should see a success message confirming the table was created
3. **Delete** `Back_End/setup_bidding.php` after setup for security

### Step 2: Files Created/Modified

#### New Files:
- `Back_End/Models/Bidding.php` - Main bidding model with database operations
- `Back_End/setup_bidding.php` - One-time setup script (delete after use)
- `Front_End/place_bid.php` - AJAX handler for placing bids
- `Front_End/get_bids.php` - AJAX handler for fetching bid information
- `Front_End/my_bids.php` - User's bid history page

#### Modified Files:
- `Front_End/product_info.php` - Added bidding UI and JavaScript functions

## Features

### 1. Place a Bid on Product Page
- Users can enter a bid amount (must be ≥ product price)
- Optional message field for communication
- Real-time validation
- Bid history tracking

### 2. Bid Information Display
- Shows current highest bid
- Displays bidder name
- Total bid count
- User's existing bid (if any)

### 3. My Bids Page (`my_bids.php`)
- View all bids placed by the user
- See bid status (pending, accepted, rejected, withdrawn)
- See original product price vs bid amount
- Quick links to product pages

### 4. Bid Status Types
- **Pending**: Initial state when bid is placed
- **Accepted**: Seller approved the bid
- **Rejected**: Seller declined the bid
- **Withdrawn**: User canceled their bid

## API Endpoints

### Place Bid
**POST** `/Front_End/place_bid.php`

Parameters:
```json
{
    "product_id": 123,
    "bid_amount": 1500.50,
    "bid_message": "Optional message for seller"
}
```

Response:
```json
{
    "success": true,
    "message": "Bid placed successfully!",
    "bid_id": 45,
    "bid_amount": 1500.50,
    "product_name": "Product Name"
}
```

### Get Bid Information
**GET** `/Front_End/get_bids.php?product_id=123`

Response:
```json
{
    "success": true,
    "highest_bid": {
        "bid_id": 45,
        "user_id": 2,
        "bid_amount": "1500.50",
        "bid_status": "pending",
        "created_at": "2024-12-02 10:30:00",
        "full_name": "John Doe"
    },
    "all_bids_count": 5,
    "user_bid": {
        "bid_id": 45,
        "bid_amount": "1500.50",
        "bid_status": "pending",
        "bid_message": "Optional message",
        "created_at": "2024-12-02 10:30:00"
    }
}
```

## Usage Examples

### For Users (Buyers)
1. Navigate to any product page
2. Scroll to "Make an Offer (Bidding)" section
3. Enter bid amount (minimum = product price)
4. Optionally add a message
5. Click "PLACE BID"
6. View their bids at `/Front_End/my_bids.php`

### For Developers
To create seller-side bid management (accepting/rejecting bids), use the Bidding class methods:

```php
require_once 'Models/Bidding.php';

$bidding = new Bidding();

// Get all bids for a product
$bids = $bidding->getBidsForProduct($product_id);

// Get highest bid
$highest = $bidding->getHighestBid($product_id);

// Update bid status
$bidding->updateBidStatus($bid_id, 'accepted');

// Withdraw a bid
$bidding->withdrawBid($bid_id, $user_id);
```

## Security Features
- User authentication required (checks session)
- Prepared statements prevent SQL injection
- Input validation on bid amounts
- Foreign key constraints maintain data integrity
- Timestamped records for audit trail

## Future Enhancements
1. Seller dashboard to accept/reject bids
2. Bid negotiations with counter-offers
3. Automatic bid expiration
4. Bid notifications/emails
5. Bid history analytics
6. Auto-accept highest bid feature

## Troubleshooting

**Issue**: "Not authorized" error when placing bid
- **Solution**: Ensure user is logged in

**Issue**: "Invalid product ID"
- **Solution**: Check that product exists in database

**Issue**: "Bid amount must be at least..."
- **Solution**: Bid amount must be ≥ product price

**Issue**: Database errors after setup
- **Solution**: Verify the bids table was created by checking database, or run setup_bidding.php again
