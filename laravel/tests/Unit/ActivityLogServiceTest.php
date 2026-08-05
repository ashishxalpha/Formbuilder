<?php

use App\Services\ActivityLogService;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('it can log activity', function () {
    $user = \App\Models\User::factory()->create();
    $service = new ActivityLogService();

    $log = $service->log($user->id, null, 'login', null, ['status' => 'success'], '127.0.0.1');

    expect($log)->toBeInstanceOf(ActivityLog::class);
    expect($log->action)->toBe('login');
    expect($log->new_values)->toBe(['status' => 'success']);
});
