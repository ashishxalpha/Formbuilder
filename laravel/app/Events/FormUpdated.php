<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $formId;

    public function __construct(int $formId)
    {
        $this->formId = $formId;
    }
}
