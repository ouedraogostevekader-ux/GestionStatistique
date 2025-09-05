<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ProgrammeBudgetaire;

class CheckProgrammeActive
{
    public function handle(Request $request, Closure $next)
    {
         $programmeId = $request->route('id') ?? $request->route('programmebudgetaire');
        $programme = ProgrammeBudgetaire::find($programmeId);

        if ($programme && !$programme->isActive) {
            return redirect()->route('programmebudgetaire.index')
                             ->withErrors(['error' => "Le programme '{$programme->libelle}' est verrouillé et ne peut pas être modifié."]);
        }

        return $next($request);
    }
    }

