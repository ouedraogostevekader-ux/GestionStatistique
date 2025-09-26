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

<!-- Modal de confirmation de reactivation de compte -->
<div class="modal fade" id="activeModal" tabindex="-1" aria-labelledby="activeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activeModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    Confirmation d'activation de compte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="activeMessage" class="mb-3">Êtes-vous sûr de vouloir activé ce compte ?</p>
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Cette action est irréversible.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="activeForm" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success" id="confirmActiveBtn">
                        <i class="bi bi-check-circle-fill"></i>
                        Réactiver
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de desactivation -->
<div class="modal fade" id="desactiveModal" tabindex="-1" aria-labelledby="desactiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="desactiveModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    Confirmation de desactivation de compte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="desactiveMessage" class="mb-3">Êtes-vous sûr de vouloir desactivé ce compte ?</p>
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Cette action est irréversible.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="desactiveForm" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger" id="confirmDesactiveBtn">
                        <i class="bi bi-dash-circle"></i>
                        Désactiver
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
