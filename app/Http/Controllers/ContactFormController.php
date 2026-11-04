<?php

namespace App\Http\Controllers;

use App\Services\ContactFormService;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    protected $contactFormService;

    public function __construct(ContactFormService $contactFormService)
    {
        $this->contactFormService = $contactFormService;
    }

    public function index(Request $request)
    {
        $sort_search = null;
        $sort_field = $request->get('sort_field');
        $sort_direction = $request->get('sort_direction', 'desc');
        
        $params = [
            'orderBy' => $sort_field && in_array($sort_field, ['name', 'email', 'created_at', 'is_read']) 
                ? $sort_field 
                : 'created_at',
            'orderDirection' => $sort_direction,
            'perPage' => 20
        ];

        if ($request->has('search')) {
            $sort_search = $request->search;
            $params['search'] = $sort_search;
        }

        $contactForms = $this->contactFormService->getList($params);

        return view('contact_forms.index', compact('contactForms', 'sort_search', 'sort_field', 'sort_direction'));
    }

    public function show($id)
    {
        $contactForm = $this->contactFormService->get($id);
        
        if (!$contactForm->is_read) {
            $this->contactFormService->markAsRead($id);
            $contactForm->refresh();
        }

        return view('contact_forms.show', compact('contactForm'));
    }

    public function destroy($id)
    {
        $this->contactFormService->destroy($id);

        flash('Contact form deleted successfully')->success();
        return redirect()->route('contact_forms.index');
    }

    public function markAsRead($id)
    {
        $this->contactFormService->markAsRead($id);

        return response()->json([
            'success' => true,
            'message' => 'Contact form marked as read'
        ]);
    }

    public function markAsUnread($id)
    {
        $this->contactFormService->markAsUnread($id);

        return response()->json([
            'success' => true,
            'message' => 'Contact form marked as unread'
        ]);
    }
}


