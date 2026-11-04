<?php
namespace App\Http\View\Composers\CustomPage;

use App\Services\CustomPage\BlockService;
use Illuminate\View\View;

class BlockCABrandListComposer
{
    /**
     * The user repository implementation.
     *
     * @var BlockService
     */
    protected $service;

    /**
     * Create a new profile composer.
     *
     * @param  BlockService  $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->service = $blockService;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('CABrandList', $this->service->getCABrandList());
    }
}