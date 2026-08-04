<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AICompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $aiJobId;
    public string $status;

    public function __construct(int $aiJobId, string $status)
    {
        $this->aiJobId = $aiJobId;
        $this->status = $status;
    }
}
