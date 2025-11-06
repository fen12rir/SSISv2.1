import {close, loadingText} from '../utils.js';

document.addEventListener('DOMContentLoaded', function() {
    const addSectionBtn = document.getElementById('add-section-btn');
    const modal = document.getElementById('add-section-modal');
    const modalContent = document.querySelector('.modal-content');
    const gradeLevelsList = document.getElementById('grade-levels-list');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');

    // Modal handler for adding section
    addSectionBtn.addEventListener('click', function() {
        modal.style.display = 'block';
        modalContent.innerHTML = loadingText;
        fetch(`../../../BackEnd/templates/admin/fetchAddSectionForm.php`)
        .then(response => response.text())
        .then(data => {
            modalContent.innerHTML = data;
            const form = document.getElementById('add-section-form');
            const button = document.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const cancelButton = form.querySelector('.btn-cancel');
                
                // Show loading state
                button.classList.add('loading');
                button.disabled = true;
                if (cancelButton) cancelButton.disabled = true;
                Loader.show();

                const formData = new FormData(form);
                fetch(`../../../BackEnd/api/admin/postAddSection.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(response=> response.json())
                .then(data =>{
                    if(data.success == false) {
                        Notification.show({
                            type: 'error',
                            title: 'Error',
                            message: data.message
                        });
                        form.reset();
                        // Remove loading state
                        button.classList.remove('loading');
                        button.disabled = false;
                        if (cancelButton) cancelButton.disabled = false;
                        Loader.hide();  
                    }
                    else {
                        Notification.show({
                            type: 'success',
                            title: 'Success',
                            message: 'Section added successfully.'
                        });
                        setTimeout(()=> {
                            Loader.hide();
                            modal.style.display = 'none';
                            loadGradeLevels();
                        }, 1000);
                    }
                })
                .catch(error=>{
                    console.log(error.message);
                    Notification.show({
                        type: 'error',
                        title: 'Error',
                        message: 'An error occurred while adding the section.'
                    });
                    // Remove loading state on error
                    button.classList.remove('loading');
                    button.disabled = false;
                    if (cancelButton) cancelButton.disabled = false;
                    Loader.hide();
                })
            })
            close(modal);
        })
    });

    // Load grade levels data
    async function loadGradeLevels() {
        loadingState.style.display = 'flex';
        gradeLevelsList.style.display = 'none';
        emptyState.style.display = 'none';

        try {
            const response = await fetch('../../../BackEnd/api/admin/fetchSectionsByGradeLevel.php');
            const result = await response.json();

            if (result.success && result.data && result.data.length > 0) {
                displayGradeLevels(result.data);
            } else {
                emptyState.style.display = 'block';
                gradeLevelsList.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading grade levels:', error);
            Notification.show({
                type: 'error',
                title: 'Error',
                message: 'Failed to load grade levels'
            });
            emptyState.style.display = 'block';
            gradeLevelsList.style.display = 'none';
        } finally {
            loadingState.style.display = 'none';
        }
    }

    // Display grade levels with expandable sections
    function displayGradeLevels(gradeLevels) {
        gradeLevelsList.style.display = 'block';
        emptyState.style.display = 'none';

        gradeLevelsList.innerHTML = gradeLevels.map(gradeLevel => {
            const sectionsCount = gradeLevel.Sections ? gradeLevel.Sections.length : 0;
            const sectionsHtml = gradeLevel.Sections && gradeLevel.Sections.length > 0
                ? `
                    <div class="sections-table-container">
                        <table class="sections-table">
                            <thead>
                                <tr>
                                    <th>Section</th>
                                    <th>Adviser</th>
                                    <th>Boys</th>
                                    <th>Girls</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${gradeLevel.Sections.map(section => `
                                    <tr>
                                        <td>${escapeHtml(section.Section_Name)}</td>
                                        <td>${escapeHtml(section.Adviser)}</td>
                                        <td>${section.Boys}</td>
                                        <td>${section.Girls}</td>
                                        <td>${section.Total}</td>
                                        <td>
                                            <a href="./admin_view_section.php?section_id=${section.Section_Id}" class="view-btn">
                                                <img src="../../assets/imgs/eye-regular.svg" alt="View">
                                            </a>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `
                : '<div class="no-sections">No sections in this grade level</div>';

            return `
                <div class="grade-level-item">
                    <div class="grade-level-header" data-grade-id="${gradeLevel.Grade_Level_Id}">
                        <div class="grade-level-title">
                            <span class="grade-level-name">${escapeHtml(gradeLevel.Grade_Level)}</span>
                            <span class="sections-count">${sectionsCount} section${sectionsCount !== 1 ? 's' : ''}</span>
                        </div>
                        <button class="toggle-btn" aria-label="Toggle sections">
                            <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </div>
                    <div class="grade-level-content" style="display: none;">
                        ${sectionsHtml}
                    </div>
                </div>
            `;
        }).join('');

        // Attach toggle event listeners
        document.querySelectorAll('.grade-level-header').forEach(header => {
            header.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const chevron = this.querySelector('.chevron');
                const isExpanded = content.style.display !== 'none';
                
                content.style.display = isExpanded ? 'none' : 'block';
                chevron.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        });

        // Attach view button handlers
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                Loader.show();
                const target = this.href;
                setTimeout(() => window.location.href = target, 100);
            });
        });
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initial load
    loadGradeLevels();
});

