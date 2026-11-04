<?php
namespace App\Http\Controllers;

use App\Models\ReviewCategory;
use Illuminate\Http\Request;

class ReviewCategoryController extends Controller
{
    public function index(Request $request)
    {
        $sort_search = null;
        $review_categories = ReviewCategory::orderBy('created_at', 'desc');
        
        if ($request->has('search')) {
            $sort_search = $request->search;
            $review_categories = $review_categories->where('title', 'like', '%' . $sort_search . '%');
        }
        
        $review_categories = $review_categories->paginate(15);

        return view('review_categories.index', compact('review_categories', 'sort_search'));
    }

    public function create()
    {
        return view('review_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $review_category = new ReviewCategory;
        $review_category->title = $request->title;
        $review_category->enabled = $request->has('enabled') ? 1 : 0;

        if ($review_category->save()) {
            flash(__('Review Category has been inserted successfully'))->success();
            return redirect()->route('review_categories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function edit($id)
    {
        $review_category = ReviewCategory::findOrFail(decrypt($id));
        return view('review_categories.edit', compact('review_category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $review_category = ReviewCategory::findOrFail($id);
        $review_category->title = $request->title;
        $review_category->enabled = $request->has('enabled') ? 1 : 0;

        if ($review_category->save()) {
            flash(__('Review Category has been updated successfully'))->success();
            return redirect()->route('review_categories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function destroy($id)
    {
        $review_category = ReviewCategory::findOrFail($id);
        
        if (ReviewCategory::destroy($id)) {
            flash(__('Review Category has been deleted successfully'))->success();
            return redirect()->route('review_categories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function updateStatus(Request $request)
    {
        $review_category = ReviewCategory::findOrFail($request->id);
        $review_category->enabled = $request->status;
        
        if ($review_category->save()) {
            $response = [
                'status'  => 1,
                'message' => 'Review Category status updated successfully!'
            ];
        } else {
            $response = [
                'status'  => 0,
                'message' => 'Something went wrong!'
            ];
        }
        
        return json_encode($response);
    }
}

