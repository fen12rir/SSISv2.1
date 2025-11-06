<?php 
require_once __DIR__ . '/../../common/getGradeLevels.php';
$getGradeLevels = new getGradeLevels();
?>

<form id="add-section-form" class="add-section-form"> 
    <div class="form-section">
        <h3 class="form-section-title">Section Information</h3>
        
        <div class="form-group">
            <label for="section-name" class="form-label">Section Name <span class="required">*</span></label>
            <input 
                type="text" 
                id="section-name" 
                name="section-name" 
                class="form-input" 
                placeholder="Enter section name (e.g., Maria, Luna, etc.)"
                required>
            <small class="form-hint">Choose a unique name for this section</small>
        </div>
        
        <div class="form-group">
            <label for="section-grade-level" class="form-label">Grade Level <span class="required">*</span></label>
            <select id="section-grade-level" name="section-grade-level" class="form-select" required>
                <option value="">-- Select Grade Level --</option>
                <?php $getGradeLevels->createSelectValues(); ?>
            </select>
            <small class="form-hint">Select the grade level for this section</small>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="document.querySelector('.modal').style.display='none'">Cancel</button>
        <button type="submit" class="btn-submit">Add Section</button>
    </div>
</form>

<style>
.add-section-form {
    padding: 1.5rem;
    color: var(--text-color, #f5f5f5);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    flex-shrink: 0;
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
</style>
