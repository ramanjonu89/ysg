<?php
namespace App\Services;

use App\Repositories\SliderRepository;

class SliderService extends AbstractService
{
    use SortableTrait;
    use PublishableTrait;

    protected $user;
    protected $repo;

    public function __construct(SliderRepository $repo)
    {
        $this->repo = $repo;
    }
    /**
     * get list of banner for display at front end
     */
    public function getPublishedList($params = [])
    {
        $params['published'] = 1;
        return $this->repo->getList($params);
    }

    /**
     * get list of banner for display at admin, included not published
     */
    public function getList($params = [])
    {
        return  $this->repo->getList($params);
    }
}