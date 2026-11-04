<?php

namespace App\Repositories;

use App\Models\ContactForm;

class ContactFormRepository extends AbstractRepo
{
    const PER_PAGE = 20;

    public function getModel(): string
    {
        return ContactForm::class;
    }

    public function getList($params = [])
    {
        $search = $params['search'] ?? null;
        $orderBy = $params['orderBy'] ?? 'created_at';
        $orderDirection = $params['orderDirection'] ?? 'desc';
        $perPage = $params['perPage'] ?? self::PER_PAGE;
        $isRead = $params['isRead'] ?? null;

        $query = $this->model->query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('message', 'like', '%' . $search . '%');
            });
        }

        if ($isRead !== null) {
            $query->where('is_read', $isRead);
        }

        $query->orderBy($orderBy, $orderDirection);

        if ($perPage > 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function markAsRead($id)
    {
        return $this->update($id, ['is_read' => true]);
    }

    public function markAsUnread($id)
    {
        return $this->update($id, ['is_read' => false]);
    }

    public function getUnreadCount()
    {
        return $this->model->where('is_read', false)->count();
    }
}

