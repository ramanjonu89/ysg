<?php
namespace App\Services;

use App\Repositories\PaymentSettingRepository;
use Illuminate\Support\Arr;

class PaymentSettingService
{
    public $setting;
    protected $repo;

    public function __construct(PaymentSettingRepository $repo)
    {
        $this->repo = $repo;
        $this->setting = $this->all();
    }

    public function toggleActive($methodName): bool
    {
        $status = (bool)$this->get($methodName.'.enable');
        return $this->set($methodName.'.enable', !$status);
    }

    public function all(): array
    {
        $result = [];
        $all = $this->repo->getAll();
        foreach ($all as $item) {
            Arr::set($result, $item->key, $item->value);
        }
        return $result;
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->setting, $key) ?? $default;
    }

    public function set($key, $value): bool
    {
        Arr::set($this->setting, $key, $value);

        if (is_array($value)) {
            $value = json_encode($value);
        }
        $model = $this->repo->getByKey($key);
        if ($model) {
            $this->repo->update($model->id, ['value' => $value]);
        } else {
            $this->repo->create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        return true;
    }

    public function remove($key): bool
    {
        $model = $this->repo->getByKey($key);
        if ($model) {
            $this->repo->destroy($model->id);
            Arr::forget($this->setting, $key);
            return true;
        }
        return false;
    }
}