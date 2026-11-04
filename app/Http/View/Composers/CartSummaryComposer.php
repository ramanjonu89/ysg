<?php
namespace App\Http\View\Composers;

use App\Services\CartService;
use Illuminate\View\View;

class CartSummaryComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $cartSummaryText = paymentSetting('octifi.cartSummaryText');
        if ($cartSummaryText) {
            $cartSummaryTexts = explode('{{octifi_logo}}', $cartSummaryText);
        } else $cartSummaryTexts = null;

        $cart = app()->make(CartService::class)->getCart();

        $view->with('cartSummaryTexts', $cartSummaryTexts);
        $view->with('cart', $cart);

    }

}