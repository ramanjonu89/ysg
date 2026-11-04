<?php
namespace App\Services;

use App\Models\Microsite;
use App\Repositories\MicrositeRepository;
use App\Repositories\MicrositeUserRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class MicrositeService extends AbstractService implements PageInterface
{
    protected $repo;
    protected $micrositeUserRepo;

    public function __construct(MicrositeRepository $repo, MicrositeUserRepository $micrositeUserRepository)
    {
        $this->repo = $repo;
        $this->micrositeUserRepo = $micrositeUserRepository;
    }

    /**
     * get list of seller for display at front end
     * @return Collection
     */
    public function getListFrontend(): Collection
    {
        return $this->repo->getList([
            'active' => true,
            'perPage' => 0,
        ]);
    }

    /**
     * get list of seller for display at front end
     * @return Collection
     */
    public function getList(): Collection
    {
        return $this->repo->getList([
            'perPage' => 0,
        ]);
    }

    public function toggleActive(int $id): bool
    {
        return (bool) $this->repo->toggleActive($id);
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
     * @param  User  $user
     * @param  Microsite  $site
     * @return bool
     */
    public function registerUser(User $user, Microsite $site): bool
    {
        $data = [
            'user_id' => $user->id,
            'microsite_id' => $site->id,
        ];
        $status = $this->micrositeUserRepo->create($data);
        return (bool) $status;
    }

    public function updateByRequest($id, Request $request) {
        $data = $request->all();

        if ($request->hasFile('favicon')) {
            $data['favicon'] = $request->file('favicon')->store('uploads/favicon');
        }

        return $this->repo->update($id, $data);
    }

    public function getMicrositeUserIds()
    {
        return $this->repo->getMicrositeUserIds();
    }
    
}
