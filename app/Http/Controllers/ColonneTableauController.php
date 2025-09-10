<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ColonneTableauController extends Controller
{
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