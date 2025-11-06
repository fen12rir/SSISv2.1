<?php

require_once __DIR__ . '/../../admin/controller/adminViewSectionController.php';

$controller = new adminViewSectionController();
?>
<form id="edit-section-details" class="edit-section-details-form"> 
    <?php 
        if(isset($_GET['section_id'])) $id = $_GET['section_id'];
        $teachersResponse = $controller->viewEditSectionFormTeachers($id);
        $sectionNameResponse = $controller->viewEditSectionFormSectionName($id);
        $studentsResponse = $controller->viewEditSectionFormStudents($id);
        //section name value
        $sectionName = $sectionNameResponse['success'] ? htmlspecialchars($sectionNameResponse['data']['Section_Name']) : '';
    ?>
    
    <div class="form-section">
        <h3 class="form-section-title">Section Information</h3>
        
        <div class="form-group">
            <label for="section-name" class="form-label">Section Name <span class="required">*</span></label>
            <input 
                type="text" 
                id="section-name" 
                name="section-name" 
                class="form-input" 
                value="<?php echo $sectionName; ?>" 
                placeholder="Enter section name"
                required>
        </div>
        
        <div class="form-group">
            <label for="select-adviser" class="form-label">Section Adviser <span class="required">*</span></label>
            <select id="select-adviser" name="select-adviser" class="form-select" required>
                <option value="">-- Select an Adviser --</option>
        <?php 
            if(isset($_GET['section_id'])) {
                $id = $_GET['section_id'];
                if(!$teachersResponse['success']) {
                            echo '<option value="" disabled>'.htmlspecialchars($teachersResponse['message']). '</option>';
                }
            }
            foreach($teachersResponse['data'] as $options) {
                $name = htmlspecialchars($options['Staff_Last_Name']) . ', '. htmlspecialchars($options['Staff_First_Name']) 
                        .' '. htmlspecialchars($options['Staff_Middle_Name']);
                $flagSelected = $options['isSelected'] ? 'selected' : '';
                echo '<option value="'. htmlspecialchars($options['Staff_Id']).'" '. $flagSelected.'> 
                    '.  $name .'
                    </option>';
            }
        ?>
    </select>
            <small class="form-hint">Choose a teacher to assign as the section adviser</small>
        </div>
    </div>
    
    <div class="form-section">
        <h3 class="form-section-title">Assign Students to This Section</h3>
        <p class="form-description">Select students from the grade level to add or remove from this section.</p>
        
        <div class="students-search-container">
            <input 
                type="text" 
                id="student-search" 
                class="form-input search-input" 
                placeholder="Search students by name..."
                autocomplete="off">
            <div class="search-results-count">
                <span id="selected-count">0</span> students selected
            </div>
        </div>
        
        <div class="students-list-container">
    <?php 
        if(!$studentsResponse['success']) {
                    echo '<div class="no-students-message"><p>'.htmlspecialchars($studentsResponse['message']).'</p></div>';
                } else {
                    echo '<div class="students-checkbox-list" id="students-list">';
                    foreach($studentsResponse['data'] as $checkboxes) {
                        $isChecked = $checkboxes['isChecked'] ? 'checked' : '';
                        $fullName = htmlspecialchars($checkboxes['Last_Name']) . ', ' . 
                                   htmlspecialchars($checkboxes['First_Name']) . ' ' . 
                                   htmlspecialchars($checkboxes['Middle_Name']);
                        $searchText = strtolower($fullName);
                        
                        echo '<div class="student-checkbox-item" data-search="'.$searchText.'">';
                        echo '    <label class="checkbox-label">';
                        echo '        <input type="checkbox" name="students[]" value="'.htmlspecialchars($checkboxes['Student_Id']).'" '.$isChecked.' class="student-checkbox">';
                        echo '        <span class="checkbox-text">'.$fullName.'</span>';
                        echo '    </label>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            ?>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="document.querySelector('.modal').style.display='none'">Cancel</button>
        <button type="submit" class="btn-submit">Save Changes</button>
    </div>
</form>

<style>
.edit-section-details-form {
    padding: 1.5rem;
    color: var(--text-color, #f5f5f5);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow-y: auto;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.form-section:last-of-type {
    border-bottom: none;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.form-section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--accent-color, #00b4d8);
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section-title::before {
    content: '';
    width: 4px;
    height: 20px;
    background: var(--accent-color, #00b4d8);
    border-radius: 2px;
}

.form-description {
    color: rgba(245, 245, 245, 0.7);
    font-size: 0.9rem;
    margin: -1rem 0 1rem 0;
    line-height: 1.5;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--text-color, #f5f5f5);
    font-size: 0.95rem;
}

.required {
    color: #ff5f5f;
}

.form-input,
.form-select {
    width: 100%;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: var(--text-color, #f5f5f5);
    font-size: 0.95rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--accent-color, #00b4d8);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.2);
}

.form-input::placeholder {
    color: rgba(245, 245, 245, 0.5);
}

.form-select {
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: rgba(20, 30, 48, 0.9);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23f5f5f5' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    padding-right: 2.5rem;
    color: var(--text-color, #f5f5f5);
}

.form-select option {
    background-color: rgba(20, 30, 48, 0.95);
    color: var(--text-color, #f5f5f5);
    padding: 0.5rem;
}

.form-select:hover {
    background-color: rgba(20, 30, 48, 0.95);
    border-color: rgba(0, 180, 216, 0.5);
}

.form-select:focus {
    background-color: rgba(20, 30, 48, 0.95);
}

.form-hint {
    display: block;
    margin-top: 0.5rem;
    color: rgba(245, 245, 245, 0.6);
    font-size: 0.85rem;
}

.students-search-container {
    margin-bottom: 1rem;
    flex-shrink: 0;
}

.search-input {
    margin-bottom: 0.75rem;
}

.search-results-count {
    text-align: right;
    color: rgba(245, 245, 245, 0.7);
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
    background: rgba(0, 180, 216, 0.1);
    border-radius: 6px;
    border: 1px solid rgba(0, 180, 216, 0.3);
}

.search-results-count span {
    color: var(--accent-color, #00b4d8);
    font-weight: 700;
    font-size: 1.1rem;
}

.students-list-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 0.75rem;
}

.students-checkbox-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.student-checkbox-item {
    padding: 0.75rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.student-checkbox-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(0, 180, 216, 0.3);
}

.student-checkbox-item.selected {
    background: rgba(0, 180, 216, 0.15);
    border-color: var(--accent-color, #00b4d8);
}

.student-checkbox-item.hidden {
    display: none;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    user-select: none;
    width: 100%;
}

.student-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: var(--accent-color, #00b4d8);
    flex-shrink: 0;
    margin: 0;
    position: relative;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    transition: all 0.2s ease;
}

.student-checkbox:hover {
    border-color: var(--accent-color, #00b4d8);
    background: rgba(0, 180, 216, 0.1);
}

.student-checkbox:checked {
    background: var(--accent-color, #00b4d8);
    border-color: var(--accent-color, #00b4d8);
    box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.2);
}

.student-checkbox:checked::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.checkbox-text {
    color: var(--text-color, #f5f5f5);
    font-size: 0.9rem;
    flex: 1;
    transition: color 0.2s ease;
}

.student-checkbox-item.selected .checkbox-text {
    color: var(--accent-color, #00b4d8);
    font-weight: 500;
}

.no-students-message {
    text-align: center;
    padding: 2rem;
    color: rgba(245, 245, 245, 0.6);
    font-style: italic;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.btn-cancel,
.btn-submit {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel {
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-color, #f5f5f5);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.15);
}

.btn-submit {
    background: var(--accent-color, #00b4d8);
    color: white;
    position: relative;
}

.btn-submit:hover:not(:disabled) {
    background: var(--hover-accent, #48cae4);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 180, 216, 0.3);
}

.btn-submit:disabled {
    background: rgba(0, 180, 216, 0.5);
    cursor: not-allowed;
    transform: none;
    opacity: 0.7;
}

.btn-submit.loading {
    color: transparent;
}

.btn-submit.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Scrollbar styling */
.students-list-container::-webkit-scrollbar {
    width: 8px;
}

.students-list-container::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}

.students-list-container::-webkit-scrollbar-thumb {
    background: rgba(0, 180, 216, 0.5);
    border-radius: 4px;
}

.students-list-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 180, 216, 0.7);
}
</style>

<script>
// Make function globally accessible
window.initializeStudentSelection = function() {
    const searchInput = document.getElementById('student-search');
    const studentItems = document.querySelectorAll('.student-checkbox-item');
    const selectedCount = document.getElementById('selected-count');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    
    if (!selectedCount || checkboxes.length === 0) {
        // Retry after a short delay if elements aren't ready
        setTimeout(initializeStudentSelection, 100);
        return;
    }
    
    // Update selected count
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.student-checkbox:checked').length;
        if (selectedCount) {
            selectedCount.textContent = checked;
        }
    }
    
    // Initialize selected state and count on page load
    checkboxes.forEach(checkbox => {
        const item = checkbox.closest('.student-checkbox-item');
        if (checkbox.checked) {
            if (item) item.classList.add('selected');
        }
    });
    
    // Initial count
    updateSelectedCount();
    
    // Update count on checkbox change and add visual feedback
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const item = this.closest('.student-checkbox-item');
            if (this.checked) {
                if (item) item.classList.add('selected');
            } else {
                if (item) item.classList.remove('selected');
            }
            updateSelectedCount();
        });
        
        // Also handle click on the label/item for better UX
        const label = checkbox.closest('.checkbox-label');
        const item = checkbox.closest('.student-checkbox-item');
        if (label) {
            label.addEventListener('click', function(e) {
                // Toggle checkbox if clicking on label (not the checkbox itself)
                if (e.target !== checkbox) {
                    checkbox.checked = !checkbox.checked;
                    // Trigger change event
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        }
    });
    
    // Search functionality
    if(searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            let visibleCount = 0;
            studentItems.forEach(item => {
                const searchText = item.getAttribute('data-search') || '';
                if(searchTerm === '' || searchText.includes(searchTerm)) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });
            
            // Update search results info if needed
            console.log('Search term:', searchTerm, 'Visible items:', visibleCount);
        });
    }
};

// Auto-initialize if form is already in DOM
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(window.initializeStudentSelection, 100);
        });
    } else {
        setTimeout(window.initializeStudentSelection, 100);
    }
})();
</script>