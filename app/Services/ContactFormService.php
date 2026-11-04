<?php

namespace App\Services;

use App\Repositories\ContactFormRepository;

class ContactFormService extends AbstractService
{
    protected $repo;

    public function __construct(ContactFormRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function getList($params = [])
    {
        return $this->repo->getList($params);
    }

    public function markAsRead($id)
    {
        return $this->repo->markAsRead($id);
    }

    public function markAsUnread($id)
    {
        return $this->repo->markAsUnread($id);
    }

    public function getUnreadCount()
    {
        return $this->repo->getUnreadCount();
    }

    public function createContactForm($data)
    {
        return $this->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
            'is_read' => false
        ]);
    }
}

