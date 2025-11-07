import{close,loadingText,modalHeader} from '../utils.js';

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('student-view-modal');
    const modalContent = document.getElementById('student-modal-content');
    
    // Use event delegation for dynamically added buttons
    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('view-student-button')) {
            const studentId = e.target.getAttribute('data-id');
            if (!studentId) return;
            
            // Show modal with flex
            modal.classList.add('show');
            modal.style.display = 'flex';
            modalContent.innerHTML = loadingText;
            
            try {
                const response = await fetch(`../../../BackEnd/templates/teacher/fetchStudentInformationModal.php?student_id=` + encodeURIComponent(studentId));
                
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                
                const data = await response.text();
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += data;
                close(modal);
            }
            catch(error) {
                console.error('Error fetching student information:', error);
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += '<div class="error-message" style="padding: 1.5rem; color: #c33; background: #fee; border-radius: 8px; margin: 1rem; text-align: center; font-weight: 600;">Error: ' + error.message + '</div>';
                close(modal);
            }
        }
    });
    
    // Add search functionality
    const studentsWrapper = document.querySelector('.students-list-wrapper');
    if (studentsWrapper) {
        const table = studentsWrapper.querySelector('.students-list');
        if (table) {
            // Create search input
            const searchContainer = document.createElement('div');
            searchContainer.style.cssText = 'margin-bottom: 1rem; position: relative;';
            searchContainer.innerHTML = `
                <input type="text" id="student-search" placeholder="Search students by name..." 
                    style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #3e9ec4; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.3s;">
            `;
            studentsWrapper.insertBefore(searchContainer, table);
            
            const searchInput = document.getElementById('student-search');
            const rows = table.querySelectorAll('tbody tr');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const nameCell = row.querySelector('td:first-child');
                    if (nameCell) {
                        const name = nameCell.textContent.toLowerCase();
                        if (name.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
                
                // Show "no results" message if needed
                let noResultsMsg = studentsWrapper.querySelector('.no-results');
                if (visibleCount === 0 && searchTerm !== '') {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement('div');
                        noResultsMsg.className = 'no-results';
                        noResultsMsg.style.cssText = 'text-align: center; padding: 2rem; color: #666; font-style: italic;';
                        noResultsMsg.textContent = 'No students found matching your search.';
                        table.parentNode.insertBefore(noResultsMsg, table.nextSibling);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            });
            
            // Add focus styles
            searchInput.addEventListener('focus', function() {
                this.style.borderColor = '#2d7a9a';
                this.style.boxShadow = '0 0 0 3px rgba(62, 158, 196, 0.1)';
            });
            
            searchInput.addEventListener('blur', function() {
                this.style.borderColor = '#3e9ec4';
                this.style.boxShadow = 'none';
            });
        }
    }
});