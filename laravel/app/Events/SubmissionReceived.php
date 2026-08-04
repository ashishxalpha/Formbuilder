<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $submissionId;

    public function __construct(int $submissionId)
    {
        $this->submissionId = $submissionId;
    }
}
