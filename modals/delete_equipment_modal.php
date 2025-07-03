<!-- modals/delete_equipment_modal.php -->

<!-- Delete Button Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-trash-alt me-2"></i>Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <div class="alert alert-warning">
                Are you sure you want to delete this equipment?
                <p class="mt-2 mb-0"><strong>Asset Tag:</strong> <span id="deleteAssetTag"></span></p>
                <input type="hidden" name="id" id="deleteId">
            </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Delete</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
        </form>
    </div>
</div>

<script src="public/js/delete_equipment.js"></script>