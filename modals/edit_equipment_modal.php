<!-- modals/edit_equipment_modal.php -->

<!-- Edit Button Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-warning text-white">
            <h5 class="modal-title" id="editModalLabel"><i class="fas fa-edit me-2"></i>Edit Equipment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <form id="editEquipmentForm" method="POST">
            <input type="hidden" id="editId" name="id">

            <div class="row">
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Asset Tag *</label>
                <input type="text" class="form-control" id="editAssetTag" name="asset_tag" required>
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Equipment Type *</label>
                <select class="form-select" id="editEquipment" name="property_equipment" required>
                    <option value="">Select Equipment Type</option>
                    <option value="Printer">Printer</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Desktop Computer">Desktop Computer</option>
                    <option value="Telephone">Telephone</option>
                    <option value="Wireless Access Point">Wireless Access Point</option>
                    <option value="Switch">Network Switch</option>
                    <option value="Other">Other</option>
                </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Department</label>
                <input type="text" class="form-control" id="editDepartment" name="department">
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Assigned Person</label>
                <input type="text" class="form-control" id="editPerson" name="assigned_person">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Location</label>
                <input type="text" class="form-control" id="editLocation" name="location">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Unit Price</label>
                <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number" class="form-control" id="editValue" name="unit_price" step="0.01">
                </div>
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Date Acquired</label>
                <input type="date" class="form-control" id="editDateAcquired" name="date_acquired">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Useful Life</label>
                <input type="text" class="form-control" id="editUsefulLife" name="useful_life">
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status</label>
                <select class="form-select" id="editStatus" name="status">
                    <option value="Working Unit">Working Unit</option>
                    <option value="Under Maintenance">Under Maintenance</option>
                    <option value="Out of Order">Out of Order</option>
                    <option value="Incomplete - Needs Data Entry">Incomplete - Needs Data Entry</option>
                </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hardware Specifications</label>
                <textarea class="form-control" id="editHardware" name="hardware_specifications" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Software Specifications</label>
                <textarea class="form-control" id="editSoftware" name="software_specifications" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Inventory Item No.</label>
                <input type="text" class="form-control" id="editInventoryNo" name="inventory_item_no">
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Remarks</label>
                <input type="text" class="form-control" id="editRemarks" name="remarks">
                <!-- <select class="form-select" id="editRemarks" name="remarks">
                    <option value="Working Unit">Working Unit</option>
                    <option value="Under Maintenance">Under Maintenance</option>
                    <option value="Out of Order">Out of Order</option>
                    <option value="Incomplete - Needs Data Entry">Incomplete - Needs Data Entry</option>
                </select> -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">
                <i class="fas fa-save me-1"></i>Update Equipment
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i>Cancel
                </button>
            </div>
            </form>
        </div>
        </div>
    </div>
</div>

<script src="public/js/edit_equipment.js"></script>