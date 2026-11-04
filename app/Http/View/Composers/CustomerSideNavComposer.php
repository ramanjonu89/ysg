<?php
namespace App\Http\View\Composers;

use App\Services\CorporateAccessService;
use App\Services\CustomPage\BlockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerSideNavComposer
{
    /**
     * The user repository implementation.
     *
     * @var BlockService
     */
    protected $CAService;

    /**
     * Create a new profile composer.
     *
     * @param  CorporateAccessService  $corporateAccessService
     */
    public function __construct(CorporateAccessService $corporateAccessService)
    {
        // Dependencies automatically resolved by service container...
        $this->CAService = $corporateAccessService;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        $showCATab = $this->CAService->getSharedShops($user)->count();
        $view->with('showCATab', $showCATab);
    }
}