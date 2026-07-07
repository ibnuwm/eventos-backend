<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $tenantId;
    public string $vendorName;
    public string $status;
    public string $message;

    public function __construct(string $tenantId, string $vendorName, string $status, string $message)
    {
        $this->tenantId = $tenantId;
        $this->vendorName = $vendorName;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Saluran WebSocket Reverb di mana event ini disiarkan
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tenant.' . $this->tenantId),
            new Channel('vendor-os-live')
        ];
    }

    public function broadcastAs(): string
    {
        return 'VendorStatusUpdated';
    }
}
