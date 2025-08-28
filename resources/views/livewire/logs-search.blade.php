<div>
    <div class="box box-primary">
        <div class="col-sm-3 form-group">
            <input type="text" name="search" wire:model.live.debounce.1s="search" placeholder="Tapez ICI" class="form-control search-input">
        </div>

        <div class="table-responsive">
            @include('logs.table')
        </div>
    </div>
</div>
