// public/js/edit-modal.js

// Edit Button Functionality
document.addEventListener('DOMContentLoaded', function () {
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const editForm = document.getElementById('editForm');
    const toastContainer = document.getElementById('toastContainer');

    // Handle Edit button click
    document.querySelectorAll('.action-btn[title="Edit"]').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const dataset = row.dataset;

            document.getElementById('editId').value = dataset.id;
            document.getElementById('editAssetTag').value = dataset.asset_tag || '';
            document.getElementById('editEquipment').value = dataset.equipment || '';
            document.getElementById('editDepartment').value = dataset.department || '';
            document.getElementById('editPerson').value = dataset.person || '';
            document.getElementById('editLocation').value = dataset.location || '';
            document.getElementById('editValue').value = dataset.value || '';
            document.getElementById('editDateAcquired').value = dataset.date_acquired || '';
            document.getElementById('editUsefulLife').value = dataset.useful_life || '';
            document.getElementById('editStatus').value = dataset.status || 'Working Unit';
            document.getElementById('editHardware').value = dataset.hardware_specifications || '';
            document.getElementById('editSoftware').value = dataset.software_specifications || '';
            document.getElementById('editInventoryNo').value = dataset.inventory_item_no || '';
            document.getElementById('editRemarks').value = dataset.remarks || '';

            editModal.show();
        });
    });

    // Helper to show toast
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>`;

        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // Submit handler (AJAX + Validation + Live DOM Update)
    editForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const assetTag = document.getElementById('editAssetTag').value.trim();
        const equipment = document.getElementById('editEquipment').value.trim();

        if (!assetTag || !equipment) {
            showToast('Asset Tag and Equipment Type are required.', 'danger');
            return;
        }

        const formData = new FormData(editForm);
        const id = formData.get('id');

        fetch('edit_equipment_modal_submit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Equipment updated successfully!', 'success');
                editModal.hide();

                // Live update the table row
                const row = document.querySelector(`tr[data-id="${id}"]`);
                row.dataset.asset_tag = formData.get('asset_tag');
                row.dataset.property_equipment = formData.get('property_equipment');
                row.dataset.department = formData.get('department');
                row.dataset.assigned_person = formData.get('assigned_person');
                row.dataset.location = formData.get('location');
                row.dataset.unit_price = formData.get('unit_price');
                row.dataset.date_acquired = formData.get('date_acquired');
                row.dataset.useful_life = formData.get('useful_life');
                row.dataset.status = formData.get('status');
                row.dataset.hardware_specifications = formData.get('hardware_specifications');
                row.dataset.software_specifications = formData.get('software_specifications');
                row.dataset.inventory_item_no = formData.get('inventory_item_no');
                row.dataset.remarks = formData.get('remarks');

                row.querySelector('.asset-tag').textContent = formData.get('asset_tag');
                row.cells[1].textContent = formData.get('property_equipment') || 'Not specified';
                row.cells[2].innerHTML = formData.get('department')
                    ? `<span class="badge badge-outline">${formData.get('department')}</span>`
                    : '<span style="color: #94a3b8; font-style: italic;">Not specified</span>';
                row.cells[3].textContent = formData.get('assigned_person') || 'Not specified';
                row.cells[4].textContent = formData.get('location') || 'Not specified';

                const value = formData.get('unit_price');
                row.cells[5].innerHTML = value
                    ? `₱${parseFloat(value).toFixed(2)}`
                    : '<span style="color: #94a3b8;">N/A</span>';

                const statusCell = row.cells[6].querySelector('.badge');
                const status = formData.get('status') || 'Working Unit';
                statusCell.textContent = status;
                statusCell.className = 'badge ' + (status.toLowerCase().includes('incomplete') ? 'badge-warning' : 'badge-success');

            } else {
                showToast(data.message || 'Error updating equipment.', 'danger');
            }
        })
        .catch(error => {
            console.error(error);
            showToast('Server error. Try again later.', 'danger');
        });
    });
});
