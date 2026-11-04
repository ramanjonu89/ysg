<?php
namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\PolicyTranslation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PolicyController extends Controller
{
    public function index($type)
    {
        $policy = Policy::where('name', $type)->first();
        return view('policies.index', compact('policy'));
    }

    //updates the policy pages
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            $policy             = Policy::where('name', $request->name)->first();
            if(!$policy)        return abort(404);
            $policy->name       = $request->name;
            $policy->content    = @$request->content[0];
            if($policy->save()){
                $policyTranslationInputs = $this->policyTranslationFormatInputs($request, $policy->id);
                $this->updateOrCreateMultiple($policyTranslationInputs);
                DB::commit();
                flash(__('Policy has been updated successfully'))->success();
                return back();
            }else{
                flash(__('Something went wrong'))->error();
                return back();
            }
        }catch(Exception $e){
            DB::rollback();
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    private function updateOrCreateMultiple(array $data)
    {
        foreach ($data as $item) {
            $itemUpdate = PolicyTranslation::where(['policy_id' => $item['policy_id'], 'language_id' => $item['language_id']])->first();
            if(!isset($itemUpdate)){
                $itemUpdate = new PolicyTranslation;
            }
            $itemUpdate->policy_id    = $item['policy_id'];
            $itemUpdate->language_id  = $item['language_id'];
            $itemUpdate->content      = $item['content'];
            $itemUpdate->save();
        }
    }

    private function policyTranslationFormatInputs(Request $request, $policyId){
        $data = [];
        foreach($request->language_id as $key => $languageId){
            $item = [];
            $item['policy_id']          = $policyId;
            $item['language_id']        = $languageId;
            $item['content']            = $request->content[$key] != null ? $request->content[$key] : $request->content[0];
            $item['created_at']         = date('Y-m-d H:i:s');
            $item['updated_at']         = date('Y-m-d H:i:s');

            array_push($data,$item);
        }
        return $data;
    }
}

