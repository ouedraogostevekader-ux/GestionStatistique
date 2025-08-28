<!-- resources/views/components/delete-modal.blade.php -->
<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    Confirmation de suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage" class="mb-3">Êtes-vous sûr de vouloir supprimer cet élément ?</p>
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Cette action est irréversible.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
