document.addEventListener('DOMContentLoaded', function() {
    const uploadBtn = document.getElementById('upload-file-btn');
    const uploadModal = document.getElementById('upload-modal');
    const closeModal = document.getElementById('close-upload-modal');
    const cancelUpload = document.getElementById('cancel-upload');
    const uploadForm = document.getElementById('upload-form');
    const filesGrid = document.getElementById('files-grid');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    const searchInput = document.getElementById('search-files');
    const previewModal = document.getElementById('preview-modal');
    const closePreviewModal = document.getElementById('close-preview-modal');
    const previewContent = document.getElementById('preview-content');
    const previewFileName = document.getElementById('preview-file-name');
    const downloadFromPreview = document.getElementById('download-from-preview');

    let allFiles = [];

    // Open upload modal
    uploadBtn.addEventListener('click', () => {
        uploadModal.style.display = 'block';
    });

    // Close upload modal
    closeModal.addEventListener('click', () => {
        uploadModal.style.display = 'none';
        uploadForm.reset();
    });

    cancelUpload.addEventListener('click', () => {
        uploadModal.style.display = 'none';
        uploadForm.reset();
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === uploadModal) {
            uploadModal.style.display = 'none';
            uploadForm.reset();
        }
        if (e.target === previewModal) {
            previewModal.style.display = 'none';
        }
    });

    // Close preview modal
    closePreviewModal.addEventListener('click', () => {
        previewModal.style.display = 'none';
    });

    // Handle file upload
    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const fileInput = document.getElementById('file-input');
        const description = document.getElementById('file-description').value.trim();

        if (!fileInput.files[0]) {
            Notification.show({
                type: 'error',
                title: 'Error',
                message: 'Please select a file to upload'
            });
            return;
        }

        Loader.show();

        try {
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            if (description) {
                formData.append('description', description);
            }

            const response = await fetch('../../../BackEnd/api/teacher/postLockerUpload.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            Loader.hide();

            if (result.success) {
                Notification.show({
                    type: 'success',
                    title: 'Success',
                    message: result.message || 'File uploaded successfully'
                });
                
                uploadModal.style.display = 'none';
                uploadForm.reset();
                loadFiles();
            } else {
                Notification.show({
                    type: 'error',
                    title: 'Error',
                    message: result.message || 'Failed to upload file'
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

    // Load files
    async function loadFiles() {
        loadingState.style.display = 'flex';
        filesGrid.style.display = 'none';
        emptyState.style.display = 'none';

        try {
            const response = await fetch('../../../BackEnd/api/teacher/getLockerFiles.php');
            const result = await response.json();

            if (result.success) {
                allFiles = result.data || [];
                displayFiles(allFiles);
            } else {
                Notification.show({
                    type: 'error',
                    title: 'Error',
                    message: result.message || 'Failed to load files'
                });
            }
        } catch (error) {
            Notification.show({
                type: 'error',
                title: 'Error',
                message: error.message || 'Failed to load files'
            });
        } finally {
            loadingState.style.display = 'none';
        }
    }

    // Display files
    function displayFiles(files) {
        if (files.length === 0) {
            filesGrid.style.display = 'none';
            emptyState.style.display = 'flex';
            return;
        }

        filesGrid.style.display = 'grid';
        emptyState.style.display = 'none';
        filesGrid.innerHTML = '';

        files.forEach(file => {
            const fileCard = createFileCard(file);
            filesGrid.appendChild(fileCard);
        });
    }

    // Create file card
    function createFileCard(file) {
        const card = document.createElement('div');
        card.className = 'file-card';
        card.dataset.fileId = file.Locker_File_Id;

        const fileIcon = getFileIcon(file.File_Type, file.Original_File_Name);
        const fileSize = formatFileSize(file.File_Size);
        const uploadDate = formatDate(file.Uploaded_At);

        card.innerHTML = `
            <div class="file-icon">${fileIcon}</div>
            <div class="file-info">
                <h3 class="file-name" title="${escapeHtml(file.Original_File_Name)}">${escapeHtml(file.Original_File_Name)}</h3>
                <p class="file-meta">
                    <span class="file-type">${file.File_Type}</span>
                    <span class="file-size">${fileSize}</span>
                </p>
                <p class="file-date">${uploadDate}</p>
                ${file.Description ? `<p class="file-description">${escapeHtml(file.Description)}</p>` : ''}
            </div>
            <div class="file-actions">
                <button class="action-btn preview-btn" data-file-id="${file.Locker_File_Id}" data-file-name="${escapeHtml(file.Original_File_Name)}" title="Preview">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                <button class="action-btn download-btn" data-file-id="${file.Locker_File_Id}" title="Download">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </button>
                <button class="action-btn delete-btn" data-file-id="${file.Locker_File_Id}" title="Delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>
        `;

        // Preview button - make file card clickable
        const previewBtn = card.querySelector('.preview-btn');
        const fileInfo = card.querySelector('.file-info');
        
        previewBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            showPreview(file);
        });
        
        // Also make the file card clickable (except action buttons)
        fileInfo.addEventListener('click', () => {
            showPreview(file);
        });

        // Download button
        card.querySelector('.download-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            window.location.href = `../../../BackEnd/api/teacher/getLockerDownload.php?fileId=${file.Locker_File_Id}`;
        });

        // Delete button
        card.querySelector('.delete-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            if (confirm('Are you sure you want to delete this file?')) {
                deleteFile(file.Locker_File_Id);
            }
        });

        return card;
    }

    // Delete file
    async function deleteFile(fileId) {
        Loader.show();

        try {
            const response = await fetch('../../../BackEnd/api/teacher/postLockerDelete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ fileId: fileId })
            });

            const result = await response.json();

            Loader.hide();

            if (result.success) {
                Notification.show({
                    type: 'success',
                    title: 'Success',
                    message: result.message || 'File deleted successfully'
                });
                loadFiles();
            } else {
                Notification.show({
                    type: 'error',
                    title: 'Error',
                    message: result.message || 'Failed to delete file'
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

    // Preview file
    function showPreview(file) {
        const ext = file.Original_File_Name.split('.').pop().toLowerCase();
        const previewUrl = `../../../BackEnd/api/teacher/getLockerPreview.php?fileId=${file.Locker_File_Id}`;
        
        previewFileName.textContent = file.Original_File_Name;
        previewModal.style.display = 'block';
        previewContent.innerHTML = '<div class="preview-loading">Loading preview...</div>';
        
        // Set download button
        downloadFromPreview.onclick = () => {
            window.location.href = `../../../BackEnd/api/teacher/getLockerDownload.php?fileId=${file.Locker_File_Id}`;
        };
        
        // Handle different file types
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            // Images
            previewContent.innerHTML = `<img src="${previewUrl}" alt="${escapeHtml(file.Original_File_Name)}" class="preview-image">`;
        } else if (ext === 'pdf') {
            // PDFs
            previewContent.innerHTML = `<iframe src="${previewUrl}" class="preview-iframe" type="application/pdf"></iframe>`;
        } else if (ext === 'txt') {
            // Text files
            fetch(previewUrl)
                .then(response => response.text())
                .then(text => {
                    previewContent.innerHTML = `<pre class="preview-text">${escapeHtml(text)}</pre>`;
                })
                .catch(error => {
                    previewContent.innerHTML = `<p class="preview-error">Cannot preview this file type. Please download to view.</p>`;
                });
        } else {
            // Other file types
            previewContent.innerHTML = `
                <div class="preview-unavailable">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <p>Preview not available for this file type.</p>
                    <p>Please download to view the file.</p>
                </div>
            `;
        }
    }

    // Search functionality
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            displayFiles(allFiles);
            return;
        }

        const filtered = allFiles.filter(file => 
            file.Original_File_Name.toLowerCase().includes(searchTerm) ||
            (file.Description && file.Description.toLowerCase().includes(searchTerm)) ||
            file.File_Type.toLowerCase().includes(searchTerm)
        );

        displayFiles(filtered);
    });

    // Helper functions
    function getFileIcon(fileType, fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            return '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>';
        } else if (['pdf'].includes(ext)) {
            return '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
        } else if (['doc', 'docx'].includes(ext)) {
            return '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
        } else if (['ppt', 'pptx'].includes(ext)) {
            return '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>';
        } else if (['xls', 'xlsx'].includes(ext)) {
            return '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M16 13H8M16 17H8M10 9H8"></path></svg>';
        } else if (['zip', 'rar'].includes(ext)) {
            return '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>';
        } else {
            return '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initial load
    loadFiles();
});

