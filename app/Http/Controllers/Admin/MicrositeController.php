<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests\Microsite\CreateRequest;
use App\Http\Requests\Microsite\UpdateRequest;
use App\Models\Microsite;
use App\Services\CustomPage\BlockService;
use App\Services\MicrositeService;
use App\Services\SellerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MicrositeController extends Controller
{
    /**
     * @param MicrositeService
     */
    protected $service;

    public function __construct(MicrositeService $service) {
        $this->service = $service;
    }

    public function index(Request $request, SellerService $sellerService)
    {
        $sellers = $sellerService->getAll();
        $entities = $this->service->getList();
        return view('admin.microsites.index', compact(['entities', 'sellers']));
    }

    public function edit(Microsite $microsite, BlockService $blockService)
    {
        $blocks = $microsite->allBlocks ?? [];
        $allBlocks = $blockService->getBlockListForMicrosite();
        $blockTypes = $blockService->getMicrositeListType();
        return view('admin.microsites.edit', compact(['microsite', 'blocks', 'allBlocks', 'blockTypes']));
    }

    public function store(CreateRequest $request, MicrositeService $micrositeService)
    {
        $microsite = $micrositeService->create(['seller_id' => $request->input('seller_id')]);

        return redirect(route('admin.microsites.edit', [$microsite->id]));

    }

    public function update(UpdateRequest $request, Microsite $microsite)
    {
        $this->service->updateByRequest($microsite->id, $request);

        if ($request->input('redirect')) {
            if ($request->input('redirect') == 'list') {
                return redirect()->route('admin.microsites.index');
            }
        }

        return back();
    }

    public function destroy($id): RedirectResponse
    {
        $this->service->destroy($id);
        flash(__('Microsite delete successful'))->success();
        return redirect()->route('admin.microsites.index');
    }
}
