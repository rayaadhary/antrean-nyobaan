<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianDipanggil implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $antrian;
    public $loket;

    public function __construct($antrian, $loket)
    {
        $this->antrian = $antrian;
        $this->loket = $loket;
    }

    public function broadcastOn()
    {
        return new Channel('antrian-channel');
    }
}
