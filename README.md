# Odin.lk — Technology E-commerce Store

Odin.lk is an online store for technology products in Sri Lanka: laptops, mobile phones (smartphones, tablets, smartwatches), desktop PCs, monitors, printers, computer accessories, CCTV, power banks, Soundcore/JBL audio, networking and storage. It has a customer storefront (styled after buyabans.com, in Odin red, blue, black and white) and an admin panel for managing the catalogue and orders.

Built with Laravel 13, PHP 8.3+, MySQL (XAMPP), Bootstrap 5, jQuery and Blade templates.

---

## Login credentials

> These are **demo accounts** created by the sample data. Change the admin password before the site goes live (My Account → Profile & Password).

### Website accounts

| Role | Email | Password | Where to log in |
|---|---|---|---|
| **Admin** | `admin@odin.lk` | `password` | http://127.0.0.1:8000/login, then go to http://127.0.0.1:8000/admin |
| Customer | `customer@odin.lk` | `password` | http://127.0.0.1:8000/login |
| Customer | `nimali@example.com` | `password` | |
| Customer | `tharindu@example.com` | `password` | |
| Customer | `ayesha@example.com` | `password` | |
| Customer | `ruwan@example.com` | `password` | |

The customer accounts already have sample orders, so the admin dashboard and order history have data to show.

### Database

| Setting | Value |
|---|---|
| Server | `127.0.0.1` (XAMPP MySQL / MariaDB) |
| Port | `3306` |
| Database name | `odin_lk` |
| Username | `root` |
| Password | *(empty)* |
| phpMyAdmin | http://localhost/phpmyadmin (needs Apache running in XAMPP) |

These values are set in the `.env` file (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

### Store contact and bank details

The hotline, WhatsApp number, email, address and bank account shown on the site are **placeholders**. Replace them in [`config/shop.php`](config/shop.php):

| Item | Placeholder value |
|---|---|
| Hotline | 011 234 5678 |
| WhatsApp | 077 123 4567 |
| Email | support@odin.lk |
| Address | No. 125, Galle Road, Colombo 03 |
| Bank (for bank-transfer orders) | Commercial Bank of Ceylon, Colombo 03, A/C 1000 1234 5678, Odin Technologies (Pvt) Ltd |
| Delivery fee | Rs. 450, free on orders over Rs. 25,000 |

---

## Requirements

- PHP 8.3 or newer (with `pdo_mysql`, `mbstring`, `gd`, `fileinfo`, `openssl`)
- Composer
- XAMPP (for MySQL/MariaDB)
- An internet connection is optional: everything is served locally except the Poppins font from Google Fonts (without it the site falls back to a system font)

---

## Running the project

If the project is already set up on this computer:

1. Open the **XAMPP Control Panel** and click **Start** next to **MySQL**.
2. Open a terminal in the `Odin` folder and run:
   ```bash
   php artisan serve
   ```
3. Open http://127.0.0.1:8000 in your browser.

Keep the terminal open while you use the site. Press `Ctrl + C` to stop it.

---

## Setting up on a new computer

1. Copy the whole `Odin` folder to the new computer, then install the PHP packages:
   ```bash
   composer install
   ```
2. Create the environment file (skip if `.env` already exists):
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
3. Start **MySQL** in XAMPP.
4. Create the database. Choose **one** option:

   **Option A: import the included SQL file** (recommended; restores the exact data)
   ```bash
   C:\xampp\mysql\bin\mysql -u root < database/odin.sql
   ```
   or in phpMyAdmin: **Import** → choose `database/odin.sql` → **Go**. The file creates the `odin_lk` database itself.

   **Option B: build fresh sample data with Laravel**
   ```bash
   php artisan migrate:fresh --seed
   ```
   This also regenerates all the sample images.

5. Link the image storage folder so uploaded images can be displayed:
   ```bash
   php artisan storage:link
   ```
6. Start the site:
   ```bash
   php artisan serve
   ```

> **About images:** `odin.sql` stores only the image *file paths*. The image files themselves are in `storage/app/public` (products, categories, brands, banners). If you import the SQL on another computer, copy that folder too, or use Option B instead.

---

## Email

The site sends three emails:

- **Order confirmation** to the email address the customer entered at checkout: items, totals, payment method (with the bank details for bank transfers), delivery address and a "Track your order" button.
- **New order alert** to the store, with a link to the order in the admin panel.
- **New IT service quote request alert** to the store, with a link to the request in the admin panel.

Emails are sent through Gmail for now. Add these lines to `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=itmicrontechholdings@gmail.com
MAIL_PASSWORD=your-gmail-app-password
MAIL_FROM_ADDRESS=itmicrontechholdings@gmail.com
MAIL_FROM_NAME="Odin.lk"
SHOP_ORDER_ALERT_EMAIL=itmicrontechholdings@gmail.com
```

- `MAIL_PASSWORD` must be a **Gmail App Password**, not the normal Gmail password. Turn on 2-Step Verification for the Google account, then create one at https://myaccount.google.com/apppasswords and paste the 16 characters (without spaces). Never commit it.
- `SHOP_ORDER_ALERT_EMAIL` is where store alerts go. If it is not set, `MAIL_FROM_ADDRESS` is used.
- After changing `.env`, run `php artisan config:clear` (or `php artisan optimize:clear`).
- The sender address will change to an **@odin.lk** address once the domain is bought. Only `.env` needs to change (`MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` and, if wanted, `SHOP_ORDER_ALERT_EMAIL`); no code changes.
- Set `MAIL_MAILER=log` to stop sending and write emails to `storage/logs/laravel.log` instead.
- If an email cannot be sent, the order or quote request is still saved and the customer still sees the success page; the error is written to `storage/logs/laravel.log`.
- Emails are sent straight away (no queue worker needed). To send them in the background later, add `implements ShouldQueue` to the classes in `app/Mail` and run `php artisan queue:work`.

---

## About `database/odin.sql`

- A full export of the `odin_lk` database: all 25 tables with their data (6 users, 40 categories, 33 brands, 81 products, 243 product images, 138 product reviews, 25 orders, 7 banners).
- The `sessions`, `cache` and `jobs` tables are included as empty tables, so no one's login session is saved in the file.
- Importing it **replaces** the existing `odin_lk` tables. Back up first if you have real orders.
- To make a new export after you add real products:
  ```bash
  C:\xampp\mysql\bin\mysqldump -u root --routines --add-drop-table --databases odin_lk > database/odin.sql
  ```

---

## Admin panel

Go to http://127.0.0.1:8000/admin (log in as the admin first).

| Section | What you can do |
|---|---|
| Dashboard | Revenue, orders, 14-day sales chart, orders by status, top sellers, low-stock alerts |
| Products | Add, edit, hide or delete products; upload several images; set the main image; set sale prices, stock and specifications |
| Categories | Main categories and sub-categories, icons, images, choose which appear as sections on the home page |
| Brands | Brand names and logos |
| Home Banners | Main slider (1600×520), promo banners (800×300) and wide strip (1600×300) |
| Orders | Search and filter orders, change status (cancelling returns stock), mark payment as paid, print |
| Customers | Customer list, order history and total spent |
| Reviews | All product reviews with average rating; search, filter by stars, and delete spam or abusive reviews |

### Replacing the sample images

All seeded images are simple drawings marked "SAMPLE IMAGE".

1. **Products:** open a product → upload your photos (JPG/PNG/WEBP, up to 4 MB, square 800×800 works best) → click ☆ on the photo you want as the main image → delete the sample images.
2. **Banners, brand logos and category images:** edit the item in its admin section and upload a new image.

---

## IT services

Besides products, Odin.lk sells IT services. Customers browse them at **http://127.0.0.1:8000/services** (also "IT Services" in the blue menu bar, the home page and the footer).

| Group | Services |
|---|---|
| Website Development | Business, corporate, e-commerce, hotel/restaurant, portfolio and landing page websites, website redesign, website maintenance, custom software |
| ERP Software | Sales, inventory, purchasing, accounting, HR & payroll, customer and supplier management, reports & dashboards, multi-branch |
| Business Management Systems | Inventory, HR, payroll, CRM, accounting, attendance, booking/reservation systems |
| Software Installation & Maintenance | Software installation, user/account setup, software updates, backup & recovery |

- Each service has its own page with price ("from Rs. ..."), delivery time, what's included and a **free quote** form. Customers can also use **Request a Quote** (`/services/request`) without choosing a service.
- Every request gets a reference number (e.g. `SRV260928AB12`). Logged-in customers see their requests and status under **My Account → Service Requests**.
- **Admin → IT Services:** add, edit, hide or delete services and service groups, change prices, delivery times, icons and the "what's included" list (one item per line).
- **Admin → Service Requests:** new requests show a red badge (also on the dashboard). Open a request to call / WhatsApp / email the customer, change its status (New → Contacted → Quote sent → In progress → Completed / Closed) and keep internal notes.
- The quote form is protected against spam (rate limited and a hidden "honeypot" field). Each new request is also emailed to the store (see [Email](#email)).
- **Portfolio PDF:** "Download our Portfolio" buttons on the services pages, home page and footer download `/services/portfolio`. A 6-page **sample** portfolio is included (`public/downloads/odin-portfolio.pdf`, designed from `resources/portfolio/odin-portfolio.html`). To use your own, go to **Admin → IT Services → Portfolio PDF → Upload new PDF** (PDF, max 20 MB); the reset button goes back to the sample.
- `php artisan db:seed --class=ServiceSeeder` adds the sample services to an existing database (safe to run again).

---

## Visitor analytics

Admin → **Visitor Analytics** (http://127.0.0.1:8000/admin/analytics) shows who visits the store. The dashboard also shows online-now / today / week / month counts.

| Shows | Details |
|---|---|
| 👥 Total visitors | Unique browsers (anonymous `odin_vid` cookie), new visitors in the period, registered customers |
| 👀 Visits / sessions | A visit ends after 30 minutes without activity; also page views, pages per visit and bounce rate |
| 🟢 Online now | Visitors active in the last 5 minutes, with the page they are on (refreshes every 20 seconds) |
| 📅 Daily / weekly / monthly | Active visitors today, last 7 days, last 30 days, plus a daily chart |
| 🌍 Countries | From Cloudflare's `CF-IPCountry` header if the site is behind Cloudflare, otherwise looked up from the IP address when you open the page |
| 📱 Mobile vs desktop · 🌐 Browsers / OS | Parsed from the browser's user agent |
| 📄 Pages · 🔗 Referrers | Most viewed pages and where visitors came from |

- **Not counted:** bots and crawlers, admin pages, AJAX requests and your own browsing while logged in as admin. To test, open the store in a private window or on your phone.
- **On XAMPP** every visitor shows as 🏠 *Local network*; real countries appear once the site is online.
- **Country lookups** use ip-api.com, which is free for non-commercial use only. For the live shop put the site behind Cloudflare (free, and its country header is used automatically) or use a paid lookup service (`ANALYTICS_GEOIP_URL`), or set `ANALYTICS_GEOIP=false` to turn lookups off.
- Settings are in `config/analytics.php` (e.g. `ANALYTICS_EXCLUDE_ADMINS=false` to count admins too).
- Old data (older than about 13 months) is removed by `php artisan analytics:prune`, scheduled weekly when `php artisan schedule:work` is running.
- **Privacy:** the store stores visitor IP addresses and an anonymous cookie. Mention this in your privacy policy.

---

## Customer features

- **Abans-style storefront.** The layout follows buyabans.com but keeps Odin's own branding and colours.
  - **Header:** a wide search bar, then a blue menu bar with All Categories (mega menu), Brands, a TODAY'S OFFERS dropdown, IT Services, Easy Payment, Hot Deals, Track your order and the hotline.
  - **Home page, in order:**
    - Full-width banner slider
    - Flash Deals cards, each with its own countdown and a BUY NOW button
    - Round category shortcuts
    - New Arrivals and Top Selling (best sellers by units sold) sliding rows
    - Six coloured "Today's Offers" tiles
    - A sliding row for every category, with sub-category chips
    - Promo banners and the IT services band
    - An Easy Payment Plans strip
    - The "Odin Picks" spotlight
    - Brand logos
  - **Product cards:** % OFF badge, old/new price, "Monthly Rs. …" instalment price, Free Delivery / Limited Stock tags, and Add to Cart.
- **Today's Offers pages** (`/offers/new-arrivals`, `hot-deals`, `above-10-off`, `below-10000`, `below-50000`, `top-selling`): each is a filtered product list with its own coloured header. Titles, colours and price limits are in `config/shop.php` (`offers`).
- **Brands page** (`/brands`): every brand that has products, grouped A–Z.
- **Easy Payment Plans:** products priced at or above `installments.min_price` show monthly amounts for each bank, plus a "3 x Rs. … with KOKO" line. Banks, months and the minimum price are in `config/shop.php` (`installments`).
- **Information pages**, linked from the footer and each editable in `resources/views/pages/`:
  - About Us, Contact Us
  - FAQ, How to Buy, Shipping & Delivery, Warranty, Returns
  - Easy Payment Plans
  - Privacy Policy, Refund Policy, Terms & Conditions
- **Footer:**
  - Blue service bar
  - Address and hotline, with Track Order / Visit Our Store / Request IT Service / Download Portfolio buttons
  - About, Help and Policies link columns
  - Payment partners strip
- **WhatsApp button:** a floating chat button on every page (number from `SHOP_WHATSAPP`).
- **Mobile:** a bottom bar with Home / Categories / Offers / Cart / Account, and swipeable product rows.
- Live search suggestions, category and brand pages, filters (brand, price, in stock) and sorting (Latest, Best Selling, price, discount).
- **Product page:** Abans three-column layout (gallery, price and payment plans, delivery/warranty side panel), with Product details / Specification / Delivery & Warranty tabs and a "You May Also Like" row.
- Cart, wishlist, and checkout with Cash on Delivery or Bank Transfer
- Home page "Top Brands" section: real brand logos (from `public/images/brands`, originals in `public/images/Top Brands images`) in white tiles. Only brands with a real logo are shown; upload a logo in Admin → Brands and the brand appears automatically
- Footer "We Accept & Payment Partners" strip: a single scrolling line of bank and card logos (pauses on hover). Logos live in `public/images/payment-partners` and the list is in `config/shop.php` (`payment_partners`) — add or remove a line there to change it. Original uploads are kept in `public/images/Payment partners images`
- Register, login, My Account, order cancellation (while pending or confirmed)
- **Track Order** page (http://127.0.0.1:8000/track-order): paste an order number to see its status timeline, tracking history, items and total. No login needed; the customer's address, phone and email are never shown there. Limited to 20 lookups per minute per visitor.
- Logging out (store, My Account and admin panel) asks "Log out?" with **No** / **Yes, log out** buttons
- Raven loading screen: six small black birds (compact body, fan tail, wings raised in a V) fly from the bottom-right corner to the top-left on a white background, each streaming a glowing green smoke trail that unfurls with the bird and leaves with it. It plays when a visitor arrives on the site or refreshes a page (not on normal link clicks) and the ravens set off one after another; the page fades in while the last raven is flying its final stretch, and is fully visible as it leaves the screen (about 4.5 seconds). Pure CSS animation; edit it in `resources/views/partials/preloader.blade.php` and `preloader-head.blade.php`
- Bootstrap, jQuery, Bootstrap Icons and Chart.js are served from `public/vendor` (no CDN needed); only Google Fonts loads from the internet

### Product ratings & reviews

Every product page has a **Customer Ratings & Reviews** section, just above "You May Also Like":

- **Overall rating:** the average (for example 4.5 / 5) with stars and "Based on N reviews". A short version also appears under the product title and links down to the section.
- **Breakdown:** a bar for each star level (5 to 1) showing its count and percentage.
- **Write a Review:**
  - Rating: click a star from 1 to 5. Hovering previews a star count ("Poor" to "Excellent") and the chosen stars stay highlighted.
  - Comment: 10 to 2,000 characters, with a live character counter.
  - Submitting shows a loading state, then a success or error message. The rating and the list refresh without reloading the page.
- **Guests** see "Please log in to write a review." After logging in they come straight back to the product.
- **One review per customer per product.** A customer who has already reviewed a product sees "Edit your review" instead, and saving replaces the earlier review.
- **Reviews list:** newest first, 5 at a time, with a **Load more reviews** button. Each review shows the name, stars, comment and date. The customer's own review is highlighted, and changed reviews are marked "Edited".
- **Admin:** Admin → Reviews lists every review and lets you delete them.
- **Data:** reviews are stored in the `product_reviews` table (`product_id`, `user_id`, `rating`, `comment`, timestamps), with foreign keys to `products` and `users`. The average, count and breakdown are always calculated live from this table.

JSON API. It uses the website's normal login, so the POST needs a logged-in session and the page's CSRF token. The reviewer is always the logged-in user, and any `user_id` sent in the request is ignored.

| Method | URL | What it does |
|---|---|---|
| GET | `/api/products/{id}/reviews?page=1` | Reviews for a product, newest first (5 per page, with `meta.total` / `meta.last_page`) |
| GET | `/api/products/{id}/rating` | `average`, `count` and `distribution` (count and percent for each of 5 → 1 stars) |
| POST | `/api/products/{id}/reviews` | Body `rating` (1-5) and `comment`. Returns **201** for a new review, **200** when it updates your earlier one, **401** if not logged in, **422** for invalid data. Limited to 6 posts a minute. |

Sample reviews from the demo customers are added by `php artisan db:seed --class=ProductReviewSeeder`. It is part of the normal seed, and it skips products that already have reviews.

Not built yet: card payments (shown as "Coming soon"), password reset by email and coupons.

---

## Useful commands

| Command | What it does |
|---|---|
| `php artisan serve` | Start the website at http://127.0.0.1:8000 |
| `php artisan migrate:fresh --seed` | **Delete everything** and reload the sample data |
| `php artisan db:seed --class=MobilePhoneSeeder` | Add the sample Mobile Phones department to an existing database (keeps your products and orders; safe to run again) |
| `php artisan db:seed --class=BrandLogoSeeder` | Use the real brand logos from `public/images/brands` for any brand still showing a sample logo (never replaces a logo uploaded in the admin panel) |
| `php artisan storage:link` | Make uploaded images visible on the site |
| `php artisan test` | Run the automated tests (65 tests) |
| `php artisan analytics:prune` | Delete visitor analytics data older than the retention period |
| `php artisan route:list --except-vendor` | List all pages and URLs |

---

## Project structure

| Path | Contents |
|---|---|
| `routes/web.php` | All website and admin URLs |
| `app/Http/Controllers` | Storefront controllers (`Admin/` for the admin panel) |
| `app/Models` | Product, Category, Brand, Order, User, Banner… |
| `app/Services/Cart.php` | Shopping cart logic |
| `resources/views` | Storefront pages (`admin/` for the admin panel) |
| `public/css/app.css`, `public/css/admin.css` | Colours and styling |
| `public/js/app.js` | Add to cart, wishlist, live search |
| `config/shop.php` | Store name, contact details, delivery fee, bank details, districts |
| `database/migrations` | Table definitions |
| `database/seeders` | Sample data and sample-image generator |
| `database/odin.sql` | Full database export |
