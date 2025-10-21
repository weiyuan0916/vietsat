# Database Schema Documentation

## Overview
This document describes the complete database schema for the PWA E-commerce platform. The system supports vendors, products, orders, real-time chat, and various e-commerce features.

## Entity Relationship Diagram

```
Users (1) ──────── (1) Vendors
  │                    │
  │ (1)            (1) │
  │                    │
  ├─────── (N) Orders ─┤
  │            │
  │            │ (N)
  │            │
  │            └─── (N) OrderItems ──── (1) Products
  │                                            │
  │ (1)                                   (1) │
  │                                            │
  ├─────── (1) Carts ─── (N) CartItems ───────┤
  │                                            │
  │ (N)                                   (1) │
  │                                            │
  ├─────── (N) Wishlists ─────────────────────┤
  │                                            │
  │ (N)                                   (N) │
  │                                            │
  ├─────── (N) Reviews ───────────────────────┤
  │            │                               │
  │            │                          (N) │
  │            │                               │
  │            └─────── (1) Vendors            │
  │                                            │
  │ (1)                                   (N) │
  │                                            │
  ├─────── (N) Conversations ── (1) Vendors   │
  │            │                               │
  │            │ (N)                           │
  │            │                               │
  │            └─── (N) Messages               │
  │                                            │
  │ (N)                                   (1) │
  │                                            │
  └─────── (N) Notifications                  │
                                               │
                                          (1) │
                                               │
Categories (1) ──────────────────────────────┤
     │                                         │
     │ (N)                                     │
     │                                         │
     └── (N) Categories (Self-referencing)    │
                                               │
Collections (N) ──────── (N) Products ────────┘
```

## Tables

### 1. users
**Description**: Core user table supporting customers, vendors, and admins.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| name | varchar | NOT NULL | User's full name |
| email | varchar | UNIQUE, NOT NULL | User's email address |
| email_verified_at | timestamp | NULL | Email verification timestamp |
| password | varchar | NOT NULL | Hashed password |
| avatar | varchar | NULL | Profile picture path |
| phone | varchar | NULL | Contact phone number |
| balance | decimal(12,2) | DEFAULT 0.00 | User's wallet balance |
| role | enum | DEFAULT 'customer' | User role: customer/vendor/admin |
| is_online | boolean | DEFAULT false | Online status |
| last_seen_at | timestamp | NULL | Last activity timestamp |
| address | text | NULL | Street address |
| city | varchar | NULL | City name |
| state | varchar | NULL | State/Province |
| country | varchar | DEFAULT 'Vietnam' | Country |
| postal_code | varchar | NULL | Postal/ZIP code |
| remember_token | varchar | NULL | Session token |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: email
- INDEX: role, is_online

**Relationships:**
- Has one Vendor
- Has many Orders
- Has one Cart
- Has many Wishlists
- Has many Reviews
- Has many Conversations
- Has many Messages (sent/received)
- Has many Notifications

---

### 2. vendors
**Description**: Vendor/seller profiles with business metrics.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| user_id | bigint | FK, UNIQUE | Reference to users |
| shop_name | varchar | NOT NULL | Store name |
| slug | varchar | UNIQUE | URL-friendly identifier |
| description | text | NULL | Store description |
| logo | varchar | NULL | Store logo path |
| banner | varchar | NULL | Store banner image |
| phone | varchar | NULL | Business phone |
| email | varchar | NULL | Business email |
| website | varchar | NULL | Store website |
| address | varchar | NULL | Business address |
| city | varchar | NULL | Business city |
| state | varchar | NULL | Business state |
| country | varchar | DEFAULT 'Vietnam' | Business country |
| postal_code | varchar | NULL | Business postal code |
| latitude | decimal(10,8) | NULL | GPS latitude |
| longitude | decimal(11,8) | NULL | GPS longitude |
| rating | decimal(3,2) | DEFAULT 0.00 | Average rating (0-5) |
| total_reviews | int | DEFAULT 0 | Total review count |
| total_sales | int | DEFAULT 0 | Total sales count |
| positive_rating_percentage | decimal(5,2) | DEFAULT 0.00 | Positive reviews % |
| ship_on_time_percentage | decimal(5,2) | DEFAULT 0.00 | On-time shipping % |
| is_trusted | boolean | DEFAULT false | Trusted badge |
| is_active | boolean | DEFAULT true | Active status |
| is_verified | boolean | DEFAULT false | Verified status |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |
| deleted_at | timestamp | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: slug
- FK: user_id → users(id) ON DELETE CASCADE
- INDEX: slug, is_active, latitude/longitude

**Relationships:**
- Belongs to User
- Has many Products
- Has many Orders
- Has many Conversations
- Morph many Reviews

---

### 3. categories
**Description**: Product categories with hierarchical support.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| parent_id | bigint | FK, NULL | Parent category reference |
| name | varchar | NOT NULL | Category name |
| slug | varchar | UNIQUE | URL-friendly identifier |
| description | text | NULL | Category description |
| icon | varchar | NULL | Icon image path |
| image | varchar | NULL | Category image |
| order | int | DEFAULT 0 | Display order |
| is_active | boolean | DEFAULT true | Active status |
| is_featured | boolean | DEFAULT false | Featured flag |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: slug
- FK: parent_id → categories(id) ON DELETE CASCADE
- INDEX: slug, parent_id, is_active/is_featured

**Relationships:**
- Belongs to Parent Category (self)
- Has many Child Categories (self)
- Has many Products

---

### 4. products
**Description**: Product catalog with pricing, inventory, and SEO.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| vendor_id | bigint | FK | Reference to vendors |
| category_id | bigint | FK | Reference to categories |
| name | varchar | NOT NULL | Product name |
| slug | varchar | UNIQUE | URL-friendly identifier |
| description | text | NULL | Full description |
| short_description | text | NULL | Brief description |
| price | decimal(12,2) | NOT NULL | Regular price |
| sale_price | decimal(12,2) | NULL | Sale price |
| cost | decimal(12,2) | NULL | Cost price |
| sku | varchar | UNIQUE, NULL | Stock keeping unit |
| stock_quantity | int | DEFAULT 0 | Available quantity |
| low_stock_threshold | int | DEFAULT 5 | Low stock alert level |
| track_inventory | boolean | DEFAULT true | Enable inventory tracking |
| stock_status | enum | DEFAULT 'in_stock' | in_stock/out_of_stock/on_backorder |
| brand | varchar | NULL | Brand name |
| weight | decimal(10,2) | NULL | Product weight |
| dimensions | varchar | NULL | Product dimensions |
| specifications | json | NULL | Technical specifications |
| meta_title | varchar | NULL | SEO title |
| meta_description | text | NULL | SEO description |
| meta_keywords | varchar | NULL | SEO keywords |
| rating | decimal(3,2) | DEFAULT 0.00 | Average rating |
| review_count | int | DEFAULT 0 | Total reviews |
| view_count | int | DEFAULT 0 | Total views |
| sales_count | int | DEFAULT 0 | Total sales |
| wishlist_count | int | DEFAULT 0 | Times wishlisted |
| badge | varchar | NULL | Badge text (e.g., "Sale") |
| badge_color | varchar | NULL | Badge color class |
| is_featured | boolean | DEFAULT false | Featured flag |
| is_active | boolean | DEFAULT true | Active status |
| is_new | boolean | DEFAULT false | New product flag |
| published_at | timestamp | NULL | Publication date |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |
| deleted_at | timestamp | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: slug, sku
- FK: vendor_id → vendors(id) ON DELETE CASCADE
- FK: category_id → categories(id) ON DELETE RESTRICT
- INDEX: slug, vendor_id, category_id, is_active/is_featured, stock_status, rating
- FULLTEXT: name, description

**Relationships:**
- Belongs to Vendor
- Belongs to Category
- Has many Product Images
- Has one Flash Sale
- Morph many Reviews
- Belongs to many Collections
- Has many Wishlists
- Has many Cart Items
- Has many Order Items

---

### 5. product_images
**Description**: Product image gallery.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| product_id | bigint | FK | Reference to products |
| image_path | varchar | NOT NULL | Image file path |
| thumbnail_path | varchar | NULL | Thumbnail path |
| alt_text | varchar | NULL | Image alt text |
| order | int | DEFAULT 0 | Display order |
| is_primary | boolean | DEFAULT false | Primary image flag |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- FK: product_id → products(id) ON DELETE CASCADE
- INDEX: product_id, product_id/is_primary

**Relationships:**
- Belongs to Product

---

### 6. collections
**Description**: Curated product collections.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| name | varchar | NOT NULL | Collection name |
| slug | varchar | UNIQUE | URL-friendly identifier |
| description | text | NULL | Collection description |
| image | varchar | NULL | Collection image |
| order | int | DEFAULT 0 | Display order |
| is_active | boolean | DEFAULT true | Active status |
| is_featured | boolean | DEFAULT false | Featured flag |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: slug
- INDEX: slug, is_active/is_featured

**Relationships:**
- Belongs to many Products

---

### 7. collection_product (Pivot)
**Description**: Many-to-many relationship between collections and products.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| collection_id | bigint | FK | Reference to collections |
| product_id | bigint | FK | Reference to products |
| order | int | DEFAULT 0 | Display order |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: collection_id/product_id
- FK: collection_id → collections(id) ON DELETE CASCADE
- FK: product_id → products(id) ON DELETE CASCADE

---

### 8. flash_sales
**Description**: Flash sale promotions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| product_id | bigint | FK | Reference to products |
| title | varchar | NULL | Sale title |
| flash_price | decimal(12,2) | NOT NULL | Flash sale price |
| quantity_available | int | NULL | Limited quantity |
| quantity_sold | int | DEFAULT 0 | Sold quantity |
| sold_percentage | decimal(5,2) | DEFAULT 0.00 | Sold percentage |
| starts_at | timestamp | NOT NULL | Sale start time |
| ends_at | timestamp | NOT NULL | Sale end time |
| is_active | boolean | DEFAULT true | Active status |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- FK: product_id → products(id) ON DELETE CASCADE
- INDEX: product_id, starts_at/ends_at, is_active

**Relationships:**
- Belongs to Product

---

### 9. reviews
**Description**: Product and vendor reviews (polymorphic).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| user_id | bigint | FK | Reference to users |
| reviewable_id | bigint | NOT NULL | Polymorphic ID |
| reviewable_type | varchar | NOT NULL | Polymorphic type |
| order_id | bigint | FK, NULL | Reference to orders |
| rating | tinyint | NOT NULL | Rating (1-5) |
| title | varchar | NULL | Review title |
| comment | text | NULL | Review content |
| images | json | NULL | Review images |
| is_verified_purchase | boolean | DEFAULT false | Verified purchase |
| is_approved | boolean | DEFAULT true | Approved status |
| helpful_count | int | DEFAULT 0 | Helpful votes |
| approved_at | timestamp | NULL | Approval timestamp |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- FK: user_id → users(id) ON DELETE CASCADE
- FK: order_id → orders(id) ON DELETE SET NULL
- INDEX: user_id, reviewable_type/reviewable_id, rating, is_approved

**Relationships:**
- Belongs to User
- Belongs to Order
- Morph to Reviewable (Product or Vendor)

---

### 10. wishlists
**Description**: User wishlist items.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| user_id | bigint | FK | Reference to users |
| product_id | bigint | FK | Reference to products |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: user_id/product_id
- FK: user_id → users(id) ON DELETE CASCADE
- FK: product_id → products(id) ON DELETE CASCADE

**Relationships:**
- Belongs to User
- Belongs to Product

---

### 11. carts
**Description**: Shopping carts.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| user_id | bigint | FK, NULL | Reference to users |
| session_id | varchar | NULL | Guest session ID |
| subtotal | decimal(12,2) | DEFAULT 0.00 | Items subtotal |
| tax | decimal(12,2) | DEFAULT 0.00 | Tax amount |
| shipping | decimal(12,2) | DEFAULT 0.00 | Shipping cost |
| discount | decimal(12,2) | DEFAULT 0.00 | Discount amount |
| total | decimal(12,2) | DEFAULT 0.00 | Total amount |
| coupon_code | varchar | NULL | Applied coupon |
| expires_at | timestamp | NULL | Expiration time |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- FK: user_id → users(id) ON DELETE CASCADE
- INDEX: user_id, session_id

**Relationships:**
- Belongs to User
- Has many Cart Items

---

### 12. cart_items
**Description**: Items in shopping carts.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| cart_id | bigint | FK | Reference to carts |
| product_id | bigint | FK | Reference to products |
| quantity | int | DEFAULT 1 | Item quantity |
| price | decimal(12,2) | NOT NULL | Unit price |
| subtotal | decimal(12,2) | NOT NULL | Line total |
| options | json | NULL | Product options |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: cart_id/product_id
- FK: cart_id → carts(id) ON DELETE CASCADE
- FK: product_id → products(id) ON DELETE CASCADE

**Relationships:**
- Belongs to Cart
- Belongs to Product

---

### 13. orders
**Description**: Customer orders.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| order_number | varchar | UNIQUE | Unique order number |
| user_id | bigint | FK | Reference to users |
| vendor_id | bigint | FK, NULL | Reference to vendors |
| subtotal | decimal(12,2) | NOT NULL | Items subtotal |
| tax | decimal(12,2) | DEFAULT 0.00 | Tax amount |
| shipping | decimal(12,2) | DEFAULT 0.00 | Shipping cost |
| discount | decimal(12,2) | DEFAULT 0.00 | Discount amount |
| total | decimal(12,2) | NOT NULL | Total amount |
| status | enum | DEFAULT 'pending' | Order status |
| payment_status | enum | DEFAULT 'pending' | Payment status |
| payment_method | varchar | NULL | Payment method |
| payment_transaction_id | varchar | NULL | Transaction ID |
| shipping_name | varchar | NOT NULL | Recipient name |
| shipping_email | varchar | NOT NULL | Recipient email |
| shipping_phone | varchar | NOT NULL | Recipient phone |
| shipping_address | text | NOT NULL | Delivery address |
| shipping_city | varchar | NOT NULL | Delivery city |
| shipping_state | varchar | NULL | Delivery state |
| shipping_postal_code | varchar | NULL | Delivery postal code |
| shipping_country | varchar | DEFAULT 'Vietnam' | Delivery country |
| tracking_number | varchar | NULL | Shipment tracking |
| carrier | varchar | NULL | Shipping carrier |
| notes | text | NULL | Order notes |
| coupon_code | varchar | NULL | Applied coupon |
| meta_data | json | NULL | Additional data |
| confirmed_at | timestamp | NULL | Confirmation time |
| shipped_at | timestamp | NULL | Shipping time |
| delivered_at | timestamp | NULL | Delivery time |
| cancelled_at | timestamp | NULL | Cancellation time |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |
| deleted_at | timestamp | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: order_number
- FK: user_id → users(id) ON DELETE RESTRICT
- FK: vendor_id → vendors(id) ON DELETE SET NULL
- INDEX: order_number, user_id, vendor_id, status, payment_status, created_at

**Relationships:**
- Belongs to User
- Belongs to Vendor
- Has many Order Items
- Has many Reviews

---

### 14. order_items
**Description**: Items in orders.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| order_id | bigint | FK | Reference to orders |
| product_id | bigint | FK | Reference to products |
| product_name | varchar | NOT NULL | Product name snapshot |
| product_sku | varchar | NULL | Product SKU snapshot |
| quantity | int | NOT NULL | Ordered quantity |
| price | decimal(12,2) | NOT NULL | Unit price |
| subtotal | decimal(12,2) | NOT NULL | Line total |
| options | json | NULL | Product options |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- FK: order_id → orders(id) ON DELETE CASCADE
- FK: product_id → products(id) ON DELETE RESTRICT

**Relationships:**
- Belongs to Order
- Belongs to Product

---

### 15. conversations
**Description**: Chat conversations between users and vendors.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| user_id | bigint | FK | Reference to users |
| vendor_id | bigint | FK | Reference to vendors |
| last_message_at | timestamp | NULL | Last message time |
| unread_count_user | int | DEFAULT 0 | User unread count |
| unread_count_vendor | int | DEFAULT 0 | Vendor unread count |
| is_active | boolean | DEFAULT true | Active status |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- UNIQUE: user_id/vendor_id
- FK: user_id → users(id) ON DELETE CASCADE
- FK: vendor_id → vendors(id) ON DELETE CASCADE
- INDEX: user_id, vendor_id, last_message_at

**Relationships:**
- Belongs to User
- Belongs to Vendor
- Has many Messages

---

### 16. messages
**Description**: Chat messages.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| conversation_id | bigint | FK | Reference to conversations |
| sender_id | bigint | FK | Reference to users |
| receiver_id | bigint | FK | Reference to users |
| message | text | NOT NULL | Message content |
| attachments | json | NULL | File attachments |
| is_read | boolean | DEFAULT false | Read status |
| read_at | timestamp | NULL | Read timestamp |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- FK: conversation_id → conversations(id) ON DELETE CASCADE
- FK: sender_id → users(id) ON DELETE CASCADE
- FK: receiver_id → users(id) ON DELETE CASCADE
- INDEX: conversation_id, sender_id, receiver_id, is_read, created_at

**Relationships:**
- Belongs to Conversation
- Belongs to Sender (User)
- Belongs to Receiver (User)

---

### 17. notifications
**Description**: User notifications.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, Auto | Primary key |
| user_id | bigint | FK | Reference to users |
| type | varchar | NOT NULL | Notification type |
| title | varchar | NOT NULL | Notification title |
| message | text | NOT NULL | Notification message |
| data | json | NULL | Additional data |
| action_url | varchar | NULL | Action URL |
| is_read | boolean | DEFAULT false | Read status |
| read_at | timestamp | NULL | Read timestamp |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

**Indexes:**
- PRIMARY: id
- FK: user_id → users(id) ON DELETE CASCADE
- INDEX: user_id, type, is_read, created_at

**Relationships:**
- Belongs to User

---

## Key Features

### 1. **Multi-Vendor Support**
- Vendors linked to user accounts
- Business metrics tracking (rating, sales, on-time delivery)
- GPS coordinates for map display

### 2. **Product Management**
- Full inventory tracking
- SEO optimization fields
- Multiple images per product
- Flash sales support
- Product collections

### 3. **Shopping Experience**
- Guest and authenticated carts
- Wishlist functionality
- Advanced search (Laravel Scout)
- Product reviews and ratings

### 4. **Order Management**
- Complete order lifecycle tracking
- Multiple payment methods
- Order history
- Tracking numbers

### 5. **Real-time Communication**
- User-vendor conversations
- Message read status
- Attachment support
- Notifications system

### 6. **Polymorphic Relationships**
- Reviews can be for products or vendors
- Flexible and extensible

---

## Migration Order

Execute migrations in this order to respect foreign key constraints:

1. users (modify existing)
2. vendors
3. categories
4. products
5. product_images
6. collections
7. collection_product
8. flash_sales
9. reviews
10. wishlists
11. carts
12. cart_items
13. orders
14. order_items
15. conversations
16. messages
17. notifications

---

## Data Integrity Rules

### Cascading Deletes
- User deletion → Cascades to: vendors, orders (restricted), carts, wishlists, reviews, conversations, messages, notifications
- Vendor deletion → Cascades to: products, conversations
- Product deletion → Cascades to: product_images, cart_items, wishlists, flash_sales; Restricted for: order_items
- Cart deletion → Cascades to: cart_items
- Order deletion → Cascades to: order_items
- Conversation deletion → Cascades to: messages

### Soft Deletes
- vendors
- products
- orders

---

## Performance Considerations

### Indexes
All foreign keys are indexed for optimal join performance.

### Full-Text Search
Products table has full-text index on name and description for fast search.

### Recommended Additional Indexes
```sql
-- Product search optimization
CREATE INDEX idx_products_active_featured ON products(is_active, is_featured, created_at);

-- Order filtering
CREATE INDEX idx_orders_status_created ON orders(status, created_at);

-- Popular products
CREATE INDEX idx_products_sales_count ON products(sales_count DESC);
```

---

## Security Considerations

1. **Password Hashing**: User passwords are hashed using Laravel's bcrypt
2. **Soft Deletes**: Critical data (vendors, products, orders) use soft deletes
3. **Foreign Key Constraints**: Enforce referential integrity
4. **Role-based Access**: User roles (customer, vendor, admin) control permissions

---

## Extensibility

The schema supports:
- Additional user roles
- Custom product attributes (via JSON)
- Extended vendor metrics
- Additional notification types
- Multiple vendor addresses (future enhancement)
- Product variations (future enhancement)

---

**Last Updated**: October 14, 2025
**Version**: 1.0

