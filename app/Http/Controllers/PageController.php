<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Information pages linked from the footer. Edit the text in resources/views/pages/{slug}.blade.php.
 */
class PageController extends Controller
{
    /**
     * URL slug => [title, icon, footer group].
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    public const PAGES = [
        'about-us' => ['About Us', 'bi-building', 'about'],
        'contact-us' => ['Contact Us', 'bi-telephone', 'about'],
        'faq' => ['Frequently Asked Questions', 'bi-question-circle', 'help'],
        'how-to-buy' => ['How To Buy', 'bi-cart-check', 'help'],
        'shipping-delivery' => ['Shipping & Delivery', 'bi-truck', 'help'],
        'warranty' => ['Warranty Information', 'bi-shield-check', 'help'],
        'returns' => ['Return Products', 'bi-arrow-return-left', 'help'],
        'easy-payment-plans' => ['Easy Payment Plans', 'bi-credit-card', 'help'],
        'privacy-policy' => ['Privacy Policy', 'bi-lock', 'policies'],
        'refund-policy' => ['Return & Refund Policy', 'bi-cash-coin', 'policies'],
        'terms-and-conditions' => ['Terms & Conditions', 'bi-file-text', 'policies'],
    ];

    public function __invoke(string $page): View
    {
        [$title, $icon] = self::PAGES[$page];

        return view("pages.{$page}", [
            'page' => $page,
            'title' => $title,
            'icon' => $icon,
            'pages' => self::PAGES,
        ]);
    }
}
