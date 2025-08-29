// public/js/view-modal.js

// View Button Functionality
document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', function () {
        const row = this.closest('tr');
        const getValue = (v, fallback = 'Not specified') => !v || v.trim() === '' ? fallback : v;

        document.getElementById('viewAssetTag').textContent = getValue(row.dataset.asset_tag);
        document.getElementById('viewEquipment').textContent = getValue(row.dataset.equipment);
        document.getElementById('viewDepartment').textContent = getValue(row.dataset.department);
        document.getElementById('viewPerson').textContent = getValue(row.dataset.person);
        document.getElementById('viewLocation').textContent = getValue(row.dataset.location);

        const value = row.dataset.value;
        document.getElementById('viewValue').textContent = !value || value.trim() === ''
            ? 'N/A'
            : '₱' + parseFloat(value).toFixed(2);

        document.getElementById('viewStatus').textContent = getValue(row.dataset.status);
        document.getElementById('viewStatus').className = 'badge ' +
            (row.dataset.status.toLowerCase().includes('incomplete') ? 'badge-warning' : 'badge-success');

        document.getElementById('viewDateAcquired').textContent = getValue(row.dataset.date_acquired);
        document.getElementById('viewUsefulLife').textContent = getValue(row.dataset.useful_life);
        document.getElementById('viewInventoryNo').textContent = getValue(row.dataset.inventory_item_no);
        document.getElementById('viewHardware').textContent = getValue(row.dataset.hardware_specifications);
        document.getElementById('viewSoftware').textContent = getValue(row.dataset.software_specifications);
        document.getElementById('viewRemarks').textContent = getValue(row.dataset.remarks);
        document.getElementById('viewCreatedAt').textContent = getValue(row.dataset.created_at);

        new bootstrap.Modal(document.getElementById('viewModal')).show();
    });
});