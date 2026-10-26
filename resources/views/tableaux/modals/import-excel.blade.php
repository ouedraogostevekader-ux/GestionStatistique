<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('tableaux.import-excel', $tableau->id) }}" method="POST" enctype="multipart/form-data" id="formImportExcel">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title d-flex align-items-center" id="modalImportExcelLabel">
                        <i class="fas fa-file-excel me-2"></i> Importer le Canevas Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Téléversez le canevas Excel pré-formaté renseigné par la structure sectorielle. Le système effectuera un contrôle d'intégrité transactionnel et mettra à jour le statut de l'assignation.
                    </p>

                    {{-- Sélection de l'assignation si multiple --}}
                    @if (isset($assignations) && $assignations->count() > 1)
                        <div class="mb-3">
                            <label for="modal_assignation_id" class="form-label fw-bold">Version & Période d'assignation <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_assignation_id" name="assignation_id" required>
                                <option value="">-- Choisir la version à renseigner --</option>
                                @foreach ($assignations as $asgn)
                                    <option value="{{ $asgn->id }}" {{ (isset($latestAssignation) && $latestAssignation->id === $asgn->id) ? 'selected' : '' }}>
                                        {{ $asgn->version->code ?? 'Version' }} ({{ $asgn->version->annee ?? '' }})
                                        @if($asgn->structure) - {{ $asgn->structure->sigle }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif (isset($latestAssignation) && $latestAssignation)
                        <input type="hidden" name="assignation_id" value="{{ $latestAssignation->id }}">
                        <div class="alert alert-light border d-flex align-items-center mb-3 py-2 px-3">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            <div>
                                <small class="text-muted d-block">Assignation ciblée :</small>
                                <strong>{{ $latestAssignation->version->code ?? 'Version' }} ({{ $latestAssignation->version->annee ?? '' }})</strong>
                                @if($latestAssignation->structure) - <span class="badge bg-secondary">{{ $latestAssignation->structure->sigle }}</span>@endif
                            </div>
                        </div>
                    @elseif (isset($assignation) && $assignation)
                        <input type="hidden" name="assignation_id" value="{{ $assignation->id }}">
                        <div class="alert alert-light border d-flex align-items-center mb-3 py-2 px-3">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            <div>
                                <small class="text-muted d-block">Assignation ciblée :</small>
                                <strong>{{ $assignation->version->code ?? 'Version' }} ({{ $assignation->version->annee ?? '' }})</strong>
                                @if($assignation->structure) - <span class="badge bg-secondary">{{ $assignation->structure->sigle }}</span>@endif
                            </div>
                        </div>
                    @endif

                    {{-- Zone Drag & Drop stylisée --}}
                    <div class="dropzone-area p-4 text-center rounded border border-2 border-dashed bg-light position-relative" id="excelDropzone" style="cursor: pointer; transition: all 0.2s ease;">
                        <input type="file" name="excel_file" id="excelFileInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".xlsx, .xls" required style="cursor: pointer;">
                        <div id="dropzonePrompt">
                            <i class="fas fa-cloud-upload-alt text-success fa-3x mb-2"></i>
                            <h6 class="fw-bold mb-1">Glissez-déposez votre fichier Excel ici</h6>
                            <p class="text-muted small mb-1">ou cliquez pour parcourir vos dossiers</p>
                            <span class="badge bg-light text-secondary border">Formats acceptés : .xlsx, .xls (max 10 Mo)</span>
                        </div>
                        <div id="fileSelectedDisplay" class="d-none">
                            <i class="fas fa-file-excel text-success fa-3x mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1" id="selectedFileName">fichier.xlsx</h6>
                            <span class="badge bg-success" id="selectedFileSize">0 Ko</span>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none p-0" id="btnRemoveFile">
                                    <i class="fas fa-times-circle me-1"></i> Choisir un autre fichier
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Option d'écrasement --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="ecraser" id="ecraserCheck" value="1" checked>
                        <label class="form-check-label small text-muted" for="ecraserCheck">
                            Écraser les valeurs existantes pour les cellules renseignées dans le fichier
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btnSubmitImport">
                        <i class="fas fa-check-circle me-1"></i> Téléverser & Valider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('excelFileInput');
    const dropzone = document.getElementById('excelDropzone');
    const promptDisplay = document.getElementById('dropzonePrompt');
    const fileDisplay = document.getElementById('fileSelectedDisplay');
    const fileNameSpan = document.getElementById('selectedFileName');
    const fileSizeSpan = document.getElementById('selectedFileSize');
    const btnRemove = document.getElementById('btnRemoveFile');
    const form = document.getElementById('formImportExcel');
    const btnSubmit = document.getElementById('btnSubmitImport');

    if (!fileInput || !dropzone) return;

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('border-success', 'bg-white');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border-success', 'bg-white');
        }, false);
    });

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            fileNameSpan.textContent = file.name;
            const sizeInKb = (file.size / 1024).toFixed(1);
            fileSizeSpan.textContent = sizeInKb > 1024 ? (sizeInKb / 1024).toFixed(2) + ' Mo' : sizeInKb + ' Ko';
            promptDisplay.classList.add('d-none');
            fileDisplay.classList.remove('d-none');
        }
    });

    if (btnRemove) {
        btnRemove.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.value = '';
            fileDisplay.classList.add('d-none');
            promptDisplay.classList.remove('d-none');
        });
    }

    if (form && btnSubmit) {
        form.addEventListener('submit', function () {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Traitement en cours...';
        });
    }
});
</script>
