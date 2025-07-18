<?php 

namespace Arden28\Guardian\Events;

use Arden28\Guardian\Events\UserLoggedIn;
use Illuminate\Support\Facades\Log;

class LogUserLogin
{
    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        $user = $event->user;

        // You can customize the log logic here
        Log::info("User logged in: ID {$user->id}, Email: {$user->email}, IP: " . request()->ip());
    }
}
