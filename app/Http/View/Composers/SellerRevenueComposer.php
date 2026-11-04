<?php
namespace App\Http\View\Composers;

use App\Services\SellerService;
use Auth;
use Illuminate\View\View;

class SellerRevenueComposer
{
    protected $sellerService;

    public function __construct(SellerService $sellerService)
    {
        $this->sellerService = $sellerService;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check() && Auth::user()->isSeller()) {
            $seller = Auth::user();

            $revenue = $this->sellerService->getTotalRevenue($seller->id);
            $revenueThisMonth = $this->sellerService->getRevenueThisMonth($seller->id);
            $revenueLastMonth = $this->sellerService->getRevenueLastMonth($seller->id);

            $view->with('vc_revenue', $revenue);
            $view->with('vc_revenueThisMonth', $revenueThisMonth);
            $view->with('vc_revenueLastMonth', $revenueLastMonth);
            
        }

    }

}