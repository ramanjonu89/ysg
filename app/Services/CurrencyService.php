<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 02/09/2022
 * Time: 14:15
 */

namespace App\Services;

use App\Models\Currency;
use App\Repositories\CurrencyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Class CurrencyService
 * @package App\Services
 * @property
 */
class CurrencyService
{
    protected $model;
    protected $repo;

    protected $code;
    protected $symbol;

    public function __construct(CurrencyRepository $repo)
    {
        $this->repo = $repo;
        $this->model = $this->getCurrent();
        $this->code = $this->model->code;
        $this->symbol = $this->model->symbol;
    }

    public function changeCurrency($code): ?\Illuminate\Database\Eloquent\Model
    {
        try {
            $model = $this->repo->findBy('code', $code);
            config()->set('currency_code', $model->code);
            config()->set('currency_symbol', $model->symbol);
            return $model;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return $this->model;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function exchange($price) {
        return floatval($price) * floatval($this->model->exchange_rate);
    }

    public function getDefault(): \Illuminate\Database\Eloquent\Model
    {
        $id = config('business_setting.system_default_currency');
        if ($id) {
            if ($model = $this->repo->find($id)) {
                return $model;
            }
        }
        return $this->repo->findBy('code', Currency::DEFAULT_CODE);
    }

    public function getCurrent(): \Illuminate\Database\Eloquent\Model
    {
        if ($code = Session::get('currency_code')) {
            return $this->repo->findBy('code', $code);
        }
        return $this->getDefault();
    }
}