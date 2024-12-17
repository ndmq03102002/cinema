<?php

namespace App\Services;

use App\Enums\Constant;
use App\Jobs\MarkReadNotifyJob;
use App\Models\User;
use Kreait\Firebase\Database\Transaction;
use Kreait\Firebase\Factory;

class FirebaseService
{
    public $firebase;
    public $pathAPI = 'user/';

    public function __construct()
    {
        $path = storage_path('firebase.json');
        $serviceAccount = file_get_contents($path);

        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri(config('services.firebase.database_url'))
            ->createDatabase();
    }

    /**
     * get notify
     * @param int $userID
     * @return  object $notify
     * @author Nampx
     */
    public function getNotify(int $userID)
    {
        return $this->firebase->getReference($this->pathAPI . $userID)
            ->getValue();
    }

    /**
     * mark read notify by id
     * @param int $userID
     * @param string $notifyId
     * @return  object $notify
     * @author Nampx
     */
    public function markReadNotify(int $userID, string $notifyId)
    {
        return $this->firebase->getReference($this->pathAPI . $userID . '/' . $notifyId . '/read')
            ->set(true)->getValue();
    }

    /**
     * mark read notify by id
     * @param int $userID
     * @return  object $notify
     * @author Nampx
     */
    public function markReadAllNotify(int $userID)
    {
        $notifyUnreads = $this->firebase->getReference($this->pathAPI . $userID)
            ->orderByChild('read')
            ->equalTo(false)
            ->getValue();

        MarkReadNotifyJob::dispatch($userID, $notifyUnreads);
    }

    /**
     * delete notify
     * @param int $userID
     * @param string $id
     * @return  object $notify
     * @author Nampx
     */
    public function deleteNotify(int $userID, string $id)
    {
        return $this->firebase->getReference($this->pathAPI . $userID)
            ->getChild($id)
            ->remove();
    }
}
