<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VersionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $formVersionId;

    public function __construct(int $formVersionId)
    {
        $this->formVersionId = $formVersionId;
    }
}
