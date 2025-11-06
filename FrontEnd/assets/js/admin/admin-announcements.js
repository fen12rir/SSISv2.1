document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('add-announcement-btn');
    const modal = document.getElementById('announcement-modal');
    const closeModal = document.getElementById('close-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const form = document.getElementById('announcement-form');
    const announcementsList = document.getElementById('announcements-list');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    const imageInput = document.getElementById('announcement-image');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const imagePreview = document.getElementById('image-preview');
    const removeImageBtn = document.getElementById('remove-image-btn');
    const removeImageFlag = document.getElementById('remove-image-flag');
    const modalTitle = document.getElementById('modal-title');

    let allAnnouncements = [];
    let isEditMode = false;

    // Open modal for adding
    addBtn.addEventListener('click', () => {
        isEditMode = false;
        modalTitle.textContent = 'Add Announcement';
        form.reset();
        imagePreviewContainer.style.display = 'none';
        removeImageFlag.value = 'false';
        document.getElementById('announcement-id').value = '';
        modal.style.display = 'block';
    });

    // Close modal
    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        form.reset();
        imagePreviewContainer.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        form.reset();
        imagePreviewContainer.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            form.reset();
            imagePreviewContainer.style.display = 'none';
        }
    });

    // Handle image preview
    imageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreviewContainer.style.display = 'block';
                removeImageFlag.value = 'false';
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle remove image
    removeImageBtn.addEventListener('click', () => {
        imageInput.value = '';
        imagePreviewContainer.style.display = 'none';
        removeImageFlag.value = 'true';
    });

    // Handle form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const announcementId = formData.get('announcement_id');
        
        Loader.show();

        try {
            let url = '../../../BackEnd/api/admin/postAnnouncement.php';
            if (isEditMode && announcementId) {
                url = '../../../BackEnd/api/admin/postUpdateAnnouncement.php';
            }

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            Loader.hide();

            if (result.success) {
                Notification.show({
                    type: 'success',
                    title: 'Success',
                    message: result.message || 'Announcement saved successfully'
                });
                
                modal.style.display = 'none';
                form.reset();
                imagePreviewContainer.style.display = 'none';
                loadAnnouncements();
            } else {
                Notification.show({
                    type: 'error',
                    title: 'Error',
                    message: result.message || 'Failed to save announcement'
                });
            }
        } catch (error) {
            Loader.hide();
            Notification.show({
                type: 'error',
                title: 'Error',
                message: error.message || 'An unexpected error occurred'
            });
        }
    });

    // Load announcements
    async function loadAnnouncements() {
        loadingState.style.display = 'flex';
        announcementsList.style.display = 'none';
        emptyState.style.display = 'none';

        try {
            const response = await fetch('../../../BackEnd/api/admin/getAnnouncements.php');
            const result = await response.json();

            if (result.success) {
                allAnnouncements = result.data || [];
                displayAnnouncements(allAnnouncements);
            } else {
                Notification.show({
                    type: 'error',
                    title: 'Error',
                    message: result.message || 'Failed to load announcements'
                });
            }
        } catch (error) {
            Notification.show({
                type: 'error',
                title: 'Error',
                message: error.message || 'Failed to load announcements'
            });
        } finally {
            loadingState.style.display = 'none';
        }
    }

    // Display announcements
    function displayAnnouncements(announcements) {
        if (announcements.length === 0) {
            announcementsList.style.display = 'none';
            emptyState.style.display = 'flex';
            return;
        }

        announcementsList.style.display = 'grid';
        emptyState.style.display = 'none';

        announcementsList.innerHTML = announcements.map(announcement => {
            const date = new Date(announcement.Date_Publication);
            const formattedDate = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            const imageHtml = announcement.Image_Path 
                ? `<img src="../../../${announcement.Image_Path}" alt="${announcement.Title}" class="announcement-image">`
                : '';

            return `
                <div class="announcement-card">
                    ${imageHtml}
                    <div class="announcement-content">
                        <h3 class="announcement-title">${escapeHtml(announcement.Title)}</h3>
                        <p class="announcement-text">${escapeHtml(announcement.Text)}</p>
                        <div class="announcement-meta">
                            <span class="announcement-date">📅 ${formattedDate}</span>
                        </div>
                        <div class="announcement-actions">
                            <button class="edit-btn" data-id="${announcement.Announcement_Id}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit
                            </button>
                            <button class="delete-btn" data-id="${announcement.Announcement_Id}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Attach event listeners
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = parseInt(e.currentTarget.getAttribute('data-id'));
                editAnnouncement(id);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = parseInt(e.currentTarget.getAttribute('data-id'));
                deleteAnnouncement(id);
            });
        });
    }

    // Edit announcement
    function editAnnouncement(id) {
        const announcement = allAnnouncements.find(a => a.Announcement_Id === id);
        if (!announcement) return;

        isEditMode = true;
        modalTitle.textContent = 'Edit Announcement';
        document.getElementById('announcement-id').value = announcement.Announcement_Id;
        document.getElementById('announcement-title').value = announcement.Title;
        document.getElementById('announcement-text').value = announcement.Text;
        document.getElementById('announcement-date').value = announcement.Date_Publication;
        removeImageFlag.value = 'false';

        if (announcement.Image_Path) {
            imagePreview.src = `../../../${announcement.Image_Path}`;
            imagePreviewContainer.style.display = 'block';
        } else {
            imagePreviewContainer.style.display = 'none';
        }

        imageInput.value = '';
        modal.style.display = 'block';
    }

    // Delete announcement
    async function deleteAnnouncement(id) {
        if (!confirm('Are you sure you want to delete this announcement?')) {
            return;
        }

        Loader.show();

        try {
            const formData = new FormData();
            formData.append('announcement_id', id);

            const response = await fetch('../../../BackEnd/api/admin/postDeleteAnnouncement.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            Loader.hide();

            if (result.success) {
                Notification.show({
                    type: 'success',
                    title: 'Success',
                    message: result.message || 'Announcement deleted successfully'
                });
                loadAnnouncements();
            } else {
                Notification.show({
                    type: 'error',
                    title: 'Error',
                    message: result.message || 'Failed to delete announcement'
                });
            }
        } catch (error) {
            Loader.hide();
            Notification.show({
                type: 'error',
                title: 'Error',
                message: error.message || 'An unexpected error occurred'
            });
        }
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initial load
    loadAnnouncements();
});

