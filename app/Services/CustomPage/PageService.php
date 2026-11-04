<?php

namespace App\Services\CustomPage;

use App\Models\CustomPage\Page;
use App\Repositories\CustomPage\PageRepository;
use App\Services\CorporateAccessService;
use App\Services\PageInterface;
use App\User;
use Illuminate\Http\Request;

/**
 * Class PageService
 * @package App\Services\CustomPage
 */
//  check herre
class PageService implements PageInterface
{
    protected $repo;
    protected $CAService;

    public function __construct(PageRepository $repo, CorporateAccessService $corporateAccessService)
    {
        $this->repo = $repo;
        $this->CAService = $corporateAccessService;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->repo->find($id);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function getBySlug($slug)
    {
        $params = [
            'is_enabled' => true,
            'slug' => $slug,
        ];
        return $this->repo->getOne($params);
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->repo->getList([]);
    }

    /**
     * @return mixed
     */
    public function getListBlockNotChild()
    {
        return $this->repo->getList([]);
    }

    /**
     * @param $currentId
     * @return mixed
     */
    public function getIdsNotCurrent($currentId)
    {
        $params = [
            'notIn' => [$currentId],
        ];
        return $this->repo->getList($params);
    }

    public function getListType(): array
    {
        return [
            Page::TYPE_PAGE => 'Custom Page',
        ];
    }

    /**
     * @param string $type
     * @return string
     */
    private function getDefPageTitle(string $type): string
    {
        return $this->getListType()[$type];
    }

    private function removeSpecialCharacters($string)
    {
        return preg_replace('/[^a-zA-Z0-9\_\- ]/s', '', $string);
    }

    private function formatSlug($title): string
    {
        $temp_str = $this->removeSpecialCharacters($title);
        $temp_str = str_replace([' ', '_'], '-', $temp_str);
        return strtolower($temp_str);
    }

    private function getDefPageSlug($title): string
    {
        $temp_slug = $temp_str = $this->formatSlug($title);
        $not_valid = true;
        $try_times = 0;
        while ($not_valid) {
            if ($try_times) {
                $temp_str = $temp_slug . '-' . $try_times;
            }
            if (null === $this->getBySlug($temp_str)) {
                $not_valid = false;
                $temp_slug = $temp_str;
            } else {
                $try_times++;
            }
        }
        return $temp_slug;
    }

    public function create($params)
    {
        $title = $params['title'][0] ?? '';
        if ($title == '') {
            $title = $this->getDefPageTitle($params['type']);
        }
        $page_slug = $this->getDefPageSlug($title);

        $data = [
            'title' => $title,
            'slug'  => $page_slug,
            'type'  => $params['type'],
            'is_enabled' => true
        ];
        return $this->repo->create($data);
    }

    public function update($id, $data)
    {
        return $this->repo->update($id, $data);
    }

    public function destroy($id)
    {
        return $this->repo->destroy($id);
    }

    public function updateBlockIndex($id, $data): bool
    {
        $page = $this->get($id);
        foreach ($page->blocks as $item) {
            if (isset($data[$item->id])) {
                $item->sort = $data[$item->id];
                $item->save();
            }
        }
        return true;
    }

    /**
     * @param Page $page
     * @param User|null $user
     * @return array
     */
    private function checkLogin(Page $page, ?User $user): array
    {
        $result = [
            'valid' => true,
        ];
        $need_login = $page->need_login;
        if ($need_login) {
            if (is_null($user)) {
                $result = [
                    'valid' => false,
                    'message' => __('Please login first!'),
                    'redirect_url' => route('user.login'),
                ];
            }
        }
        return $result;
    }

    /**
     * @param Page $page
     * @param User|null $user
     * @return array
     */
    private function checkExclusiveAccess(Page $page, ?User $user): array
    {
        $result = [
            'valid' => true,
        ];
        if ($page->need_login && $page->exclusive_access) {
            if (!$this->CAService->canViewCSSPage($user)) {
                $result = [
                    'valid' => false,
                    'message' => __('Please login using a valid account to access the Private/Corporate Sales Store!'),
                    'redirect_url' => route('home'),
                ];
            }
        }
        return $result;
    }

    /**
     * @param Page $page
     * @param Request $request
     * @return array
     */
    public function checkAccessPermission(Page $page, Request $request): array
    {
        $user = $request->user();
        $result = $this->checkLogin($page, $user);
        if ($result['valid'] == true) {
            $result = $this->checkExclusiveAccess($page, $user);
        }
        return $result;
    }

    public function formatDataInputs($request){
        $inputs['is_enabled']       = isset($request->active) && $request->active == 'on' ? true : false;
        $inputs['need_login']       = isset($request->need_login) && $request->need_login == 'on' ? true: false;
        $inputs['exclusive_access'] = isset($request->exclusive_access) && $request->exclusive_access == 'on' ? true: false;
        $inputs['options']          = isset($request->options) ? json_decode($request->options) : null;
        $inputs['slug']             = $request->slug;
        $inputs['parent_id']        = $request->parent_id;

        $inputs['title']            = isset($request->title[0]) ? $request->title[0] : '';
        $inputs['description']      = isset($request->description[0]) ?  $request->description[0] : '';
        $inputs['content']          = isset($request->content[0]) ? $request->content[0] : '';
        $inputs['meta_title']       = isset($request->meta_title[0]) ? $request->meta_title[0] : '';
        $inputs['meta_description'] = isset($request->meta_description[0]) ? $request->meta_description[0] : '';
        $inputs['meta_keywords']    = isset($request->meta_keywords[0]) ? $request->meta_keywords[0] : '';

        return $inputs;
    }
}