<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerController;
use App\User;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserCreateEmail
{

    protected $user;
    protected $trabajador;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct( User $user, UserController $trabajador)
    {
        $this->receivers=$user;
        $this->trabajador=$trabajador;
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $this->trabajador->sendNewUserMail($event->user,$event->pass);
    }
}
