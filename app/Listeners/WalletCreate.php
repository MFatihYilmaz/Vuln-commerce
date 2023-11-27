<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Wallet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WalletCreate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
          // Kullanıcıya ait wallet oluştur
          $wallet = new Wallet();
          $wallet->user_id = $event->user_id;
          $wallet->deposit = 0; 
          $wallet->save();
    }
}
