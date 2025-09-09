<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProgrammeActiviteProgrammeBudgetaire extends Pivot
{
    use HasFactory, HasUlids;

    protected $table = 'programme_activite_programme_budgetaire';
    protected $fillable = [
        'programme_activite_id',
        'programme_budgetaire_id',
    ];
}
