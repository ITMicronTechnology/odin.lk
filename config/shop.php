<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Store details
    |--------------------------------------------------------------------------
    |
    | Contact details shown in the header, footer and on order pages.
    |
    */

    'name' => env('SHOP_NAME', 'Odin.lk'),
    'tagline' => 'Sri Lanka\'s Technology Store',
    'hotline' => env('SHOP_HOTLINE', '011 234 5678'),
    'whatsapp' => env('SHOP_WHATSAPP', '077 123 4567'),
    'email' => env('SHOP_EMAIL', 'support@odin.lk'),
    'address' => env('SHOP_ADDRESS', 'No. 125, Galle Road, Colombo 03, Sri Lanka'),

    // Where new-order and IT service quote alerts are emailed.
    'order_alert_email' => env('SHOP_ORDER_ALERT_EMAIL', env('MAIL_FROM_ADDRESS')),

    /*
    |--------------------------------------------------------------------------
    | Pricing
    |--------------------------------------------------------------------------
    */

    'currency' => 'Rs.',
    'shipping_fee' => (int) env('SHOP_SHIPPING_FEE', 450),
    'free_shipping_threshold' => (int) env('SHOP_FREE_SHIPPING_OVER', 25000),
    'max_quantity_per_item' => 10,

    /*
    |--------------------------------------------------------------------------
    | Bank details for bank-transfer orders
    |--------------------------------------------------------------------------
    */

    'bank' => [
        'name' => 'Commercial Bank of Ceylon',
        'account_name' => 'Odin Technologies (Pvt) Ltd',
        'account_number' => '1000 1234 5678',
        'branch' => 'Colombo 03',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment partners shown in the footer
    |--------------------------------------------------------------------------
    |
    | Logo file (in public/images/payment-partners) => name used as alt text.
    |
    */

    'payment_partners' => [
        'visa.webp' => 'Visa',
        'mastercard.webp' => 'Mastercard',
        'american-express.webp' => 'American Express',
        'koko.webp' => 'Koko',
        'commercial-bank.webp' => 'Commercial Bank',
        'hnb.webp' => 'HNB',
        'sampath-bank.webp' => 'Sampath Bank',
        'boc.webp' => 'Bank of Ceylon',
        'peoples-bank.webp' => "People's Bank",
        'ndb-bank.webp' => 'NDB Bank',
        'dfcc-bank.webp' => 'DFCC Bank',
        'nations-trust-bank.webp' => 'Nations Trust Bank',
        'hsbc.webp' => 'HSBC',
        'nsb.webp' => 'National Savings Bank',
        'cdb.webp' => 'CDB',
        'union-bank.webp' => 'Union Bank',
    ],

    /*
    |--------------------------------------------------------------------------
    | Easy payment plans (card instalments)
    |--------------------------------------------------------------------------
    |
    | Shown as "Monthly Rs. ..." on product cards and as bank tiles on product
    | pages. Set SHOP_INSTALLMENTS=false to hide them. Logos come from
    | public/images/payment-partners.
    |
    */

    'installments' => [
        'enabled' => (bool) env('SHOP_INSTALLMENTS', true),
        'min_price' => 15000,
        'banks' => [
            ['name' => 'Commercial Bank', 'logo' => 'commercial-bank.webp', 'months' => 24],
            ['name' => 'Sampath Bank', 'logo' => 'sampath-bank.webp', 'months' => 12],
            ['name' => 'HNB', 'logo' => 'hnb.webp', 'months' => 24],
            ['name' => 'Nations Trust Bank', 'logo' => 'nations-trust-bank.webp', 'months' => 12],
            ['name' => 'Bank of Ceylon', 'logo' => 'boc.webp', 'months' => 24],
            ['name' => 'DFCC Bank', 'logo' => 'dfcc-bank.webp', 'months' => 12],
        ],
        // "or 3 x Rs. ... with Koko" (buy now, pay later)
        'koko_parts' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Offer pages (home page offer tiles link to /offers/{slug})
    |--------------------------------------------------------------------------
    */

    'offers' => [
        'new-arrivals' => ['title' => 'New Arrivals', 'subtitle' => 'Just landed', 'icon' => 'bi-stars', 'from' => '#7c3aed', 'to' => '#c026d3'],
        'hot-deals' => ['title' => 'Hot Deals', 'subtitle' => 'Biggest discounts', 'icon' => 'bi-lightning-charge-fill', 'from' => '#e10600', 'to' => '#f97316'],
        'above-10-off' => ['title' => 'Above 10% Off', 'subtitle' => 'Save more today', 'icon' => 'bi-percent', 'from' => '#0a3d91', 'to' => '#2563eb', 'min_discount' => 10],
        'below-10000' => ['title' => 'Below Rs. 10,000', 'subtitle' => 'Budget picks', 'icon' => 'bi-tag-fill', 'from' => '#db2777', 'to' => '#f43f5e', 'max_price' => 10000],
        'below-50000' => ['title' => 'Below Rs. 50,000', 'subtitle' => 'Great value tech', 'icon' => 'bi-wallet2', 'from' => '#ea580c', 'to' => '#facc15', 'max_price' => 50000],
        'top-selling' => ['title' => 'Top Selling', 'subtitle' => 'Customer favourites', 'icon' => 'bi-trophy-fill', 'from' => '#0b0b0f', 'to' => '#374151'],
    ],

    'support_hours' => 'Mon - Sat, 9.00 AM - 7.00 PM',

    'districts' => [
        'Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota',
        'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale',
        'Matara', 'Monaragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura',
        'Trincomalee', 'Vavuniya',
    ],

];
