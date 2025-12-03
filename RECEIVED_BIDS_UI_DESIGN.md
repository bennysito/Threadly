# Received Bids Tab - UI Design Overview

## Tab Navigation

```
┌─────────────────────────────────────────────────────────────────┐
│                      SELLER DASHBOARD                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  Tabs:  My products  │  Add new product  │  Sold products  │  ✓ Received Bids  │
│         ─────────────────────────────────────────────────────    │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                 RECEIVED BIDS SECTION                     │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Empty State

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                   │
│                                                                   │
│                                                                   │
│               You haven't received any bids yet.                 │
│                                                                   │
│      When customers place bids on your products, they will       │
│                        appear here.                              │
│                                                                   │
│                                                                   │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Single Bid Card Layout

```
┌──────────────────────────────────────────────────────────────────────┐
│ BID CARD HEADER                                                       │
├──────────────────────────────────────────────────────────────────────┤
│ ┌──────────┐                                                          │
│ │          │  Nike Air Max 270                                        │
│ │ PRODUCT  │  Original Price: ₱4,500.00                              │
│ │  IMAGE   │  [PENDING]                                              │
│ │          │                                                          │
│ └──────────┘                                                          │
├──────────────────────────────────────────────────────────────────────┤
│ BID DETAILS SECTION                                                  │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  BID AMOUNT              BIDDER                                     │
│  ₱5,200.00              Juan Dela Cruz                             │
│                         @juandc23                                  │
│                                                                      │
│  CONTACT INFO            BID DATE                                  │
│  juan@email.com         Dec 03, 2024 02:45 PM                    │
│  +63 9123456789                                                    │
│                                                                      │
├──────────────────────────────────────────────────────────────────────┤
│ MESSAGE SECTION                                                      │
├──────────────────────────────────────────────────────────────────────┤
│  Message from Bidder                                                │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ "Interested in this product. Willing to pay extra if you can   │ │
│  │  deliver within 3 days. Let me know!"                          │ │
│  └────────────────────────────────────────────────────────────────┘ │
├──────────────────────────────────────────────────────────────────────┤
│ ACTION BUTTONS                                                       │
├──────────────────────────────────────────────────────────────────────┤
│                                         [✓ Approve Bid] [✗ Reject] │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Status Badge Variants

```
┌──────────────────────────────────────────────────────────┐
│ Status Badge Colors:                                     │
│                                                          │
│ [PENDING]    - Yellow background, brown text           │
│ [ACCEPTED]   - Green background, dark green text       │
│ [REJECTED]   - Red background, dark red text           │
│ [WITHDRAWN]  - Gray background, dark gray text         │
└──────────────────────────────────────────────────────────┘
```

## Multiple Bids Display

```
┌──────────────────────────────────────────────────────────────────────┐
│ BID 1 - Nike Air Max 270 - [PENDING]                                 │
│ ├─ Bid Amount: ₱5,200.00 | Bidder: Juan DC | Dec 03, 2:45 PM       │
│ └─ [✓ Approve] [✗ Reject]                                           │
├──────────────────────────────────────────────────────────────────────┤
│ BID 2 - Nike Air Max 270 - [ACCEPTED]                                │
│ ├─ Bid Amount: ₱4,800.00 | Bidder: Maria Garcia | Dec 03, 1:20 PM  │
│ └─ [ACCEPTED (disabled)]                                            │
├──────────────────────────────────────────────────────────────────────┤
│ BID 3 - Adidas Ultraboost - [PENDING]                                │
│ ├─ Bid Amount: ₱3,900.00 | Bidder: Carlos Lopez | Dec 03, 12:30 PM │
│ └─ [✓ Approve] [✗ Reject]                                           │
├──────────────────────────────────────────────────────────────────────┤
│ BID 4 - Adidas Ultraboost - [REJECTED]                               │
│ ├─ Bid Amount: ₱3,500.00 | Bidder: Ana Santos | Dec 02, 5:15 PM    │
│ └─ [REJECTED (disabled)]                                            │
└──────────────────────────────────────────────────────────────────────┘
```

## Responsive Mobile View (375px)

```
┌─────────────────────────────────┐
│ SELLER DASHBOARD - MOBILE       │
├─────────────────────────────────┤
│                                 │
│ [My products]                   │
│ [Add product]                   │
│ [Sold products]                 │
│ [Received Bids] ✓ ACTIVE       │
│                                 │
├─────────────────────────────────┤
│ BID CARD                         │
│ ┌─────────────────────────────┐ │
│ │  ┌─────────────┐            │ │
│ │  │             │  Nike      │ │
│ │  │   IMAGE     │  Max 270   │ │
│ │  │             │  ₱4,500    │ │
│ │  │             │  [PENDING] │ │
│ │  └─────────────┘            │ │
│ │                             │ │
│ │  BID AMOUNT                 │ │
│ │  ₱5,200.00                  │ │
│ │                             │ │
│ │  BIDDER                     │ │
│ │  Juan DC                    │ │
│ │  @juandc23                  │ │
│ │                             │ │
│ │  CONTACT                    │ │
│ │  juan@email.com             │ │
│ │  +63 9123456789             │ │
│ │                             │ │
│ │  BID DATE                   │ │
│ │  Dec 03, 2024 02:45 PM     │ │
│ │                             │ │
│ │  MESSAGE                    │ │
│ │  "Interested! Can deliver   │ │
│ │   in 3 days?"               │ │
│ │                             │ │
│ │  [✓ APPROVE BID]            │ │
│ │  [✗ REJECT BID]             │ │
│ │                             │ │
│ └─────────────────────────────┘ │
│                                 │
└─────────────────────────────────┘
```

## Color Scheme

```
PRIMARY COLORS:
├─ Accent Color: #b45309 (Amber/Brown)
├─ Background: #f9fafb (Light Gray)
├─ Card Background: #ffffff (White)
└─ Text Primary: #111111 (Dark Gray/Black)

STATUS COLORS:
├─ Pending: #fef3c7 (bg), #92400e (text) - Yellow
├─ Accepted: #d1fae5 (bg), #065f46 (text) - Green
├─ Rejected: #fee2e2 (bg), #7f1d1d (text) - Red
└─ Withdrawn: #f3f4f6 (bg), #374151 (text) - Gray

BUTTON COLORS:
├─ Approve: #10b981 (Green)
├─ Reject: #ef4444 (Red)
└─ Disabled: #9ca3af (Gray)
```

## Typography

```
FONT SIZES:
├─ Card Title: 1rem (16px)
├─ Label: 0.85rem (13px)
├─ Value: 1.05rem (16px)
├─ Amount Highlight: 1.75rem (28px)
└─ Message: 0.95rem (15px)

FONT WEIGHTS:
├─ Section Header: 700 (Bold)
├─ Card Title: 600 (Semibold)
├─ Label: 600 (Semibold)
└─ Body: 400 (Normal)
```

## Spacing

```
CARD SPACING:
├─ Padding: 1.5rem (24px)
├─ Gap between sections: 1.5rem
├─ Border radius: 12px
└─ Margin bottom: 1.5rem

GRID LAYOUT:
├─ Desktop: 4-column auto-fit grid (200px min)
├─ Tablet: 2-column grid
└─ Mobile: 1-column stack
```

## Interactive States

```
BUTTON STATES:

NORMAL:
[✓ Approve Bid]  [✗ Reject Bid]
├─ Cursor: pointer
└─ Background: Solid color

HOVER:
[✓ Approve Bid]  [✗ Reject Bid]
├─ Brightness: increased
└─ Shadow: enhanced

CLICK:
Confirmation dialog appears
├─ "Are you sure you want to [action] this bid?"
├─ [OK]  [Cancel]
└─ On confirm: AJAX request sent

LOADING:
[✓ Approve Bid]  [✗ Reject Bid]
├─ Opacity: reduced
└─ Cursor: wait

DISABLED:
[ACCEPTED]  [REJECTED]
├─ Opacity: 50%
└─ Cursor: not-allowed
```

## Card Hover Effect

```
NORMAL STATE:
┌──────────────────────────────┐
│  Bid Card                     │
│  Shadow: light               │
│  Border: light gray          │
└──────────────────────────────┘

HOVER STATE:
┌──────────────────────────────┐
│  Bid Card                     │
│  Shadow: enhanced            │
│  Border: amber               │
│  Transform: lifted           │
└──────────────────────────────┘
```

## Notification Messages

```
SUCCESS: "Bid accepted successfully!"
├─ Type: Alert popup
├─ Color: Green
└─ Action: Page reloads

SUCCESS: "Bid rejected successfully!"
├─ Type: Alert popup
├─ Color: Green
└─ Action: Page reloads

ERROR: "Error: [message]"
├─ Type: Alert popup
├─ Color: Red
└─ Action: None (dismiss to retry)

CONFIRMATION: "Are you sure you want to [action] this bid?"
├─ Type: Confirm dialog
├─ Options: [OK]  [Cancel]
└─ Action: Proceed if OK, cancel if Cancel
```

---

**UI Design Document**
**Version**: 1.0
**Last Updated**: December 3, 2025
