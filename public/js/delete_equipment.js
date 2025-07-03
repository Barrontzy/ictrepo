document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.action-btn[title="Delete"]').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const assetTag = row.dataset.asset_tag;
            
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteAssetTag').textContent = assetTag;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    // Handle form submission via AJAX
    document.getElementById('deleteForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('ajax/delete_equipment.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            modal.hide();

            if (data.success) {
                showToast('Deleted successfully', 'success');
                document.querySelector(`tr[data-id="${formData.get('id')}"]`).remove();
            } else {
                showToast('Failed to delete', 'danger');
            }
        })
        .catch(err => {
            showToast('Error occurred during deletion', 'danger');
            console.error(err);
        });
    });

    // Toast utility
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }
});