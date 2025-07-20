function confirmDelete(projectId) {
    if (confirm('Are you sure you want to delete this project?')) {
        document.getElementById('delete-form-' + projectId).submit();
    }
}

// Make function globally available to prevent tree-shaking
window.confirmDelete = confirmDelete; 