import {ValidationUtils,capitalizeFirstLetter,generateOptions, getRegions, getProvinces, getCities, getBarangays } from "../utils.js";
document.addEventListener('DOMContentLoaded',function(){
    // |=================|
    // |===== ORDER =====|
    // |=================|
    //1ST PREVIOUS SCHOOL INFO
    //2ND STUDENT INFO
    //3RD DISABILITY
    //4TH ADDRESS
    //5TH PARENT INFO 
    //|=========================|
    //|===== HTML ELEMENTS =====|
    //|=========================|
    // === PREVIOUS SCHOOL INFORMATION (FORM 1ST PART) ===
    const startYear = document.getElementById("start-year");
    const endYear = document.getElementById("end-year");
    const lastYear = document.getElementById("last-year");
    const lschool = document.getElementById("lschool");
    const lschoolAddr = document.getElementById("lschoolAddress");
    const lschoolId = document.getElementById("lschoolID");
    const fschool = document.getElementById("fschool");
    const fschoolAddr = document.getElementById("fschoolAddress");
    const fschoolId = document.getElementById("fschoolID");
    const enrollingGradeLevel = document.getElementById("grades-tbe");
    const lastGradeLevel = document.getElementById("last-grade");
    // === STUDENT INFORMATION(FORM 2ND PART) ===
    const psaNumber = document.getElementById("PSA-number");
    const lrn = document.getElementById("LRN");
    const lname = document.getElementById("lname");
    const fname = document.getElementById("fname");
    const birthDate = document.getElementById("bday");
    const age = document.getElementById("age");
    const nativeGroup = document.getElementById("community");
    const language = document.getElementById("language");
    const religion = document.getElementById("religion");
    //=== STUDENT IF DISABLED (FORM 3RD PART) ===
    const disability = document.getElementById("boolsn");
    const assistiveTech = document.getElementById("atdevice");
    //=== STUDENT ADDRESS (FORM 4TH PART) ===
    const regions = document.getElementById("region");
    const provinces = document.getElementById("province");
    const cityOrMunicipality = document.getElementById("city-municipality");
    const barangay = document.getElementById("barangay");
    const subdivsion = document.getElementById("subdivision");
    const houseNumber = document.getElementById("house-number");
    //===STUDENT PARENTS INFORMATION (FORM 5TH PART) ===
    const fatherLname = document.getElementById("Father-Last-Name");
    const fatherFname = document.getElementById("Father-First-Name");
    const motherLname = document.getElementById("Mother-Last-Name");
    const motherFname = document.getElementById("Mother-First-Name");
    const guardianLname = document.getElementById("Guardian-Last-Name");
    const guardianFname = document.getElementById("Guardian-First-Name");
    const fatherCPnum = document.getElementById("F-number");
    const motherCPnum = document.getElementById("M-number");
    const guardianCPnum = document.getElementById("G-number");
    //===FORM && FORM BUTTON ===
    const form = document.getElementById('enrollment-form');
    const submitButton = form.querySelector('button[type="submit"]');
    // |============================|
    // |===== DECLARED VALUES ======|
    // |============================|
    // === DATES ===
    const today = new Date();
    const year = today.getFullYear();
    const minDate = new Date();
    minDate.setFullYear(today.getFullYear() - 25);
    const maxDate = new Date();
    maxDate.setFullYear(today.getFullYear() - 3);
    
    // === VALIDATION REGEX (for final validation) ===
    const nameRegex = /^[A-Za-z]+(\s[A-Za-z]+)*$/; // Only letters, single space between words
    const numberRegex = /^[0-9]+$/; // Digits only
    const addressRegex = /^[A-Za-z0-9\s,.]+$/; // Alphanumeric, space, comma, period
    
    // === LEGACY REGEX (keeping for backward compatibility) ===
    const lrnRegex = /^([0-9]){12}$/;
    const bCertRegex = /^([0-9]){13}$/;
    const yearRegex = /^(1[0-9]{3}|2[0-9]{3}|3[0-9]{3})$/;
    const idRegex = /^([0-9]){6}$/;
    const charRegex = /^[A-Za-z0-9\s.,'-]{3,100}$/;
    const onlyDigits = /^[0-9]+$/;
    
    // === INPUT FILTERING REGEX (real-time character filtering) ===
    const REGEX_PATTERNS = {
        // Names - STRICT: Only letters and spaces
        NAME: /^[A-Za-z\s]*$/,
        NAME_SINGLE_CHAR: /[A-Za-z\s]/,
        
        // Numbers only - digits only
        NUMBERS_ONLY: /^[0-9]*$/,
        NUMBER_SINGLE_CHAR: /[0-9]/,
        
        // Phone - digits only, max 11
        PHONE: /^[0-9]{0,11}$/,
        PHONE_SINGLE_CHAR: /[0-9]/,
        
        // Address - STRICT: alphanumeric, spaces, comma, period ONLY
        ADDRESS: /^[A-Za-z0-9\s,.]*$/,
        ADDRESS_SINGLE_CHAR: /[A-Za-z0-9\s,.]/,
        
        // Year - 4 digits
        YEAR: /^[0-9]{0,4}$/,
        YEAR_SINGLE_CHAR: /[0-9]/,
        
        // General text - alphanumeric, spaces, comma, period
        GENERAL_TEXT: /^[A-Za-z0-9\s,.]*$/,
        GENERAL_TEXT_SINGLE_CHAR: /[A-Za-z0-9\s,.]/,
        
        // Text only - letters and spaces only
        TEXT_ONLY: /^[A-Za-z\s]*$/,
        TEXT_ONLY_SINGLE_CHAR: /[A-Za-z\s]/
    };
    
    // |=============================|
    // |===== VALIDATION UTILS ======|
    // |=============================|
    
    /**
     * Trim and clean input based on field type
     */
    function trimInput(value, type = 'default') {
        if (!value) return '';
        
        // Remove leading/trailing whitespace
        let cleaned = value.trim();
        
        // Type-specific cleaning
        switch(type) {
            case 'name':
                // Remove multiple consecutive spaces (allow only single space)
                cleaned = cleaned.replace(/\s{2,}/g, ' ');
                // Remove leading/trailing spaces
                cleaned = cleaned.trim();
                break;
                
            case 'address':
                // Remove multiple consecutive spaces
                cleaned = cleaned.replace(/\s{2,}/g, ' ');
                // Remove multiple consecutive commas or periods
                cleaned = cleaned.replace(/[,.]{2,}/g, match => match[0]);
                // Remove leading/trailing punctuation and spaces
                cleaned = cleaned.replace(/^[,.\s]+|[,.\s]+$/g, '');
                break;
                
            case 'number':
                // Remove all non-digits
                cleaned = cleaned.replace(/\D/g, '');
                break;
                
            case 'text':
                // Remove multiple consecutive spaces
                cleaned = cleaned.replace(/\s{2,}/g, ' ');
                // Remove leading/trailing spaces
                cleaned = cleaned.trim();
                break;
                
            default:
                // Remove multiple consecutive spaces
                cleaned = cleaned.replace(/\s{2,}/g, ' ');
                break;
        }
        
        return cleaned;
    }
    
    /**
     * Filter input in real-time - prevents typing invalid characters
     */
    function filterInput(element, pattern, maxLength = null) {
        element.addEventListener('beforeinput', function(e) {
            // Allow special keys (backspace, delete, arrow keys, etc.)
            if (e.inputType === 'deleteContentBackward' || 
                e.inputType === 'deleteContentForward' ||
                e.inputType === 'deleteByCut') {
                return;
            }
            
            // Check if input type is text insertion
            if (e.inputType === 'insertText' || e.inputType === 'insertFromPaste') {
                const newChar = e.data;
                
                if (!newChar) return;
                
                // Check against single character pattern
                if (pattern && !pattern.test(newChar)) {
                    e.preventDefault();
                    return;
                }
                
                // Check max length
                if (maxLength) {
                    const currentLength = element.value.length;
                    const selectionLength = element.selectionEnd - element.selectionStart;
                    const newLength = currentLength - selectionLength + newChar.length;
                    
                    if (newLength > maxLength) {
                        e.preventDefault();
                        return;
                    }
                }
            }
        });
        
        // Additional paste protection
        element.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const filteredText = pastedText.split('').filter(char => pattern.test(char)).join('');
            
            if (maxLength && filteredText.length > maxLength) {
                return;
            }
            
            // Insert filtered text
            const start = element.selectionStart;
            const end = element.selectionEnd;
            const currentValue = element.value;
            const newValue = currentValue.substring(0, start) + filteredText + currentValue.substring(end);
            
            if (!maxLength || newValue.length <= maxLength) {
                element.value = newValue;
                element.setSelectionRange(start + filteredText.length, start + filteredText.length);
                element.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    }
    
    /**
     * Apply name filtering and trimming - STRICT: letters and single space only
     */
    function applyNameFilter(element) {
        filterInput(element, REGEX_PATTERNS.NAME_SINGLE_CHAR);
        
        element.addEventListener('blur', function() {
            this.value = trimInput(this.value, 'name');
            // Additional validation: ensure only single space between words
            if (this.value) {
                this.value = this.value.replace(/\s+/g, ' ').trim();
            }
        });
        
        element.addEventListener('input', function() {
            // Real-time pattern validation - only letters and spaces
            if (this.value && !REGEX_PATTERNS.NAME.test(this.value)) {
                this.value = this.value.split('').filter(char => 
                    REGEX_PATTERNS.NAME_SINGLE_CHAR.test(char)
                ).join('');
            }
            // Prevent multiple consecutive spaces while typing
            this.value = this.value.replace(/\s{2,}/g, ' ');
        });
    }
    
    /**
     * Apply number filtering and trimming
     */
    function applyNumberFilter(element, maxLength = null) {
        filterInput(element, REGEX_PATTERNS.NUMBER_SINGLE_CHAR, maxLength);
        
        element.addEventListener('blur', function() {
            this.value = trimInput(this.value, 'number');
        });
        
        element.addEventListener('input', function() {
            // Ensure only digits
            this.value = this.value.replace(/\D/g, '');
            
            // Enforce max length
            if (maxLength && this.value.length > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
        });
    }
    
    /**
     * Apply phone number filtering
     */
    function applyPhoneFilter(element) {
        filterInput(element, REGEX_PATTERNS.PHONE_SINGLE_CHAR, 11);
        
        element.addEventListener('blur', function() {
            this.value = trimInput(this.value, 'number');
        });
        
        element.addEventListener('input', function() {
            // Ensure only digits and max 11
            this.value = this.value.replace(/\D/g, '').substring(0, 11);
        });
    }
    
    /**
     * Apply address filtering and trimming - STRICT: alphanumeric, space, comma, period only
     */
    function applyAddressFilter(element) {
        filterInput(element, REGEX_PATTERNS.ADDRESS_SINGLE_CHAR);
        
        element.addEventListener('blur', function() {
            this.value = trimInput(this.value, 'address');
        });
        
        element.addEventListener('input', function() {
            // Real-time pattern validation - only alphanumeric, space, comma, period
            if (this.value && !REGEX_PATTERNS.ADDRESS.test(this.value)) {
                this.value = this.value.split('').filter(char => 
                    REGEX_PATTERNS.ADDRESS_SINGLE_CHAR.test(char)
                ).join('');
            }
            // Prevent multiple consecutive spaces
            this.value = this.value.replace(/\s{2,}/g, ' ');
        });
    }
    
    /**
     * Apply text-only filtering (for religion, language, etc.) - STRICT: letters and spaces only
     */
    function applyTextFilter(element) {
        filterInput(element, REGEX_PATTERNS.TEXT_ONLY_SINGLE_CHAR);
        
        element.addEventListener('blur', function() {
            this.value = trimInput(this.value, 'text');
        });
        
        element.addEventListener('input', function() {
            // Real-time pattern validation - only letters and spaces
            if (this.value && !REGEX_PATTERNS.TEXT_ONLY.test(this.value)) {
                this.value = this.value.split('').filter(char => 
                    REGEX_PATTERNS.TEXT_ONLY_SINGLE_CHAR.test(char)
                ).join('');
            }
            // Prevent multiple consecutive spaces
            this.value = this.value.replace(/\s{2,}/g, ' ');
        });
    }
    
    /**
     * Apply year filtering
     */
    function applyYearFilter(element) {
        filterInput(element, REGEX_PATTERNS.YEAR_SINGLE_CHAR, 4);
        
        element.addEventListener('input', function() {
            // Ensure only digits and max 4
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
        });
    }
    
    /**
     * Apply general text filtering (disability, assistive tech)
     */
    function applyGeneralTextFilter(element) {
        filterInput(element, REGEX_PATTERNS.GENERAL_TEXT_SINGLE_CHAR);
        
        element.addEventListener('blur', function() {
            this.value = trimInput(this.value, 'text');
        });
        
        element.addEventListener('input', function() {
            if (this.value && !REGEX_PATTERNS.GENERAL_TEXT.test(this.value)) {
                this.value = this.value.split('').filter(char => 
                    REGEX_PATTERNS.GENERAL_TEXT_SINGLE_CHAR.test(char)
                ).join('');
            }
            this.value = this.value.replace(/\s{2,}/g, ' ');
        });
    }

    function initialSelectValue(selectElement, parentElement) {
        selectElement.innerHTML = `<option value=""> Select a ${parentElement} first </option>`;
    }
    async function replaceTextBox(replaceElement, addressType) {
        let createTBox = document.createElement("input");
        createTBox.type = "text";
        createTBox.id = addressType;
        createTBox.placeholder = `Enter ${addressType} manually`;
        createTBox.className = "textbox";
        replaceElement.replaceWith(createTBox);
    }
    async function changeAddressValues() {
        try {
            const addressData = {
                region: { code: '', text: '' },
                province: { code: '', text: '' },
                city: { code: '', text: '' },
                barangay: { code: '', text: '' }
            };
            if(regions.value && regions.selectedIndex !== -1) {
                addressData.region.code = regions.value;
                addressData.region.text = regions.options[regions.selectedIndex].text;
            }            
            if(provinces.value && provinces.selectedIndex !== -1) {
                addressData.province.code = provinces.value;
                addressData.province.text = provinces.options[provinces.selectedIndex].text;
            }
            if(cityOrMunicipality.value && cityOrMunicipality.selectedIndex !== -1) {
                addressData.city.code = cityOrMunicipality.value;
                addressData.city.text = cityOrMunicipality.options[cityOrMunicipality.selectedIndex].text;
            }
            if(barangay.value && barangay.selectedIndex !== -1) {
                addressData.barangay.code = barangay.value;
                addressData.barangay.text = barangay.options[barangay.selectedIndex].text;
            }
            Object.entries(addressData).forEach(([key, value]) => {
                let codeInput = form.querySelector(`input[name="${key}_code"]`);
                if (!codeInput) {
                    codeInput = document.createElement('input');
                    codeInput.type = 'hidden';
                    codeInput.name = `${key}_code`;
                    form.appendChild(codeInput);
                }
                codeInput.value = value.code;
                let textInput = form.querySelector(`input[name="${key}_text"]`);
                if (!textInput) {
                    textInput = document.createElement('input');
                    textInput.type = 'hidden';
                    textInput.name = `${key}_text`;
                    form.appendChild(textInput);
                }
                textInput.value = value.text;
            });
            return addressData;
        } catch(error) {
            console.error('Error in changeAddressValues:', error);
            return null;
        }
    }
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    function checkIfNumericInput(element) {
        element.addEventListener('beforeinput', function(e) {
            if (e.inputType === 'insertText' && /\D/.test(e.data)) {
                e.preventDefault(); // stop invalid character before it appears
                return;
            }
        });
    }
    // |========================|
    // |===== VALIDATIONS ======|
    // |========================|
    // === ENROLLING AND LAST GRADE LEVEL SCHOOL VALIDATION + INIT ===
    // lgl = egl - 1 && egl = lgl+ 1
    function syncSelects(currentSelect,otherSelect) { // OK
        const selectedIndex = currentSelect.selectedIndex;
        if(currentSelect.id === 'grades-tbe') {
            const prevIndex = selectedIndex - 1;
            if(selectedIndex === 0) {
                otherSelect.selectedIndex = 0;
            }
            if(prevIndex >= 0) {
                otherSelect.selectedIndex = prevIndex;
            }
        }
        else {
            if(selectedIndex === 0) {
                otherSelect.selectedIndex = 0;
            }
            else {
                const nextIndex = selectedIndex + 1;
                if(nextIndex === 1) {
                    currentSelect.disabled = true;
                    lastGradeLevel.style.opacity = 0.5;
                    lastGradeLevel.value = '';
                    ValidationUtils.clearError('em-last-grade-level',lastGradeLevel);
                }
                if(nextIndex < otherSelect.options.length) {
                    otherSelect.selectedIndex = nextIndex;
                }
            }
        }
    }
    function toggleEnrollingGradeLevelRelatedDisables() { // OK
        const selectedIndex = enrollingGradeLevel.selectedIndex;
        if(selectedIndex === 1) {
            //DISABLE LAST GRADE LEVEL
            lastGradeLevel.disabled = true;
            lastGradeLevel.style.opacity = 0.5;
            lastGradeLevel.value = '';
            ValidationUtils.clearError('em-last-grade-level',lastGradeLevel);
            //DISABLE LAST YEAR ATTENDED
            lastYear.disabled = true;
            lastYear.style.opacity = 0.5;
            lastYear.value = '';
            ValidationUtils.clearError('em-last-year-finished',lastYear);
            lastYear.removeEventListener('input', validateYearFinished);
        }
        else {
            lastGradeLevel.disabled = false;
            lastGradeLevel.style.opacity = 1;
            //DISABLE LAST YEAR ATTENDED
            lastYear.disabled = false;
            lastYear.style.opacity = 1;
            lastYear.value = saveYear;
            if(saveYear && saveYear.trim() !== '') {
                lastYear.dispatchEvent(new Event('input'));
            }
        }
    }
    function validateEnrollingLevel() { //OK
        const enrollingLevelSelected = enrollingGradeLevel.selectedIndex
        if( enrollingLevelSelected === 0) {
            return ValidationUtils.errorMessages('em-enrolling-grade-level', 'Please select an enrolling grade level', enrollingGradeLevel);
        }
        ValidationUtils.clearError('em-enrolling-grade-level',enrollingGradeLevel);
        return true;
    }
    function validateLastGradeLevel() { //OK
        if (lastGradeLevel.disabled) return true;
        const lastGradeLevelSelected = lastGradeLevel.selectedIndex;
        if(lastGradeLevelSelected === lastGradeLevel.options.length - 1) {
            return ValidationUtils.errorMessages("em-last-grade-level", 'This is already the last grade level possible', lastGradeLevel);
        }
        ValidationUtils.clearError('em-last-grade-level', lastGradeLevel);
        return true;
    }
    function validateEnrollingAndLastGradeLevel() { // OK
        if(!validateLastGradeLevel()) {
            return false;
        }
        const lastGradeLevelSelected = lastGradeLevel.selectedIndex;
        const enrollingGradeLevelSelected = enrollingGradeLevel.selectedIndex;
        if(lastGradeLevelSelected > enrollingGradeLevelSelected) {
            return ValidationUtils.errorMessages("em-last-grade-level", 'Last grade level cannot be greater than enrolling grade level', lastGradeLevel);
        }
        ValidationUtils.clearError('em-last-grade-level',lastGradeLevel);
        return true;
    }
    function validateStartYear() {
        let startYearVal = parseInt(startYear.value);
        let endYearVal = parseInt(endYear.value);
        if(ValidationUtils.isEmpty(startYear)) {
            return ValidationUtils.errorMessages("em-start-year", ValidationUtils.emptyError, startYear);
        }
        if (!yearRegex.test(startYear.value)) {
            return ValidationUtils.errorMessages("em-start-year", "Enter a valid year", startYear);
        }
        if (startYearVal == endYearVal) {
            return ValidationUtils.errorMessages("em-start-year", "Academic year cannot be equal", startYear);
        }
        if(startYearVal < year) {
            return ValidationUtils.errorMessages("em-start-year", "Year is lower than the current year", startYear);
        }
        if(startYearVal > endYearVal) {
            return ValidationUtils.errorMessages("em-start-year", "Starting year cannot be greater than the end year", startYear);
        }
        ValidationUtils.clearError("em-start-year", startYear);
        return true;
    }
    function validateAcademicYear(){
        const endYearVal = parseInt(endYear.value);
        const startYearVal = parseInt(startYear.value);
        if(!validateStartYear()) {
            return false;
        }
        if (ValidationUtils.isEmpty(endYear)) {
            return ValidationUtils.errorMessages("em-start-year", ValidationUtils.emptyError, endYear);
        }
        else if (!yearRegex.test(endYear.value) ) {
            return ValidationUtils.errorMessages("em-start-year", "Enter a valid year", endYear);
        }
        else if (endYearVal == startYearVal) {
            return ValidationUtils.errorMessages("em-start-year", "Academic year cannot be equal", endYear);
        }     
        else if(endYearVal < startYearVal) {
            return ValidationUtils.errorMessages("em-start-year", "End year cannot be lower than the starting year", endYear);
        }
        else if(endYearVal != startYearVal + 1) {
            return ValidationUtils.errorMessages("em-start-year", "Academic year should be 1 year apart", endYear);
        }
        ValidationUtils.clearError("em-start-year", endYear);
        return true;
    }
    function validateYearFinished(){
        const lastYearVal = parseInt(lastYear.value);
            
        if (ValidationUtils.isEmpty(lastYear)) {
            return ValidationUtils.errorMessages("em-last-year-finished", ValidationUtils.emptyError, lastYear);
        }
        else if (!yearRegex.test(lastYear.value)){
            return ValidationUtils.errorMessages("em-last-year-finished", "Enter a valid year", lastYear);
        }
        else if (lastYearVal > year) {
            return ValidationUtils.errorMessages("em-last-year-finished", "Value cannot be greater than the current year", lastYear);
        }
        else if (lastYearVal < 1950) {
            return ValidationUtils.errorMessages("em-last-year-finished", "Year is too low", lastYear);
        }
        ValidationUtils.clearError("em-last-year-finished", lastYear);
        return true;
    }
    function validateSchoolId(element, errorElement) {
        if (ValidationUtils.isEmpty(element)) {
            return ValidationUtils.errorMessages(errorElement, ValidationUtils.emptyError, element);
        }
        else if(!idRegex.test(element.value)) {
            return ValidationUtils.errorMessages(errorElement, "Not a valid school Id", element);
        }
        else if(!onlyDigits.test(element.value)) {
            return ValidationUtils.errorMessages(errorElement, "Contains non numeric characters", element);
        }
        ValidationUtils.clearError(errorElement, element);
        return true;
    }
    function validateSchool(element, errorElement){
        if (ValidationUtils.isEmpty(element)) { 
            return ValidationUtils.errorMessages(errorElement, ValidationUtils.emptyError, element);
        }
        else if (!charRegex.test(element.value)) {
            return ValidationUtils.errorMessages(errorElement, "Enter 3 or more characters", element);
        }
        ValidationUtils.clearError(errorElement, element);
        return true;
    }
    function validatePreviousSchoolInfo() {
        let isValid = true;
        const fields = [
            {element: lschool, error: "em-lschool"},
            {element: lschoolAddr, error: "em-lschoolAddress"},
            {element: fschool, error: "em-fschool"},
            {element: fschoolAddr, error: "em-fschoolAddress"}
        ];
        fields.forEach(({element, error}) => {
            if (!validateSchool(element, error)) {
                isValid = false;
            }
        });
        const idFields = [
            {element: lschoolId, error: "em-lschoolID"},
            {element: fschoolId, error: "em-fschoolID"}
        ];
        idFields.forEach(({element, error}) => {
            if (!validateSchoolId(element, error)) {
                isValid = false;
            }
        });
        if (!validateStartYear()) isValid = false;
        if (!validateAcademicYear()) isValid = false;
        if (!lastYear.disabled && !validateYearFinished()) isValid = false;
        if(!validateEnrollingAndLastGradeLevel()) isValid = false;
        ValidationUtils.validationState.previousSchool = isValid;
        return isValid;
    }
     // === STUDENT INFO VALIDATION ===
    function validateAge(ageValue) {
        const minAge = 3;
        const maxAge = 25;
        if (isNaN(ageValue)) {
            return ValidationUtils.errorMessages("em-age", "Age must be a number", age);
        }
        
        if (ageValue < minAge) {
            return ValidationUtils.errorMessages("em-age", "Student must be at least 3 years old", age);
        }
        
        if (ageValue > maxAge) {
            return ValidationUtils.errorMessages("em-age", "Student must be 25 years old or younger", age);
        }
        ValidationUtils.clearError("em-age", age);
        return true;
    }
    function getAge() {
        let currentYear = today.getFullYear();
        let currentMonth = today.getMonth()+1;
        let currentDay = today.getDate(); 
        let bday = birthDate.value;
        if (!bday) {
            ValidationUtils.errorMessages("em-bday", "Please select a birth date", birthDate);
            return false;
        }
        let getDate = new Date(bday);
        
        if (getDate > today) {
            ValidationUtils.errorMessages("em-bday", "Birth date cannot be in the future", birthDate);
            return false;
        }
        let birthYear = getDate.getFullYear();
        let birthMonth = getDate.getMonth()+1;
        let birthDay = getDate.getDate();
        let ageValue = currentYear - birthYear;
        if (currentMonth < birthMonth || (currentMonth === birthMonth && currentDay < birthDay)) {
            ageValue--;
        }
        if (validateAge(ageValue)) {
            age.value = ageValue;
            ValidationUtils.clearError("em-bday", birthDate);
            return true;
        } else {
            age.value = "";
            return false;
        }
    }
    function validatePSA() {
        const value = psaNumber.value.trim();
        if (!value) {
            return ValidationUtils.errorMessages("em-PSA-number", ValidationUtils.emptyError, psaNumber);
        }
        if (!/^\d*$/.test(value)) {
            return ValidationUtils.errorMessages("em-PSA-number", ValidationUtils.notNumber, psaNumber);
        }
        if (!bCertRegex.test(value)) {
            return ValidationUtils.errorMessages("em-PSA-number", 
                value.length > 13 ? "Only 13 digits are allowed" : "Enter a valid birth certificate number", 
                psaNumber
            );
        }
        ValidationUtils.clearError("em-PSA-number", psaNumber);
        return true;
    }
    function validateLRN() {
        const value = lrn.value.trim();
        if (lrn.disabled) {
            ValidationUtils.clearError("em-LRN", lrn);
            return true;
        }
        if (!value) {
            return ValidationUtils.errorMessages("em-LRN", ValidationUtils.emptyError, lrn);
        }
        if (!/^\d*$/.test(value)) {
            return ValidationUtils.errorMessages("em-LRN", ValidationUtils.notNumber, lrn);
        }
        if (!lrnRegex.test(value)) {
            return ValidationUtils.errorMessages("em-LRN", 
                value.length > 12 ? "Only 12 digits are allowed" : "Enter a valid LRN",
                lrn
            );
        }
        ValidationUtils.clearError("em-LRN", lrn);
        return true;
    }
    function validateNativeGroup() {
        if(nativeGroup.disabled) {
            ValidationUtils.clearError('em-community', nativeGroup);
        }
        if(ValidationUtils.isEmpty(nativeGroup)) {
            return ValidationUtils.errorMessages("em-community", ValidationUtils.emptyError, nativeGroup);
        }
        ValidationUtils.clearError('em-community',nativeGroup);
        return true;
    }
    function validateStudentInfo() {
        let isValid = true;
        const requiredFields = [
            {element: lname, error: "em-lname"},
            {element: fname, error: "em-fname"},
            {element: language, error: "em-language"},
            {element: religion, error: "em-religion"}
        ];
        requiredFields.forEach(({element, error}) => {
            if (!ValidationUtils.validateEmpty(element, error)) {
                isValid = false;
            }
        });
        if (!validatePSA()) {
            isValid = false;
        }
        if (!lrn.disabled && !validateLRN()) {
            isValid = false;
        }
        if(!nativeGroup.disabled && !validateNativeGroup()) { 
            isValid = false;
        }
        if (!validateAge(parseInt(age.value)) || !birthDate.value || !getAge()) {
            isValid = false;
        }
        ValidationUtils.validationState.studentInfo = isValid;
        return isValid;
    }
    // === DISABLED STUDENT VALIDATION ===
    function validateDisabilities(element, errorElement) {
        if(ValidationUtils.isEmpty(element)) {
            return ValidationUtils.errorMessages(errorElement, ValidationUtils.emptyError, element);
        }
        ValidationUtils.clearError(errorElement,element);
        return true;
    }
    function validateDisabilityInfo() {
        let isValid = true;
        if(disability.disabled && assistiveTech.disabled) return isValid;
        const disabiltiyFields = [
            {element: disability, error: "em-boolsn" },
            {element: assistiveTech, error: "em-atdevice" }
        ];
        disabiltiyFields.forEach((element,error)=>{
            if(!element.disabled) return;
            if(!ValidationUtils.validateEmpty(element,error)) {
                isValid = false;
            }
        });
        ValidationUtils.validationState.disabledInfo = isValid;
        return isValid;
    }
    // === ADDRESS VALIDATION ===
    function validateAddressInfo() {
        let isValid = true;
        const addressFields = [
            { element: regions, error: "em-region", label: "Region" },
            { element: provinces, error: "em-province", label: "Province" },
            { element: cityOrMunicipality, error: "em-city", label: "City/Municipality" },
            { element: barangay, error: "em-barangay", label: "Barangay" },
            { element: subdivsion, error: "em-subdivision", label: "Subdivision/Street" },
            { element: houseNumber, error: "em-house-number", label: "House Number" }
        ];
        addressFields.forEach(({ element, error, label }) => {
            if (!element) return;
            if (element.tagName === "SELECT") {
                if (!element.value) {
                    ValidationUtils.errorMessages(error, `Please select a ${label}`, element);
                    isValid = false;
                } else {
                    ValidationUtils.clearError(error, element);
                }
            }
            else if (element.tagName === "INPUT") {
                if (ValidationUtils.isEmpty(element)) {
                    ValidationUtils.errorMessages(error, ValidationUtils.emptyError, element);
                    isValid = false;
                } else {
                    ValidationUtils.clearError(error, element);
                }
                if (element === houseNumber && !ValidationUtils.isEmpty(element)) {
                    if (isNaN(element.value)) {
                        ValidationUtils.errorMessages(error, ValidationUtils.notNumber, element);
                        isValid = false;
                    }
                }
            }
        });
        ValidationUtils.validationState.addressInfo = isValid;
        return isValid;
    }
    // === PARENT INFO VALIDATION ===
    function validatePhoneNumber(element, errorElement) {
        if(ValidationUtils.isEmpty(element)) {
            ValidationUtils.errorMessages(errorElement, ValidationUtils.emptyError, element);
            return false;
        }
        if(element.value.length > 11 || element.value.length < 11 || element.value.charAt(0) !== '0') {
            ValidationUtils.errorMessages(errorElement, "Not a valid phone number", element);
            return false;
        }
        ValidationUtils.clearError(errorElement, element);
        return true;
    }
    function validateParentInfo() {
        let isValid = true;
        const allInfo = [
            {element: fatherLname, error: "em-father-last-name"},
            {element: fatherFname, error: "em-father-first-name"},
            {element: motherLname, error: "em-mother-last-name"},
            {element: motherFname, error: "em-mother-first-name"},
            {element: guardianLname, error: "em-guardian-last-name"},
            {element: guardianFname, error: "em-guardian-first-name"}
        ];
        allInfo.forEach(({element, error}) => {
            if (!ValidationUtils.validateEmpty(element, error)) {
                isValid = false;
            }
        });
        const phoneInfo = [
            {element: fatherCPnum, error: "em-f-number"},
            {element: motherCPnum, error: "em-m-number"},   
            {element: guardianCPnum, error: "em-g-number"}
        ];
        phoneInfo.forEach(({element, error}) => {
            if (!validatePhoneNumber(element, error)) {
                isValid = false;
            }
        });
        ValidationUtils.validationState.parentInfo = isValid;
        return isValid;
    }
    // |============================|
    // |===== INITIALIZATIONS ======|
    // |============================|
    // === APPLY INPUT FILTERS TO ALL FIELDS ===
    
    // Name fields - student
    const studentNameFields = [lname, fname, nativeGroup];
    studentNameFields.forEach(field => {
        if (field) applyNameFilter(field);
    });
    
    // Name fields - parents/guardians
    const parentNameFields = [fatherLname, fatherFname, motherLname, motherFname, guardianLname, guardianFname];
    parentNameFields.forEach(field => {
        if (field) applyNameFilter(field);
    });
    
    // Middle names and extensions (optional fields) - also use name filter
    const optionalNameFields = document.querySelectorAll('#mname, #extension, #Father-Middle-Name, #Mother-Middle-Name, #Guardian-Middle-Name');
    optionalNameFields.forEach(field => {
        if (field) applyNameFilter(field);
    });
    
    // Phone numbers (exactly 11 digits)
    const phoneFieldsList = [fatherCPnum, motherCPnum, guardianCPnum];
    phoneFieldsList.forEach(field => {
        if (field) applyPhoneFilter(field);
    });
    
    // Number fields with specific lengths
    if (psaNumber) applyNumberFilter(psaNumber, 13);  // PSA: 13 digits
    if (lrn) applyNumberFilter(lrn, 12);              // LRN: 12 digits
    if (lschoolId) applyNumberFilter(lschoolId, 6);   // School ID: 6 digits
    if (fschoolId) applyNumberFilter(fschoolId, 6);   // School ID: 6 digits
    
    // Year fields (4 digits)
    const yearFieldsList = [startYear, endYear, lastYear];
    yearFieldsList.forEach(field => {
        if (field) applyYearFilter(field);
    });
    
    // Make start year and end year readonly
    if (startYear) {
        startYear.readOnly = true;
        startYear.style.backgroundColor = '#f0f0f0';
        startYear.style.cursor = 'not-allowed';
    }
    if (endYear) {
        endYear.readOnly = true;
        endYear.style.backgroundColor = '#f0f0f0';
        endYear.style.cursor = 'not-allowed';
    }
    
    // Address fields (alphanumeric + space, comma, period)
    const addressFieldsList = [lschoolAddr, fschoolAddr, subdivsion];
    addressFieldsList.forEach(field => {
        if (field) applyAddressFilter(field);
    });
    
    // School names (treated as address - alphanumeric + punctuation)
    const schoolNameFields = [lschool, fschool];
    schoolNameFields.forEach(field => {
        if (field) applyAddressFilter(field);
    });
    
    // Text-only fields (religion, language - letters only)
    const textOnlyFieldsList = [religion, language];
    textOnlyFieldsList.forEach(field => {
        if (field) applyTextFilter(field);
    });
    
    // House number (numbers only)
    if (houseNumber) {
        applyNumberFilter(houseNumber);
    }
    
    // Disability and assistive tech fields (general text)
    const generalTextFieldsList = [disability, assistiveTech];
    generalTextFieldsList.forEach(field => {
        if (field) applyGeneralTextFilter(field);
    });
    
    // === PREVIOUS SCHOOL INFO INIT ===
    // INIT pampublikong paaralan radio button
    const publicSchool = document.getElementById('public');
    if(publicSchool) {
        publicSchool.checked = true;
    }
    const radios = document.querySelectorAll('input[name="bool-LRN"]');
    // INIT lrn radio button to YES
    if(![...radios].some(r=>r.checked)) {
        const defaultVal = [...radios].find(r=>r.value === '1');
        if(defaultVal) {
            defaultVal.checked = true;
            defaultVal.dispatchEvent(new Event('change'));
        }
    }
    // INIT SELECT VALUES
    if (lastGradeLevel && enrollingGradeLevel) {
        let saveOptions = lastGradeLevel.innerHTML;
        if (lastGradeLevel.options.length === 0  || enrollingGradeLevel.options.length === 0) {//fallback values
            const gradeOptions = `
                <option value=""> Select a grade level</option>
                <option value="1">Kinder I</option>
                <option value="2">Kinder II</option>
                <option value="3">Grade 1</option>       
                <option value="4">Grade 2</option>
                <option value="5">Grade 3</option>
                <option value="6">Grade 4</option>
                <option value="7">Grade 5</option>
                <option value="8">Grade 6</option>
            `;
            enrollingGradeLevel.innerHTML = gradeOptions;
            lastGradeLevel.innerHTML = gradeOptions;
        }
        if(enrollingGradeLevel.selectedIndex === 1) {//disable if lastGradeLevel if index equals to 1(Kinder I)
            lastGradeLevel.disabled = true;
            lastGradeLevel.style.opacity = 0.5;
            lastGradeLevel.textContent = '';
        }
        else {
            lastGradeLevel.disabled = false;
            lastGradeLevel.style.opacity = 1;
            lastGradeLevel.innerHTML = saveOptions;
        }
    }
    if (startYear && endYear) {
        startYear.value = year;
        endYear.value = year + 1;
    }
    // === STUDENT INFO INIT ===
    // Set date constraints
    if (birthDate) {
        birthDate.max = formatDate(maxDate);
        birthDate.min = formatDate(minDate);
    }
    //  INIT gender to babae
    const female = document.getElementById('female');
    if(female) {
        female.checked = true;
    }
    //  INIT kabilang sa katutubong grupo to yes
    const isEthnic = document.getElementById('is-ethnic');
    if(isEthnic) {
        isEthnic.checked = true;
    }  
    // === ADDRESS INIT ===
    let regionCode = "";
    let provinceCode = "";
    let cityCode = "";
    (async() =>{
        try {
            const controller = new AbortController();
            const timeOut = setTimeout(() => {
                controller.abort();
                console.error("Request timed out");
                replaceTextBox(regions, "region");
            }, 10000);
            const response = await getRegions();
            clearTimeout(timeOut);
            regions.innerHTML = `<option value=""> Select a Region </option>`;
            generateOptions(response,regions);
        }
        catch(error) {
            console.error("Error fetching regions:", error.message);
            replaceTextBox(regions, "region");
        }
        initialSelectValue(provinces, "Region");
        initialSelectValue(cityOrMunicipality, "Province");
        initialSelectValue(barangay, "City/Municipality");
    })();
    async function getProvinceOptions() {
        try {
            regionCode = regions.value;
            if (!regionCode) {
                initialSelectValue(provinces, "Region");
                return;
            }
            const data = await getProvinces(regionCode);
            provinces.innerHTML = `<option value=""> Select a Province</option>`;
            generateOptions(data,provinces);
        }
        catch (error) {
            console.error("Error fetching provinces:", error);
            if (regions.value !== "") {
                replaceTextBox(provinces, "province");
            }
        }
    }
    async function getCityOptions() {
        try {
            provinceCode = provinces.value;
            if (!provinceCode) {
                initialSelectValue(cityOrMunicipality, "Province");
                return;
            }
            const data = await getCities(provinceCode);
            cityOrMunicipality.innerHTML = `<option value=""> Select a City/Municipality</option>`;
            generateOptions(data,cityOrMunicipality);
        }
        catch (error) {
            console.error("Error fetching cities:", error);
            if (provinces.value !== "") {
                replaceTextBox(cityOrMunicipality, "city");
            } 
        }
    }
    async function getBarangayOptions() {
        try {
            cityCode = cityOrMunicipality.value;
            if (!cityCode) {
                initialSelectValue(barangay, "City/Municipality");
                return;
            }
            const data = await getBarangays(cityCode);
            barangay.innerHTML = `<option value=""> Select a Barangay</option>`;
            generateOptions(data,barangay);
        }
        catch(error) {
            console.error("Error fetching barangays:", error);
            if (cityOrMunicipality.value !== "") {
                replaceTextBox(barangay, "barangay");
            }
        }
    }
    // === DISABLED STUDENT INIT ===
    const isDisabled = document.getElementById('is-disabled');
    if(isDisabled) isDisabled.checked = true;
    const hasAssistiveTech = document.getElementById('has-assistive-tech');
    if(hasAssistiveTech) hasAssistiveTech.checked = true; 
    // === PARENT INFO INIT ===
    const not4ps = document.getElementById('not-4ps');
    if(not4ps) {
        not4ps.checked = true;
    } 
    // |===================|
    // |===== EVENTS ======|
    // |===================|
    // === PREVIOUS SCHOOL EVENTS ===
    const schoolFields = [
        {element: lschool, error: "em-lschool"},
        {element: lschoolAddr, error: "em-lschoolAddress"},
        {element: fschool, error: "em-fschool"},
        {element: fschoolAddr, error: "em-fschoolAddress"}
    ];
    schoolFields.forEach(({element, error}) => {
        if (element) {
            // Filter already applied in initialization
            element.addEventListener('input', () => validateSchool(element, error));
        }
    });
    
    const idFields = [
        {element: lschoolId, error: "em-lschoolID"},
        {element: fschoolId, error: "em-fschoolID"}
    ];
    idFields.forEach(({element, error}) => {
        if (element) {
            // Filter already applied, just add validation
            element.addEventListener('input', () => validateSchoolId(element, error));
        }
    });
    
    // Year fields - filters already applied
    // Note: startYear and endYear are readonly, so no user input validation needed
    if (startYear) startYear.addEventListener('input', validateStartYear);
    if (endYear) endYear.addEventListener('input', validateAcademicYear);
    
    //VARIABLE FOR SAVING VALUE
    let saveYear = null;
    if (lastYear) {
       lastYear.addEventListener('input',function(){
        if(!this.disabled) {
            saveYear = this.value;
            validateYearFinished();
        }
       }) 
    }
    enrollingGradeLevel.addEventListener('change', function() {
        toggleEnrollingGradeLevelRelatedDisables();
        if(!lastGradeLevel.disabled) syncSelects(this,lastGradeLevel);
        validateEnrollingLevel();
        validateEnrollingAndLastGradeLevel();
    });
    lastGradeLevel.addEventListener('change', function() {
        if(this.disabled) return;
        syncSelects(this,enrollingGradeLevel);
        validateLastGradeLevel();
        validateEnrollingAndLastGradeLevel(); 
    });
    // === STUDENT INFO EVENTS ===
    let saveNativeGroup = '';
    const hasNativeGroup = document.querySelectorAll('input[name="group"].radio');
    hasNativeGroup.forEach(radio=>{
        radio.addEventListener('change',function(){
            if(!isEthnic.checked) {
                saveNativeGroup = nativeGroup.value;
                nativeGroup.disabled = true;
                nativeGroup.style.opacity = 0.5;
                nativeGroup.value = '';
                ValidationUtils.clearError('em-community', nativeGroup);
                nativeGroup.removeEventListener('input',validateNativeGroup);
            }
            else {
                nativeGroup.disabled = false;
                nativeGroup.style.opacity = 1;
                nativeGroup.value = saveNativeGroup || '';
                if (saveNativeGroup && saveNativeGroup.trim() !== '') {
                    nativeGroup.dispatchEvent(new Event('input'));
                };
            }
        })
    })
    nativeGroup.addEventListener('input', function(){
        validateNativeGroup();
    });
    if (birthDate) {
        birthDate.addEventListener('change', getAge);
    }
    
    // PSA and LRN - filters already applied
    if (psaNumber) {
        psaNumber.addEventListener('blur', validatePSA);
    }
    
    if (lrn) {
        lrn.addEventListener('blur', validateLRN);
    }
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (radio.value === "0") {
                lrn.disabled = true;
                lrn.style.opacity = "0.2";
                lrn.value ="";
                ValidationUtils.clearError("em-LRN", lrn);
            } else {
                lrn.disabled = false;
                lrn.style.opacity = "1";
                lrn.value = localStorage.getItem('lrn');
            }
        });
    });
    // === DISABLITY EVENTS ===
    function toggleField(radioChecked, field, savedValue,errorElement) {
        if (!radioChecked) {
            savedValue = field.value;
            field.disabled = true;
            field.style.opacity = 0.2;
            field.value = '';
            ValidationUtils.clearError(errorElement,field);
            field.removeEventListener('input',validateDisabilities);
        } else {
            field.disabled = false;
            field.style.opacity = 1;
            field.value = savedValue;
            if (savedValue && savedValue.trim() !== '') {
                field.dispatchEvent(new Event('input'));
            }
        }
        return savedValue;
    }
    const assistiveTechRadio = document.querySelectorAll('input[name="at"],radio');
    const disabilityRadio = document.querySelectorAll('input[name="sn"].radio');
    let saveDisability = '';
    let saveAssistiveTech = '';
    assistiveTechRadio.forEach(radio=>{
        radio.addEventListener('change',function(){
            saveAssistiveTech = toggleField(hasAssistiveTech.checked,assistiveTech,saveAssistiveTech,'em-atdevice');
        })
    })
    disabilityRadio.forEach(radio=>{
        radio.addEventListener('change',function(){
            saveDisability = toggleField(isDisabled.checked,disability,saveDisability,'em-boolsn');
        })
    })
    disability.addEventListener('input',function(){
        validateDisabilities(this,'em-boolsn');
    })
    assistiveTech.addEventListener('input',function(){
        validateDisabilities(this,'em-atdevice');
    })
    // === ADDRESS EVENTS ===
    if (regions) {
        regions.addEventListener("change", async function() {
            await getProvinceOptions();
            document.getElementById("region-name").value = regions.options[regions.selectedIndex].text;
            if (regionCode == "") {
                initialSelectValue(provinces, "Region");
            }
        });
        provinces.addEventListener("change", async function(){
            await getCityOptions();
            document.getElementById("province-name").value = provinces.options[provinces.selectedIndex].text;
            if (provinceCode == "") {
                initialSelectValue(cityOrMunicipality, "Province");
            }
        });
        cityOrMunicipality.addEventListener("change", async function() {
            await getBarangayOptions();
            document.getElementById("city-municipality-name").value = cityOrMunicipality.options[cityOrMunicipality.selectedIndex].text;
            if (cityCode == "") {
                initialSelectValue(barangay, "City/Municipality");
            }
        });
        barangay.addEventListener("change", function() {
            document.getElementById("barangay-name").value = barangay.options[barangay.selectedIndex].text;
        });
    }
    // === PARENT INFO EVENTS ===
    const parentNameFieldsList = [
        {element: fatherLname, error: "em-father-last-name"},
        {element: fatherFname, error: "em-father-first-name"},
        {element: motherLname, error: "em-mother-last-name"},
        {element: motherFname, error: "em-mother-first-name"},
        {element: guardianLname, error: "em-guardian-last-name"},
        {element: guardianFname, error: "em-guardian-first-name"}
    ];
    parentNameFieldsList.forEach(({element, error}) => {
        if (element) {
            // Filter already applied
            element.addEventListener('keyup', () => {
                ValidationUtils.validateEmpty(element, error);
            });
        }
    });
    
    const phoneFields = [
        {element: fatherCPnum, error: "em-f-number"},
        {element: motherCPnum, error: "em-m-number"},   
        {element: guardianCPnum, error: "em-g-number"}
    ];
    phoneFields.forEach(({element, error}) => {
        if (element) {
            // Filter already applied
            element.addEventListener('input',() => validatePhoneNumber(element, error));
        }
    });
    // === OTHER EVENTS ===
    document.querySelectorAll('input[type="text"]').forEach(input => {
        capitalizeFirstLetter(input);
    });
    if (!localStorage.getItem('lrn')) {
        localStorage.setItem('lrn', 900000000000);
    }
    if (!localStorage.getItem('psa')) {
        localStorage.setItem('psa', 1000000000000);
    }
    lrn.value = localStorage.getItem('lrn');
    psaNumber.value = localStorage.getItem('psa');
    // |============================|
    // |===== FORM SUBMISSION ======|
    // |============================|
    let isSubmitting = false;
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const validateAllFields = () => {
            const allInputs = form.querySelectorAll(':is(input:not([type="radio"]), select, textarea):not(:disabled):not([data-optional="true"])');
            console.log(allInputs);
            // Clear existing error states
            allInputs.forEach(input => {
                const errorContainer = input.parentElement.querySelector('.error-msg') || 
                                    input.closest('div').querySelector('.error-msg') || 
                                    input.parentElement.parentElement.querySelector('.error-msg');
                if (errorContainer) {
                    const errorClass = errorContainer.querySelector('span')?.className;
                    if (errorClass) {
                        ValidationUtils.clearError(errorClass, input);
                    }
                }
            });
            // Trigger validation events
            const events = ['blur', 'change', 'input'];
            allInputs.forEach(input => {
                if (!input.disabled) {
                    events.forEach(eventType => {
                        input.dispatchEvent(new Event(eventType, { bubbles: true }));
                    });
                }
            });
            // Call validation functions
            const studentInfoValid = validateStudentInfo();
            const parentInfoValid = validateParentInfo();
            const previousSchoolValid = validatePreviousSchoolInfo();
            const disabledInfo = validateDisabilityInfo();
            const addressInfoValid = validateAddressInfo();
            // Check for visible errors
            const errorMessages = document.querySelectorAll('.error-msg.show');   
            if (errorMessages.length > 0) {
                const firstError = errorMessages[0];
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });   
                const associatedInput = firstError.closest('div').querySelector('input, select, textarea');
                if (associatedInput) {
                    associatedInput.focus();
                }
                return false;
            }
            return studentInfoValid && parentInfoValid && previousSchoolValid && addressInfoValid && disabledInfo;
        };
        const areFieldsValid = validateAllFields();
        const isFormValid = ValidationUtils.isFormValid();
        if (!areFieldsValid || !isFormValid) {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'block';
                errorMessage.innerHTML = !areFieldsValid ? 
                    'Please fill in all required fields correctly.' : 
                    'Please ensure all sections are properly filled out.';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }
            return;
        }
        // Process address values
        try {
            const addressData = await changeAddressValues();
            if (!addressData) {
                throw new Error('Failed to process address data');
            }
        } catch (error) {
            console.error('Error processing address data:', error);
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'block';
                errorMessage.innerHTML = 'Error processing address information. Please try again.';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }
            return;
        }
        const formData = new FormData(form);
        // Show loading state
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        submitButton.disabled = true;
        submitButton.style.backgroundColor = 'gray';
        if(isSubmitting) return;
        isSubmitting = true;
        const result = await postEnrollmentForm(formData);
        if(!result.success) {
            errorMessage.style.display = 'block';
            errorMessage.innerHTML = result.message;
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 5000);
            form.reset();
            isSubmitting = false;
            submitButton.disabled = false;
            submitButton.style.backgroundColor = "#0FFCF6";
        }
        else {
            successMessage.style.display = 'block';
            successMessage.innerHTML = result.message;
            let lrnValue = localStorage.getItem('lrn');
            let psaValue = localStorage.getItem('psa');
            const currentLRN = parseInt(lrnValue);
            const currentPSA = parseInt(psaValue);
            //incerment if submission is successful
            localStorage.setItem('lrn', currentLRN + 1);
            localStorage.setItem('psa', currentPSA + 1);
            setTimeout(() => {
                window.location.href = './user_enrollees.php';
            }, 2000);
        }
    });
});
const TIME_OUT = 10000;
async function postEnrollmentForm(formData) {
    const controller = new AbortController();
    const timeoutId = setTimeout(()=> controller.abort(),TIME_OUT);
    try {
        const response = await fetch(`../../../BackEnd/api/users/postEnrollmentFormData.php`,{
            signal: controller.signal,
            method: 'POST',
            body: formData
        });
        clearTimeout(timeoutId);
        let data;
        try {
            data = await response.json();
        }
        catch{
            throw new Error('Invalid response');
        }
        if(!response.ok) {
            return {
                success: false,
                message: data.message || `HTTP error: ${response.status}`,
                data: null
            }
        };
        return data;
    }
    catch(error) {
        clearTimeout(timeoutId);
        if(error.name === "AbortError") {
            return {
                success: false,
                message: `Response timeout: Server took too long to response. Took ${TIME_OUT / 1000} seconds`,
                data: null
            };
        }
        return {
            success: false,
            message: error.message || `There was an unexpected problem`,
            data: null
        };
    }
}