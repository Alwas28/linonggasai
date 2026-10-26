<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $action = $request->string('aksi')->toString();

        return view('admin.activity', [
            'logs' => ActivityLog::query()
                ->with('user')
                ->when(array_key_exists($action, ActivityLog::ACTIONS), fn (Builder $query) => $query->where('action', $action))
                ->latest('created_at')
                ->latest('id')
                ->paginate(30)
                ->withQueryString(),
            'activeAction' => $action,
        ]);
    }
}
