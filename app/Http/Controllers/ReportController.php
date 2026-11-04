<?php
namespace App\Http\Controllers;

use App\Product;
use App\ReorganiseProductsExport;
use App\ReorganiseProductsImport;
use App\Seller;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\ReportService;
use App\Services\SellerService;
use App\Services\ShopService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Auth;

class ReportController extends Controller
{
    public function stock_report(Request $request)
    {
        $params = [
            'category_id' => $request->get('category_id', ''),
            'user_id' => $request->get('shop_name', '')
        ];

        $productService = app()->make(ProductService::class);
        $products = $productService->getStockReport($params);

        $shopService = app()->make(ShopService::class);
        $shops = $shopService->getSelectOptionsArray();

        $categoryService = app()->make(CategoryService::class);
        $categories = $categoryService->getSelectOptionsArray();

        return view('reports.stock_report', compact(['products', 'shops', 'categories']));
    }

    public function in_house_sale_report(Request $request)
    {
        if ($request->has('category_id')) {
            $products = Product::where('category_id', $request->category_id)->orderBy('num_of_sale', 'desc')->get();
        } else {
            $products = Product::orderBy('num_of_sale', 'desc')->get();
        }

        return view('reports.in_house_sale_report', compact('products'));
    }

    public function seller_report(Request $request)
    {
        if ($request->has('verification_status')) {
            $sellers = Seller::where('verification_status', $request->verification_status)->get();
        } else {
            $sellers = Seller::all();
        }

        return view('reports.seller_report', compact('sellers'));
    }

    public function seller_sale_report(Request $request)
    {
        if ($request->has('verification_status')) {
            $sellers = Seller::where('verification_status', $request->verification_status)->get();
        } else {
            $sellers = Seller::all();
        }

        return view('reports.seller_sale_report', compact('sellers'));
    }

    public function shipping_report(Request $request)
    {
        if ($request->has('verification_status')) {
            $sellers = Seller::where('verification_status', $request->verification_status)->get();
        } else {
            $sellers = Seller::all();
        }

        return view('reports.shipping_report', compact('sellers'));
    }

    public function heinemann_sales_report(Request $request)
    {
        if ($request->has('verification_status')) {
            $sellers = Seller::where('verification_status', $request->verification_status)->get();
        } else {
            $sellers = Seller::where('id', '203')->get();
        }

        return view('reports.heinemann_sales_report', compact('sellers'));
    }

    public function coupon_report(Request $request)
    {
        $shopService = app()->make(ShopService::class);
        $sellers     = $shopService->getSelectOptionsArray();
        
        $reportService = app()->make(ReportService::class);
        $couponusages = $reportService->getListCouponsReport($request);
 
        return view('reports.coupon_report', compact('sellers', 'couponusages'));
    }

    public function wish_report(Request $request)
    {
        if ($request->has('category_id')) {
            $products = Product::where('category_id', $request->category_id)->get();
        } else {
            $products = Product::all();
        }

        return view('reports.wish_report', compact('products'));
    }

    public function reorganise_products()
    {
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('reports.reorganise_products');
        }
    }

    public function reorganise_products_report()
    {
        return Excel::download(new ReorganiseProductsExport, 'reorganise_products_report.xlsx');
    }

    public function reorganise_products_upload(Request $request)
    {
        if ($request->hasFile('reorganise_products')) {
            $file   = request()->file('reorganise_products');
            $import = new ReorganiseProductsImport();
            $import->import($file);

            $data = [];

            foreach($import->failures() as $item){
                array_push($data,  $item->row());
            }
            
            $failed = count(array_unique($data));
            $total = $import->getRowCount();


            if ($import->failures()->isNotEmpty()) {
                return back()->withFailures($import->failures())->withFailed($failed)->withTotal($total);
            }
        }
        flash(__('Products imported successfully'))->success();
        return back()->withFailed($failed)->withTotal($total);
    }
}

