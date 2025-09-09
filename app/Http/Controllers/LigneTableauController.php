<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LigneTableauController extends Controller
{
    public function destroy($id)
    {
        try {
            $ligne = LigneTableau::findOrFail($id);
            $ligne->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}