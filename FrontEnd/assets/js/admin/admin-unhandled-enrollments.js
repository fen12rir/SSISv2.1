import {close,loadingText,modalHeader} from '../utils.js';
document.addEventListener('DOMContentLoaded', function() {
     // Style status cells
    const rows = document.querySelectorAll('.denied-followup-row');
    rows.forEach(row => {
        const status = row.querySelector('td:nth-child(5)');
        if (status) {
            const statusValue = status.textContent.trim().toLowerCase();
            status.innerHTML = `<span class="status-cell status-${statusValue}"> ${statusValue.toUpperCase()} </span>`;
        }
    });
    //INIT MODAL
    const modal = document.getElementById('enrolleeModal');
    const modalContent = document.getElementById('enrollee-modal-content');
    // Handle view resubmission and view reason buttons
    document.addEventListener('click', async function(e) {
        if(!e.target.classList.contains('view-resubmission') && !e.target.classList.contains('view-reason')) {return};
        if (e.target.classList.contains('view-resubmission')) {
            const enrolleeId = e.target.getAttribute('data-enrollee');
            if(enrolleeId === null) return;
            modal.style.display = 'block';
            modalContent.innerHTML = loadingText;
            const result = await fetchEnrolleeInfo(enrolleeId);
            if(!result.success) {
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += result.message;
                close(modal);
            }
            else {
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += result.data;
                modalContent.innerHTML += `
                <div class="action-buttons">
                        <button data-action="enroll" class="resubmission accept-btn" data-enrollee="${enrolleeId}">Accept</button>
                        <button data-action="deny" class="resubmission deny-btn" data-enrollee="${enrolleeId}">Deny</button>
                </div>`;
                close(modal);
            }
        }
        //MODAL FOR REMARKS
        else if (e.target.classList.contains('view-reason')) {
            const enrolleeId = e.target.getAttribute('data-enrollee');
            if(enrolleeId === null) return; 
            modal.style.display = 'block';
            modalContent.innerHTML = loadingText;
            const result = await fetchEnrolleeRemarks(enrolleeId);
            if(!result.success) {
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += result.message;
                close(modal);
            }
            else {
                const enrollmentStatus = parseInt(result.data.Enrollment_Status);
                const transactionId = parseInt(result.data.Enrollment_Transaction_Id);
                let statusText = ``;
                //CUSTOMIZE BUTTON BASED ON STATUS GIVEN
                if (enrollmentStatus == 1) {
                statusText = `<div class="unhandled-buttons">
                                    <button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="enroll" class="remarks accept-btn">Finalize Enrollment</button>
                                    <button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="deny" class="remarks deny-btn">Deny</button>
                                    <button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="toFollow" class="remarks to-follow-btn">To Follow</button></div>`;    
                }
                else if (enrollmentStatus == 4) {
                    const transactionStatus = parseInt(result.data.Transaction_Status);
                    const isConsultationDisabled = transactionStatus === 2 ? 'disabled' : '';
                    const isResubmissionDisabled = (transactionStatus === 1 || transactionStatus === 3)? 'disabled' : '';
                    statusText = `<div><button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="enroll" class="remarks accept-btn ${isResubmissionDisabled}" ${isResubmissionDisabled}>Enroll</button>
                                        <button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="resubmit" class="remarks resubmission-btn ${isResubmissionDisabled}" ${isResubmissionDisabled}>Allow Resubmission</button>
                                        <button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="consult" class="remarks consultation-btn ${isConsultationDisabled}"${isConsultationDisabled}> Needs Consultation </button>
                                        </div>`;    
                }
                else if (enrollmentStatus == 2) {
                    statusText = `<div><button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="deny" class="remarks deny-btn">Finalize Denial</button>
                                        <button data-id="${transactionId}" data-enrollee="${enrolleeId}" data-action="toFollow" class="remarks to-follow-btn">To Follow</button></div>`;    
                }
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML +=`
                    <div id="modal-body">
                        <h3>Enrollee Reasons</h3>
                        ${result.data.Remarks}
                        <div class="action-buttons">
                            ${statusText}
                        </div>
                    </div>
                `;
                close(modal);
            }
        }

    });
    let isSubmitting = false;
    modal.addEventListener('click', async function(e) {
        //RETURN IF IS SUBMITTING == TRUE BEFORE SUBMISSION
        if(isSubmitting) return;
        const action = e.target.getAttribute('data-action');
        //EARLY RETURN ON NON-BUTTON CLICKS
        const validActions = ["enroll","deny","toFollow","resubmit","consult"];
        if(!validActions.includes(action)) return;
        //EARLY RETURN IF BUTTON DOES NOT EXIST
        const button = e.target;
        if(!button) return;
        //DISABLED CLICK WHEN SUBMITTING
        isSubmitting = true;
        button.disabled = true;
        button.style.backgroundColor = 'gray';
        let status, bgColor;
        //ENROLLEE AND ENROLLMENT TRANSACTION ID
        const enrolleeId = e.target.getAttribute('data-enrollee');
        const transactionId = e.target.getAttribute('data-id');
        //DIRECT ENROLLMENT STATUS OPS  
        if(action==="enroll" || action==="deny" || action==="toFollow") {
            status = {
                "enroll" : 1,
                "deny" : 2,
                "toFollow" : 4
            }[action];
            const result = await postUpdateEnrollee(status,enrolleeId);
            if(!result.success) {
                alert(result.message);
                bgColor = {
                    1 : '#4CAF50',
                    2 : '#F44336',
                    4: '#AF9A4C'
                }[status];
                isSubmitting = false;
                button.disabled = false;
                button.style.backgroundColor = bgColor;
            }
            else {
                alert(result.message);
                window.location.reload();
            }
        }
        //ENROLLMENT TRANSACTION OPS
        else if(action==="resubmit" || action==="consult"){
            status = 4;
            const transactionStatus = {
                "resubmit" : 1,
                "consult" : 2
            }[action];
            const result = await postUpdateEnrollmentTransaction(transactionStatus,transactionId,enrolleeId,status);
            if(!result.success) {
                alert(result.message);
                bgColor = {
                    1 : '#AF714C',
                    2: '#4C69AF'
                }[transactionStatus];
                isSubmitting = false;
                button.disabled = false;
                button.style.backgroundColor = bgColor;
            }
            else {
                alert(result.message);
                window.location.reload();
            }
        }
    });
});
//TIME BEFORE ABORT 10S
const TIME_OUT = 100000;
async function postUpdateEnrollmentTransaction(transactionNumber,transactionId,enrolleeId,status,) { 
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(()=>controller.abort(),TIME_OUT);
        const response = await fetch(`../../../BackEnd/api/admin/postUpdateEnrollmentTransaction.php`, {
            signal: controller.signal,
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'transaction-status': transactionNumber,
                'transaction-id' : transactionId,
                'enrollment-status' : status,
                'enrollee-id' : enrolleeId
            })
        });
        clearTimeout(timeoutId);
        let data;
        try {
            data = await response.json();
        }
        catch{
            throw new Error('Invalid Response');
        }
        if(!response.ok) {
            return {
                success: false,
                message: data.message || `HTTP ERROR: ${response.status}`,
                data: null
            };
        }
        return data;
    }
    catch(error){
        if(error.name === "AbortError") {
           return {
                success: false,
                message: `Request timeout: Server took too long to respond`,
                data: nul
           }
        }
        return {
            success: false,
            message: error.message || `Something went wrong`,
            data: null
        };
    }
}
async function postUpdateEnrollee(status, enrolleeId) {
    const TIME_OUT = 10000;
    try {
        const  controller = new AbortController();
        const timeoutId = setTimeout(()=>{controller.abort()},TIME_OUT);
        const response = await fetch(`../../../BackEnd/api/admin/postUpdateEnrollee.php`, {
            signal: controller.signal,
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'enrollment-status': status,
                'enrollee-id' : enrolleeId
            })
        });
        clearTimeout(timeoutId);
        let data;
        try {
            data = await response.json();
        }
        catch{
            throw new Error('Invalid Response');
        }
        if(!response.ok) {
            return {
                success: false,
                message: data.message || `HTTP ERROR: ${response.status}`,
                data: null
            };
        }
        return data;
    }
    catch(error){
        if(error.name === "AbortError") {
           return {
                success: false,
                message: `Request timeout: Server took too long to respond`,
                data: nul
           }
        }
        return {
            success: false,
            message: error.message || `Something went wrong`,
            data: null
        };
    }
}
async function fetchEnrolleeRemarks(enrolleeId) {
    const TIME_OUT = 10000;
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(()=> controller.abort(), TIME_OUT);
        const response = await fetch(`../../../BackEnd/api/admin/fetchEnrolleeTransactions.php?id=${encodeURIComponent(enrolleeId)}`,{
            signal: controller.signal
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
                message: data.message || `There was a problem with the response: ${response.status}`,
                data: null
            };
        }
        return data;
    }
    catch(error) {
        console.error(error.message);
        if (error.name === 'AbortError') {
            return {
                success: false,
                message: `Request timeout: Server took too long to respond`,
                data: null
            };
        }
        return {
            success: false,
            message: `An error occured. Failed to fetch`,
            data: null
        };
    }
}
async function fetchEnrolleeInfo(enrolleeId) {
    const TIME_OUT = 10000;
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(()=>controller.abort(),TIME_OUT);
        const response = await fetch(`../../../BackEnd/templates/admin/fetchEnrolleeInfo.php?id=${encodeURIComponent(enrolleeId)}`,{
            signal: controller.signal
        });
        clearTimeout(timeoutId);
        let data;
        try {
            data = await response.text();
        }
        catch{
            throw new Error('Invalid response');
        }
        if(!response.ok) {
            return {
                success: false,
                message: data.message || `There was a problem with the response: ${response.status}`,
                data: null
            };
        }
        return {
            success: true, 
            message: `Enrollee Info successfully fetched`, 
            data: data
        };
    }
    catch(error) {
        console.error(error);
        if(error.name === 'AbortError') {
            return {
                success: false,
                message: `Request timeout: Server took too long to response`,
                data: null
            };
        }
        return {success: false, message: error.message || `Something went wrong`, data: null};
    }
}