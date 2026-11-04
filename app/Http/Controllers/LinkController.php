<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkTranslation;
use App\Services\LinkService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LinkController extends Controller
{
    protected $linkService;

    public function __construct(LinkService $linkService) {
        $this->linkService = $linkService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = $this->linkService->getAll();
        return view("links.index", compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countLinks = $this->linkService->getAll()->count();
        if ($countLinks <= 8) {
            return view('links.form');
        } else {
            flash(__('You can not add more than 8 links'))->error();
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try{
            $link       = new Link;
            $link->name = $request->name[0];
            $link->url  = $request->link;
            if($link->save()){
                $linkTranslationInputs = $this->linkTranslationFormatInputs($request, $link->id);
                $this->updateOrCreateMultiple($linkTranslationInputs);
                DB::commit();
                flash(__('Link has been inserted successfully'))->success();
                return redirect()->route('links.index');
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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $link = Link::findOrFail(decrypt($id));
        return view('links.form', compact('link'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try{
            $link       = Link::findOrFail($id);
            $link->name = $request->name[0];
            $link->url  = $request->link;
            if($link->save()){
                $linkTranslationInputs = $this->linkTranslationFormatInputs($request, $link->id);
                $this->updateOrCreateMultiple($linkTranslationInputs);
                DB::commit();
                flash(__('Link has been updated successfully'))->success();
                return redirect()->route('links.index');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $link = Link::findOrFail($id);
        if (Link::destroy($id)) {
            flash(__('Link has been deleted successfully'))->success();
            return redirect()->route('links.index');
        }
        flash(__('Something went wrong'))->error();

        return back();
    }

    private function updateOrCreateMultiple(array $data)
    {
        foreach ($data as $item) {
            $itemUpdate = LinkTranslation::where(['link_id' => $item['link_id'], 'language_id' => $item['language_id']])->first();
            if(!isset($itemUpdate)){
                $itemUpdate = new LinkTranslation;
            }
            $itemUpdate->link_id      = $item['link_id'];
            $itemUpdate->language_id  = $item['language_id'];
            $itemUpdate->name         = $item['name'];
            $itemUpdate->save();
        }
    }

    private function linkTranslationFormatInputs(Request $request, $linkId){
        $data = [];
        foreach($request->language_id as $key => $languageId){
            $item = [];
            $item['link_id']            = $linkId;
            $item['language_id']        = $languageId;
            $item['name']               = $request->name[$key] != null ? $request->name[$key] : $request->name[0];
            $item['created_at']         = date('Y-m-d H:i:s');
            $item['updated_at']         = date('Y-m-d H:i:s');

            array_push($data,$item);
        }
        return $data;
    }
}
