<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LogController extends Controller //implements HasMiddleware
{

    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware('permission:log-list', only: ['index', 'causedBy', 'subjectOn']),
    //         new Middleware('permission:log-show', only: ['show', 'causedBy', 'subjectOn']),
    //     ];
    // }

    public function index(Request $request)
    {
        $logs = Activity::orderBy('updated_at', 'DESC')->get();

        return view('logs.index')->with('logs', $logs);
    }

    public function show(Request $request)
    {
        $logs = Activity::all();
        return view('logs.index')->with('logs', $logs);
    }

    public function causedBy($id)
    {
        $user = User::withTrashed()->find($id);
        $logs = $user->actions;
        return view('logs.causeBy')->with('logs', $logs);
    }

    public function subjectOn($id, $model)
    {
        $ap = app("App\Models\\$model");
        $mod = $ap::withTrashed()->find($id);
        $logs = $mod->activities;

        return view('logs.subjectOn')->with('logs', $logs);
    }
}
