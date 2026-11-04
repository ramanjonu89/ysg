<?php
namespace App\Http\Controllers;

use App\FlashDeal;
use App\FlashDealProduct;
use App\Services\FlashDealService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FlashDealController extends Controller
{
    protected $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FlashDealService $flashDealService)
    {
        $this->service = $flashDealService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $flash_deals = FlashDeal::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $flash_deals = $flash_deals->where('title', 'like', '%' . $sort_search . '%');
        }
        $flash_deals = $flash_deals->paginate(15);

        return view('flash_deals.index', compact('flash_deals', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('flash_deals.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            list($start, $end) = explode(' - ', $request->daterange);
            $startDate = Carbon::createFromFormat('d/m/Y H:i', $start)->timestamp;
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $end)->timestamp;
        } catch (\Exception $e) {
            flash(__('Incorrect Date Range'))->error();
            return back();
        }


        $flash_deal = new FlashDeal;
        $flash_deal->title = $request->title;
        $flash_deal->text_color = $request->text_color;
        $flash_deal->start_date = $startDate;
        $flash_deal->end_date = $endDate;
        $flash_deal->background_color = $request->background_color;
        $flash_deal->slug = strtolower(str_replace(' ', '-', $request->title) . '-' . Str::random(5));
        if ($request->hasFile('banner')) {
            $flash_deal->banner = $request->file('banner')->store('uploads/offers/banner');
        }
        if ($flash_deal->save()) {
            flash(__('Flash Deal has been inserted successfully'))->success();
            return redirect()->route('flash_deals.products', $flash_deal);
        } else {
            flash(__('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  FlashDeal  $id
     * @return View
     */
    public function edit(FlashDeal $flashDeal): View
    {
        $daterange = date('d/m/Y H:i', $flashDeal->start_date).' - '.date('d/m/Y H:i', $flashDeal->end_date);

        return view('flash_deals.edit', [
            'daterange' => $daterange,
            'flash_deal' => $flashDeal
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            list($start, $end) = explode(' - ', $request->daterange);
            $startDate = Carbon::createFromFormat('d/m/Y H:i', $start)->timestamp;
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $end)->timestamp;
        } catch (\Exception $e) {
            flash(__('Incorrect Date Range'))->error();
            return back();
        }

        $flash_deal = FlashDeal::findOrFail($id);
        $flash_deal->title = $request->title;
        $flash_deal->text_color = $request->text_color;
        $flash_deal->start_date = $startDate;
        $flash_deal->end_date = $endDate;
        $flash_deal->background_color = $request->background_color;
        if (($flash_deal->slug == null) || ($flash_deal->title != $request->title)) {
            $flash_deal->slug = strtolower(str_replace(' ', '-', $request->title) . '-' . Str::random(5));
        }
        if ($request->hasFile('banner')) {
            $flash_deal->banner = $request->file('banner')->store('uploads/offers/banner');
        }
        if ($flash_deal->save()) {
            flash(__('Flash Deal has been updated successfully'))->success();
            return redirect()->route('flash_deals.products', $flash_deal);
        } else {
            flash(__('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $flash_deal = FlashDeal::findOrFail($id);
        foreach ($flash_deal->flash_deal_products as $key => $flash_deal_product) {
            $flash_deal_product->delete();
        }
        if (FlashDeal::destroy($id)) {
            flash(__('FlashDeal has been deleted successfully'))->success();

            return redirect()->route('flash_deals.index');
        } else {
            flash(__('Something went wrong'))->error();

            return back();
        }
    }

    public function update_status(Request $request)
    {
        $flash_deal = FlashDeal::findOrFail($request->id);
        $flash_deal->status = $request->status;
        if ($flash_deal->save()) {
            flash(__('Flash deal status updated successfully'))->success();

            return 1;
        }

        return 0;
    }

    public function update_featured(Request $request)
    {
        foreach (FlashDeal::all() as $key => $flash_deal) {
            $flash_deal->featured = 0;
            $flash_deal->save();
        }
        $flash_deal = FlashDeal::findOrFail($request->id);
        $flash_deal->featured = $request->featured;
        if ($flash_deal->save()) {
            flash(__('Flash deal status updated successfully'))->success();

            return 1;
        }

        return 0;
    }

    public function product_discount(Request $request)
    {
        $product_ids = $request->product_ids;

        return view('partials.flash_deal_discount', compact('product_ids'));
    }

    public function product_discount_edit(Request $request)
    {
        $product_ids = $request->product_ids;
        $flash_deal_id = $request->flash_deal_id;

        return view('partials.flash_deal_discount_edit', compact('product_ids', 'flash_deal_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FlashDeal  $flashDeal
     * @return View
     */
    public function productList(\App\Models\FlashDeal $flashDeal): View
    {
        $flashDealProducts = $this->service->productList($flashDeal->id);
        return view('flash_deals.productList', compact('flashDeal', 'flashDealProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  Request  $request
     * @param  \App\Models\FlashDeal  $flashDeal
     * @return RedirectResponse
     */
    public function productListAddItems(Request $request, \App\Models\FlashDeal $flashDeal): RedirectResponse
    {
        if ($file = $request->file('exelFile')) {
            $this->service->importExcel($flashDeal, $file);
        } else if ($productIds = $request->get('productIds')){
            $ids = explode(',', $productIds);
            foreach ($ids as $id) {
                $this->service->addProductById($flashDeal, trim($id));
            }
        }

        return redirect()->route('flash_deals.products', $flashDeal);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  Request  $request
     * @param  \App\Models\FlashDeal  $flashDeal
     * @param  \App\Models\FlashDealProduct  $itemId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function productListUpdateItem(Request $request, \App\Models\FlashDeal $flashDeal, \App\Models\FlashDealProduct $itemId): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->get('data');
            $flashDealProduct = $this->service->updateProduct($itemId->id, $data);

            $htmlData = $flashDealProduct->toArray();
            unset($htmlData['product']);
            $returnHTML = view('flash_deals.productListItem', [
                'htmlData' => $htmlData,
                'flashDeal' => $flashDeal,
                'flashDealProduct' => $flashDealProduct,
                'product' => $flashDealProduct->product,
            ])->render();
        } catch (\Exception $e) {
            return response()->json( array('success' => false, 'error' => $e->getMessage()) );
        }
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }

    /**
     * Show the form for editing the specified resource.
     * @param  Request  $request
     * @param  \App\Models\FlashDeal  $flashDeal
     * @param  \App\Models\FlashDealProduct  $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function productListRemoveItem(Request $request, \App\Models\FlashDeal $flashDeal, \App\Models\FlashDealProduct $itemId): \Illuminate\Http\JsonResponse
    {
        $result = [
            'success' => false,
            'html' => '',
        ];
        $status = $this->service->removeProduct($itemId->id);
        if ($status) {
            $result['success'] = true;
        }
        return response()->json( $result );
    }
}

