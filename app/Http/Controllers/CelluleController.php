<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CelluleController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:cellule-list', only: ['index', 'show']),
            new Middleware('permission:cellule-create', only: ['create', 'store']),
            new Middleware('permission:cellule-edit', only: ['edit', 'update']),
            new Middleware('permission:cellule-delete', only: ['destroy']),
        ];
    }
}
