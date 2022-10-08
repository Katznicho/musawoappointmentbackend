<?php

namespace App\Traits;

use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogTrait
{
    public function createActivityLog($action, $description, $platform = 'mobile', $status = true)
    {
        UserActivityLog::create([
            'user_name' => request()->user() ? request()->user()->name : null,
            'ip_address' => request()->ip(),
            'action' => $action,
            'method' => request()->method(),
            'path' => request()->path(),
            'description' => $description,
            'platform' => $platform,
            'status' => $status
        ]);
    }
}