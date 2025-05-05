<?php
namespace App\Events;

use App\Models\Antrian;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AntrianDiambil implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $antrian;

    public function __construct(Antrian $antrian)
    {
        $this->antrian = $antrian;
    }

    public function broadcastOn()
    {
        return new Channel('antrian-channel');
    }

    public function broadcastWith()
    {
        return [
            'antrian' => $this->antrian
        ];
    }

    public function broadcastAs()
    {
        return 'AntrianDiambil';
    }
}
