import{modalHeader, close, loadingText} from '../utils.js'; 
document.addEventListener('DOMContentLoaded', function() {

    const editBtn = document.getElementById('edit-section-btn');
    const modal = document.getElementById('admin-view-section-edit-modal');
    const modalContent = document.getElementById('admin-view-section-edit-content');

    const sectionId = new URLSearchParams(window.location.search).get('section_id');
    console.log(sectionId);
    editBtn.addEventListener('click', function() {
        modal.style.display = 'block';
        modalContent.innerHTML = loadingText;
        fetch(`../../../BackEnd/templates/admin/fetchEditSectionForm.php?section_id=${sectionId}`)
        .then(response => response.text())
        .then(data=> {
            modalContent.innerHTML = modalHeader();
            modalContent.innerHTML += data;
            close(modal);

            // Initialize student selection after form is loaded
            setTimeout(() => {
                if (typeof window.initializeStudentSelection === 'function') {
                    window.initializeStudentSelection();
                } else {
                    // Fallback: manually initialize if function not available
                    const checkboxes = document.querySelectorAll('.student-checkbox');
                    const selectedCount = document.getElementById('selected-count');
                    const searchInput = document.getElementById('student-search');
                    const studentItems = document.querySelectorAll('.student-checkbox-item');
                    
                    function updateCount() {
                        const checked = document.querySelectorAll('.student-checkbox:checked').length;
                        if (selectedCount) selectedCount.textContent = checked;
                    }
                    
                    // Initialize count
                    updateCount();
                    
                    // Add event listeners
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const item = this.closest('.student-checkbox-item');
                            if (this.checked) {
                                if (item) item.classList.add('selected');
                            } else {
                                if (item) item.classList.remove('selected');
                            }
                            updateCount();
                        });
                        
                        // Initialize selected state
                        const item = checkbox.closest('.student-checkbox-item');
                        if (checkbox.checked && item) {
                            item.classList.add('selected');
                        }
                    });
                    
                    // Search functionality
                    if(searchInput && studentItems.length > 0) {
                        searchInput.addEventListener('input', function(e) {
                            const searchTerm = e.target.value.toLowerCase().trim();
                            
                            studentItems.forEach(item => {
                                const searchText = item.getAttribute('data-search') || '';
                                if(searchTerm === '' || searchText.includes(searchTerm)) {
                                    item.classList.remove('hidden');
                                } else {
                                    item.classList.add('hidden');
                                }
                            });
                        });
                    }
                }
            }, 100);

            const form = document.getElementById('edit-section-details');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formSectionId = parseInt(sectionId);
                const formData = new FormData(form);
                formData.append('section-id', formSectionId);

                const formButton = form.querySelector('button[type="submit"]');
                const cancelButton = form.querySelector('.btn-cancel');
                
                // Show loading state
                formButton.classList.add('loading');
                formButton.disabled = true;
                if (cancelButton) cancelButton.disabled = true;
                
                postEditSectionDetails(formData)
                .then(data=> {
                    if (data.success  && data.partialSuccess) {
                        let failMessage = data.message.join('\n- ');
                        alert(`Some updates failed \n ${failMessage}`);
                        setTimeout(()=>{
                            window.location.reload();
                        }, 1000);
                    }
                    else if(!data.success) {
                        alert(data.message);
                        // Remove loading state
                        formButton.classList.remove('loading');
                        formButton.disabled = false;
                        if (cancelButton) cancelButton.disabled = false;
                    }
                    else {
                        alert(data.message);
                        setTimeout(()=>{
                            window.location.reload();
                        }, 1000);
                    }
                })
                .catch(error=>{
                    alert(error);
                    console.log(error);
                    // Remove loading state on error
                    formButton.classList.remove('loading');
                    formButton.disabled = false;
                    if (cancelButton) cancelButton.disabled = false;
                })
            });

        });
    });
    
});
async function postEditSectionDetails(formData) {
    const response = await fetch(`../../../BackEnd/api/admin/postEditSectionDetails.php`, {
                    method: 'POST',
                    body: formData
                });
    let data; 
    try {
        data = await response.json();
    }
    catch {
        throw new Error('Invalid response');
    }
    if(!response.ok) {
        throw new Error(`HTTP error: ${response.status}`);
    }
    return data;
}