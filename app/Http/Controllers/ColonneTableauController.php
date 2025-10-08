<?php

namespace App\Http\Controllers;

use App\Models\ColonneTableau;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ColonneTableauController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:colonne-list', only: ['index', 'show']),
            new Middleware('permission:colonne-create', only: ['create', 'store']),
            new Middleware('permission:colonne-edit', only: ['edit', 'update']),
            new Middleware('permission:colonne-delete', only: ['destroy']),
        ];
    }

    public function destroy($id)
    {
        try {
            $colonne = ColonneTableau::findOrFail($id);
            $colonne->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
