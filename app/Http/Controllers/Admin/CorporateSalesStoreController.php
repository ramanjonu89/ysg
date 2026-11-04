<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\BulkUpdateCSSAccessRequest;
use App\Repositories\CustomPage\SellerCorporateDomainRepository;
use App\Services\BannerService;
use App\Services\EmailCorporateAccessService;
use App\Services\SellerCorporateDomainService;
use App\Services\SellerService;
use Illuminate\Http\Request;

class CorporateSalesStoreController extends Controller
{
    protected $homeBannerService;
    /**
     * @var SellerService
     */
    protected $sellerService;

    /**
     * @var SellerCorporateDomainService
     */
    protected $sellerCorporateDomainService;

    /**
     * @var EmailCorporateAccessService
     */
    protected $emailCorporateAccessService;

    const TYPE_CREATE = 'create';
    const TYPE_DELETE = 'delete';
    const SELECT_ALL_VALUE = 0;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BannerService $homeBannerService,
        SellerService $sellerService,
        SellerCorporateDomainService $sellerCorporateDomainService,
        EmailCorporateAccessService $emailCorporateAccessService
    )
    {
        //$this->middleware('auth');
        $this->homeBannerService = $homeBannerService;
        $this->sellerService = $sellerService;
        $this->sellerCorporateDomainService = $sellerCorporateDomainService;
        $this->emailCorporateAccessService = $emailCorporateAccessService;
    }

    public function index(Request $request)
    {
        $homeBanner1List = $this->homeBannerService->getList([
            'position' => 1,
        ]);

        $homeBanner2List = $this->homeBannerService->getList([
            'position' => 2
        ]);

        return view('frontend_settings.home.index', [
            'homeBanner1List' => $homeBanner1List,
            'homeBanner2List' => $homeBanner2List,
        ]);
    }

    public function css_access(Request $request)
    {
        $sellers = $this->sellerService->getCAList();

        return view('admin.css.access', compact(['sellers']));
    }

    public function bulk_update(BulkUpdateCSSAccessRequest $request) {
        $sellerIds = $request->get('seller_ids', []);

        if (($key = array_search(self::SELECT_ALL_VALUE, $sellerIds)) !== false) {
            unset($sellerIds[$key]);
        }
        
        $type = $request->get('action_type', self::TYPE_CREATE);

        $emails = $request->get('emails_array', []);
        $domains = $request->get('domains_array', []);

        $this->sellerCorporateDomainService->removeDomainAccess($sellerIds, $domains);
        $this->emailCorporateAccessService->removeEmailAccess($sellerIds, $emails);

        if (self::TYPE_CREATE === $type) {
            $this->sellerCorporateDomainService->addDomainAccess($sellerIds, $domains);
            $this->emailCorporateAccessService->addEmailAccess($sellerIds, $emails);
        }

        flash(__('CSS access update successfully'))->success();

        return back();
    }
}
