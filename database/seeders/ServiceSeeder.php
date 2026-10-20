<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * IT services offered by Odin (websites, ERP, business systems, installation & maintenance).
 *
 * Safe to run on a live database: it only adds categories and services that don't exist yet.
 */
class ServiceSeeder extends Seeder
{
    /**
     * [name, icon, tagline, description, services[]]
     * Each service: [name, icon, short description, features[], starting price|null, price note, delivery time, featured].
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string, 4: list<array{0: string, 1: string, 2: string, 3: list<string>, 4: int|null, 5: string, 6: string, 7: bool}>}>
     */
    private const CATALOG = [
        [
            'Website Development', 'bi-globe2', 'Fast, modern websites that bring you customers',
            'Professional websites designed and built in Sri Lanka — mobile friendly, fast, secure and easy for you to update.',
            [
                ['Business Websites', 'bi-building', 'A professional online presence for your shop, clinic, agency or small business.', ['Up to 8 pages, mobile friendly design', 'Contact form, Google Maps & WhatsApp button', 'Basic SEO so customers find you on Google', 'Free domain (.lk / .com) and hosting for 1 year', 'Easy admin to update text and photos'], 45000, 'one-time', '1 - 2 weeks', true],
                ['Corporate Websites', 'bi-buildings', 'Premium multi-page websites for companies and organisations.', ['Custom design matched to your brand', 'Unlimited pages with content management', 'News, careers and downloads sections', 'Multi-language support (English, Sinhala, Tamil)', 'Performance and security hardening'], 95000, 'one-time', '3 - 5 weeks', false],
                ['E-commerce Websites', 'bi-cart3', 'Sell online with a complete store like Odin.lk — products, cart, checkout and orders.', ['Product catalogue with categories, brands and filters', 'Cart, checkout and order tracking', 'Cash on delivery, bank transfer and card payment gateways', 'Admin panel for products, orders and customers', 'Sales reports and visitor analytics'], 150000, 'one-time', '4 - 8 weeks', true],
                ['Hotel & Restaurant Websites', 'bi-cup-hot', 'Show off rooms, menus and photos, and take bookings or orders online.', ['Room / menu pages with photo galleries', 'Online booking or table reservation form', 'Food ordering with WhatsApp or online payments', 'Reviews and TripAdvisor / Google integration', 'Promotions and seasonal offers'], 75000, 'one-time', '2 - 4 weeks', false],
                ['Portfolio Websites', 'bi-person-badge', 'A stylish personal site for photographers, designers, freelancers and professionals.', ['Beautiful gallery or project showcase', 'About, services and testimonials sections', 'Contact form and social media links', 'Blazing fast one or two page layout'], 30000, 'one-time', '1 week', false],
                ['Website Redesign', 'bi-brush', 'Give your old website a modern look, better speed and mobile support.', ['Fresh modern design, keeping your content', 'Mobile and tablet friendly layout', 'Speed and SEO improvements', 'Move to faster, secure hosting if needed'], 40000, 'one-time', '2 - 3 weeks', false],
                ['Website Maintenance', 'bi-wrench-adjustable', 'We keep your website updated, secure, backed up and running smoothly.', ['Monthly updates and security patches', 'Daily or weekly backups', 'Content updates (text, images, prices)', 'Uptime monitoring and quick fixes', 'Monthly performance report'], 5000, 'per month', 'Ongoing', false],
                ['Landing Page Websites', 'bi-megaphone', 'A single high-converting page for campaigns, product launches and ads.', ['Eye-catching one page design', 'Lead capture form connected to email / WhatsApp', 'Facebook & Google Ads tracking set up', 'Fast loading for better ad results'], 25000, 'one-time', '3 - 5 days', false],
                ['Custom Software Development', 'bi-code-slash', 'Software built exactly for your business — web apps, portals and automation.', ['Requirement study and free consultation', 'Web-based system accessible from anywhere', 'User roles, reports and data export', 'Integration with your existing tools', 'Training, documentation and support'], null, 'quotation', 'Depends on scope', true],
            ],
        ],
        [
            'ERP Software', 'bi-diagram-3', 'Run your whole business from one system',
            'A complete ERP for Sri Lankan businesses — sales, stock, purchasing, accounts and staff in one place. Choose the modules you need.',
            [
                ['Sales Management', 'bi-receipt', 'Quotations, invoices, POS billing and sales tracking.', ['Quotations, invoices and receipts', 'POS billing with barcode scanner', 'Customer credit limits and outstanding tracking', 'Sales by product, customer and salesperson'], 75000, 'one-time', '2 - 3 weeks', true],
                ['Inventory Management', 'bi-box-seam', 'Know exactly what stock you have, where it is and when to reorder.', ['Real-time stock levels across locations', 'Barcode and serial / IMEI number tracking', 'Low stock alerts and reorder levels', 'Stock transfers, adjustments and valuation'], 75000, 'one-time', '2 - 3 weeks', false],
                ['Purchasing', 'bi-bag-check', 'Purchase orders, goods received notes and supplier bills.', ['Purchase requests and approvals', 'Purchase orders and GRN', 'Supplier invoices and returns', 'Purchase history and price comparison'], 60000, 'one-time', '2 weeks', false],
                ['Accounting', 'bi-calculator', 'Double-entry accounting with Sri Lankan tax support.', ['Chart of accounts and journal entries', 'Receivables, payables and bank reconciliation', 'VAT / SSCL ready reports', 'Profit & loss, balance sheet and cash flow'], 90000, 'one-time', '3 - 4 weeks', false],
                ['HR & Payroll', 'bi-people', 'Employee records and monthly payroll with EPF / ETF.', ['Employee profiles and documents', 'Salary, allowances, overtime and deductions', 'EPF, ETF and APIT calculations', 'Payslips and bank transfer files'], 80000, 'one-time', '2 - 3 weeks', false],
                ['Customer Management', 'bi-person-lines-fill', 'A central customer database with history and follow-ups.', ['Customer profiles and purchase history', 'Loyalty points and discounts', 'Follow-up reminders and notes', 'SMS / email campaigns'], 50000, 'one-time', '1 - 2 weeks', false],
                ['Supplier Management', 'bi-truck', 'Track suppliers, prices, payments and performance.', ['Supplier profiles and contacts', 'Outstanding payments and due dates', 'Price lists and lead times', 'Supplier performance reports'], 45000, 'one-time', '1 - 2 weeks', false],
                ['Reports & Dashboards', 'bi-bar-chart-line', 'Live dashboards and reports so owners can decide fast.', ['Owner dashboard on mobile and desktop', 'Daily sales, profit and stock reports', 'Export to Excel and PDF', 'Custom reports for your business'], 55000, 'one-time', '1 - 2 weeks', false],
                ['Multi-branch Management', 'bi-diagram-2', 'Manage several shops or branches from one head office system.', ['Branch-wise stock, sales and staff', 'Inter-branch transfers', 'Consolidated head office reports', 'Branch user permissions'], 120000, 'one-time', '3 - 5 weeks', false],
            ],
        ],
        [
            'Business Management Systems', 'bi-briefcase', 'Ready-made systems for everyday operations',
            'Focused systems that solve one job really well — quick to set up and affordable for small and medium businesses.',
            [
                ['Inventory System', 'bi-boxes', 'A simple stock system for shops, warehouses and stores.', ['Items, categories and barcodes', 'Stock in / stock out with history', 'Low stock alerts', 'Stock valuation reports'], 50000, 'one-time', '1 - 2 weeks', false],
                ['HR Management System', 'bi-person-vcard', 'Keep all employee information, leave and documents organised.', ['Employee database and documents', 'Leave requests and approvals', 'Performance and training records', 'Employee self-service portal'], 60000, 'one-time', '2 weeks', false],
                ['Payroll System', 'bi-cash-stack', 'Accurate monthly salaries with EPF, ETF and APIT.', ['Salary structures and allowances', 'Overtime, no-pay and loan deductions', 'EPF / ETF / APIT reports', 'Payslips by email'], 55000, 'one-time', '1 - 2 weeks', false],
                ['CRM System', 'bi-person-heart', 'Manage leads, follow-ups and sales pipelines.', ['Lead capture from website and Facebook', 'Sales pipeline and follow-up reminders', 'Customer notes and call history', 'Sales team performance reports'], 65000, 'one-time', '2 weeks', true],
                ['Accounting System', 'bi-journal-text', 'Easy bookkeeping for small businesses — income, expenses and profit.', ['Income and expense tracking', 'Invoices and receipts', 'Bank and cash book', 'Monthly profit & loss'], 45000, 'one-time', '1 - 2 weeks', false],
                ['Attendance System', 'bi-fingerprint', 'Fingerprint / face attendance with automatic reports.', ['Fingerprint or face device integration', 'Shift and roster management', 'Late, early leave and overtime reports', 'Links with payroll'], 40000, 'one-time', '1 week', false],
                ['Booking & Reservation System', 'bi-calendar-check', 'Online bookings for hotels, salons, clinics, rentals and events.', ['Online booking calendar', 'SMS / email confirmations and reminders', 'Advance payments', 'Staff and resource scheduling'], 60000, 'one-time', '2 weeks', false],
            ],
        ],
        [
            'Software Installation & Maintenance', 'bi-tools', 'Friendly technical support for your office',
            'On-site and remote support by experienced technicians — we install, set up, update and back up your software so your business keeps running.',
            [
                ['Software Installation', 'bi-download', 'Genuine Windows, Microsoft Office, antivirus and business software installed.', ['Windows and Microsoft Office installation', 'Antivirus and security software', 'Drivers, printers and scanners', 'On-site or remote installation'], 2500, 'per device', 'Same day', true],
                ['User & Account Setup', 'bi-person-gear', 'Email, Microsoft 365 / Google Workspace and user accounts set up correctly.', ['Business email on your own domain', 'Microsoft 365 / Google Workspace setup', 'User accounts, permissions and passwords', 'Shared drives and printers'], 3500, 'per user', '1 - 2 days', false],
                ['Software Updates', 'bi-arrow-repeat', 'Keep systems patched, secure and running at full speed.', ['Windows and application updates', 'Security patches and antivirus updates', 'Performance clean-up', 'Monthly maintenance plans available'], 2000, 'per device', 'Same day', false],
                ['Backup & Recovery', 'bi-cloud-arrow-up', 'Automatic backups so you never lose your business data.', ['Automatic daily cloud or local backups', 'Database and file backups', 'Backup monitoring and reports', 'Data recovery when you need it'], 4500, 'per month', '1 day setup', false],
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::CATALOG as $categoryIndex => [$name, $icon, $tagline, $description, $services]) {
            $category = ServiceCategory::firstOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'icon' => $icon,
                'tagline' => $tagline,
                'description' => $description,
                'sort_order' => $categoryIndex + 1,
            ]);

            foreach ($services as $serviceIndex => [$serviceName, $serviceIcon, $short, $features, $price, $priceNote, $delivery, $featured]) {
                $category->services()->firstOrCreate(['slug' => Str::slug($serviceName)], [
                    'name' => $serviceName,
                    'icon' => $serviceIcon,
                    'short_description' => $short,
                    'description' => "{$short}\n\nOdin's team in Colombo handles everything from planning to launch and after-sales support. We work with businesses all over Sri Lanka, explain everything in simple language, and stay with you after the project is finished.\n\nContact us for a free consultation and a clear, no-obligation quotation.",
                    'features' => $features,
                    'starting_price' => $price,
                    'price_note' => $priceNote,
                    'delivery_time' => $delivery,
                    'is_featured' => $featured,
                    'sort_order' => $serviceIndex + 1,
                ]);
            }
        }
    }
}
