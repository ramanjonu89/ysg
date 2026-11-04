<?php
namespace App\Services;

use App\Repositories\BannerRepository;
use Illuminate\Support\Facades\Auth;

class BannerService extends AbstractService
{
    use SortableTrait;

    protected $user;
    protected $repo;

    public function __construct(BannerRepository $repo)
    {
        $this->repo = $repo;
    }

    public function setUser($user = null) {
        if (is_null($user)) {
            $this->user = Auth::user();
        } else {
            $this->user = $user;
        }
    }

    /**
     * get list of banner for display at front end
     */
    public function getPublishedList($params = [])
    {
        $position = $params['position']??null;
        return $this->repo->getList([
            'published' => 1,
            'position' => $position,
        ]);
    }

    /**
     * get list of banner for display at admin, included not published
     */
    public function getList($params = [])
    {
        $published = $params['published']??null;
        $position = $params['position']??null;
        return  $this->repo->getList([
            'published' => $published,
            'position' => $position,
        ]);
    }

    public function togglePublish($id): bool
    {
        return  $this->repo->togglePublish($id);
    }

}