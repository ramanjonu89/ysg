<?php
namespace App\Services\CustomPage;

use App\Models\CustomPage\BlockContent;
use App\Repositories\CustomPage\BlockContentRepository;
use Carbon\Carbon;

class BlockContentService
{
    protected $repo;

    public function __construct(BlockContentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getById($id): \Illuminate\Database\Eloquent\Model
    {
        return $this->repo->find($id);
    }

    public function getTypeById($id) {
        $model = $this->repo->find($id);
        if ($model) {
            return $model->block->type;
        }
        return null;
    }

    public function create($data) {
        return $this->repo->create($data);
    }

    public function update($id, $data) {
        return $this->repo->update($id, $data);
    }

    public function destroy($id) {
        return $this->repo->destroy($id);
    }

    public function formatDataInputs(object $request) {
        if (isset($request->image)) {
            unset($request->image);
        }

        if (isset($request->date_timer_type)) {
            switch ($request->date_timer_type) {
                case BlockContent::START_DATE_ONLY:
                    $start = $request->start_date;
                    break;
                case BlockContent::END_DATE_ONLY:
                    $end = $request->end_date;
                    break;
                default:
                    if (isset($request->daterange)) {
                        list($start, $end) = explode(' - ', $request->daterange);
                    }
                    break;
            }
        }

        $inputs['start_date']       = isset($start) ? Carbon::createFromFormat('d/m/Y H:i:s', $start)->timestamp : 0;
        $inputs['end_date']         = isset($end)   ? Carbon::createFromFormat('d/m/Y H:i:s', $end)->timestamp : 0;

        $inputs['is_enabled']       = isset($request->active) && $request->active == 'on' ? true : false;

        $inputs['block_id']         = isset($request->block_id) ? $request->block_id : null;
        $inputs['title']            = isset($request->title[0]) ? $request->title[0] : '';
        $inputs['description']      = isset($request->description[0]) ? $request->description[0] : '' ;
        $inputs['content']          = isset($request->content[0]) ? $request->content[0] : '';
        $inputs['options']          = isset($request->options) ? json_encode($request->options) : '';

        return $inputs;
    }
}