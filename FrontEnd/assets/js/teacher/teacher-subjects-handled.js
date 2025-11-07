document.addEventListener('DOMContentLoaded', function() {
    const subjectsList = document.querySelector('.subjects-list-wrapper');
    
    if (!subjectsList) {
        return;
    }

    const subjectsTable = subjectsList.querySelector('table');
    if (!subjectsTable) {
        return;
    }

    // Add search functionality
    const searchContainer = document.createElement('div');
    searchContainer.style.cssText = 'margin-bottom: 1rem; position: relative;';
    searchContainer.innerHTML = `
        <input type="text" id="subject-search" placeholder="Search by subject name, section, day, or time..." 
            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #3e9ec4; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.3s;">
    `;
    subjectsList.insertBefore(searchContainer, subjectsTable);
    
    const searchInput = document.getElementById('subject-search');
    const rows = subjectsTable.querySelectorAll('tbody tr');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let matches = false;
            
            cells.forEach((cell, index) => {
                // Skip the action column (last column)
                if (index < cells.length - 1) {
                    const text = cell.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        matches = true;
                    }
                }
            });
            
            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show "no results" message if needed
        let noResultsMsg = subjectsList.querySelector('.no-results');
        if (visibleCount === 0 && searchTerm !== '') {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results';
                noResultsMsg.style.cssText = 'text-align: center; padding: 2rem; color: #666; font-style: italic;';
                noResultsMsg.textContent = 'No subjects found matching your search.';
                subjectsTable.parentNode.insertBefore(noResultsMsg, subjectsTable.nextSibling);
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

    // Style error messages
    const errorMessage = subjectsList.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.style.cssText = 'padding: 1.5rem; margin: 1rem 0; background-color: #fee; color: #c33; border-radius: 8px; text-align: center; font-weight: 600;';
    }
    
    // Make grade buttons link to grades page (they can filter by section subject if needed)
    const gradeButtons = subjectsTable.querySelectorAll('.grade-students-btn');
    gradeButtons.forEach(button => {
        // The button already links to teacher_grades.php
        // If we had section subject ID, we could add it as a query parameter
        button.addEventListener('click', function(e) {
            // Allow default link behavior
        });
    });
});

