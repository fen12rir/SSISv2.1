export function close(modal) { // global close util
    const closeButton = modal.querySelector('.close');
    if(closeButton && !closeButton.hasAttribute('data-listener-added')) {
        closeButton.setAttribute('data-listener-added', 'true');
        closeButton.addEventListener('click', function(){
            modal.style.display = 'none';
            modal.classList.remove('show');
        });
    }
    
    // Also close on outside click
    if (!modal.hasAttribute('data-outside-listener-added')) {
        modal.setAttribute('data-outside-listener-added', 'true');
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
            }
        });
    }
}
export const loadingText = `
    <div class="loading-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem; min-height: 200px;">
        <div class="spinner" style="width: 48px; height: 48px; border: 4px solid #f3f3f3; border-top: 4px solid #3e9ec4; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <p style="margin-top: 1rem; color: #666; font-size: 1rem;">Loading...</p>
    </div>
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
`; //global loading pop up modal
export function modalHeader(backButton = false) {
    let hasBackButton = backButton === true ? '<button class="back-button"> <img src="../../assets/imgs/arrow-left-solid.svg"> </button>' : '';
    const headerContent = `<div class="modal-header">${hasBackButton}<span class="close">&times;</span> </div><br>`;   
    return headerContent;
}
export function generateOptions(data,selectContainer) {
    if(data) {
        data.forEach(data=>{
            const createOption = document.createElement('option');
            createOption.value = data.code;
            createOption.textContent = data.name;
            selectContainer.append(createOption);
        })
    }
}
export async function fetchAddress(url) {
    const TIME_OUT = 10000;
    const controller = new AbortController();
    const timeoutId = setTimeout(()=> controller.abort(),TIME_OUT);
    try {
        const response = await fetch(url,{
            signal: controller.signal
        });
        clearTimeout(timeoutId);
        let data;
        try {
            data = await response.json();
        }
        catch {
            throw new Error('Invalid response');
        }
        if(!response.ok) throw new Error(`Failed to fetch: ${url}`);
        if (!data || !Array.isArray(data) || data.length === 0) throw new Error('Data recieved are either empty or non-array');
        return data;
    }
    catch(error) {
        clearTimeout(timeoutId);
        if(error.name === 'AbortError') {
            throw new Error(`Request timeout: Server took too long to respond! Exited after ${TIME_OUT / 1000} seconds`);
        }
        throw error;
    }
}
export async function getRegions() {
    return await fetchAddress(`https://psgc.gitlab.io/api/regions/`);
}
export async function getProvinces(regionCode) {
    return await fetchAddress(`https://psgc.gitlab.io/api/regions/${regionCode}/provinces`);
}
export async function getCities(provinceCode) {
    return await fetchAddress(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities`);
}
export async function getBarangays(cityCode) {
    return await fetchAddress(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays`);
}
export function capitalizeFirstLetter(element) {
    element.addEventListener('input', function(e) {
        const value = e.target.value;
        const cursorPos = e.target.selectionStart;
        if(value.length > 0) {
            const firstChar = value.charAt(0);
            if (firstChar === firstChar.toLowerCase() && firstChar !== firstChar.toUpperCase()) {
                e.target.value = firstChar.toUpperCase() + value.slice(1);
                e.target.setSelectionRange(cursorPos, cursorPos);
            }
        }
    });
}
export const ValidationUtils = {
    emptyError: "This field is required",
    notNumber: "This field must be a number",
    isEmpty(element) {
        return !element.value.trim();
    },
    clearError(errorElement, childElement) {
        let container = childElement.parentElement.querySelector('.error-msg');
        if (!container) {
            container = childElement.closest('div').querySelector('.error-msg');
        }
        if (!container) {
            container = childElement.parentElement.parentElement.querySelector('.error-msg');
        }
        const errorSpan = container.querySelector('.' + errorElement);

        container.classList.remove('show');
        childElement.style.border = "1px solid #616161";
        if (errorSpan) {
            errorSpan.innerHTML = '';
        }
    },
    errorMessages(errorElement, message, childElement) {
        let container = childElement.parentElement.querySelector('.error-msg');
        if (!container) {
            container = childElement.closest('div').querySelector('.error-msg');
        }
        if (!container) {
            container = childElement.parentElement.parentElement.querySelector('.error-msg');
        }
        const errorSpan = container.querySelector('.' + errorElement);
        container.classList.add('show');
        childElement.style.border = "1px solid red";
        if (errorSpan) {
            errorSpan.innerHTML = message;
        }
        return false;
    },
    checkEmptyFocus(element, errorElement) {
        element.addEventListener('blur', () => this.clearError(errorElement, element));
    },
    validateEmpty(element, errorElement) {
        if(this.isEmpty(element)) {
            this.errorMessages(errorElement, this.emptyError, element);
            this.checkEmptyFocus(element, errorElement);
            return false;
        }
        this.clearError(errorElement, element);
        return true;
    },
    validationState: {
        studentInfo: true,
        parentInfo: true,
        disabledInfo: true,
        addressInfo: true,
        previousSchool: true
    },
    isFormValid() {
        return Object.values(this.validationState).every(state => state === true);
    }
};