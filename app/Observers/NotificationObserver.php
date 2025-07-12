<?php

namespace App\Observers;

use App\Models\Notification;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationObserver
{
    /**
     * Handle the Notification "creating" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function creating(Notification $notification)
    {
        if (empty($notification->account_type)) {
            $token = JWTAuth::getToken();

            if ($token) {
                $payload = JWTAuth::getPayload($token);
                $accountType = $payload->get('account_type');
                $notification->account_type = $accountType !== null ? $accountType : null;
            } else {
                $notification->account_type = null;
            }
        }
    }
}
