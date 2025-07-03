<!-- modals/view_equipment_modal.php -->

<!-- View Button Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Equipment Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row">
            <div class="col-md-6">
                <p><strong>Asset Tag:</strong> <span id="viewAssetTag"></span></p>
                <p><strong>Equipment Type:</strong> <span id="viewEquipment"></span></p>
                <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
                <p><strong>Assigned Person:</strong> <span id="viewPerson"></span></p>
                <p><strong>Location:</strong> <span id="viewLocation"></span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Unit Price:</strong> <span id="viewValue" class="fw-bold text-success"></span></p>
                <p><strong>Date Acquired:</strong> <span id="viewDateAcquired"></span></p>
                <p><strong>Useful Life:</strong> <span id="viewUsefulLife"></span></p>
                <p><strong>Status:</strong> <span id="viewStatus" class="badge"></span></p>
                <p><strong>Inventory Item No.:</strong> <span id="viewInventoryNo"></span></p>
            </div>
            </div>
            <p><strong>Hardware Specifications:</strong> <span id="viewHardware"></span></p>
            <p><strong>Software Specifications:</strong> <span id="viewSoftware"></span></p>
            <p><strong>Remarks:</strong> <span id="viewRemarks"></span></p>
            <p><strong>Date Added:</strong> <span id="viewCreatedAt"></span></p>
        </div>
        </div>
    </div>
</div>

<script src="public/js/view_equipment.js"></script>