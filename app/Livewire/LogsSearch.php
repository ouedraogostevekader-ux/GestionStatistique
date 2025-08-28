<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class LogsSearch extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    //public $logs;
    public $search = "";
    public $shortField = "updated_at";
    public $shortDirection = "DESC";

    public function search()
    {
        //$this->logs = Activity::orderBy('updated_at','DESC')->paginate(20);
    }

    public function sortBy(string $field)
    {
        if ($this->shortField === $field) {
            $this->shortDirection = $this->shortDirection === "ASC" ? "DESC" : "ASC";
        } else {
            $this->shortDirection = 'ASC';
            $this->shortField = $field;
        }
    }

    public function render()
    {
        $query = Activity::orderBy($this->shortField, $this->shortDirection);
        if ($this->search != "") {
            $query->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere(function ($query) {
                        $query->where('causer_type', User::class)
                            ->whereExists(function ($sub) {
                                $sub->select(DB::raw(1))
                                    ->from('users')
                                    ->whereRaw('CAST(activity_log.causer_id AS BIGINT) = users.id')
                                    ->where('users.email', 'like', '%' . $this->search . '%')
                                    ->whereNull('users.deleted_at');
                            });
                    });
            });
        }
        return view('livewire.logs-search', ['logs' => $query->paginate(20)]);
    }
}
