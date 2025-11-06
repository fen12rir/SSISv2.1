import{close,loadingText,modalHeader} from '../utils.js';
document.addEventListener('DOMContentLoaded', function() {
    const advisoryButton = document.querySelectorAll('.view-student-button');
    const modal = document.getElementById('student-view-modal');
    const modalContent = document.getElementById('student-modal-content');
    //Listener for each student button
    advisoryButton.forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-id');
            modal.style.display = 'block';
            modalContent.innerHTML = loadingText;
            fetch(`../../../BackEnd/templates/teacher/fetchStudentInformationModal.php?student_id=` + encodeURIComponent(studentId))
            .then(response => response.text())
            .then(data=> {
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += data;
                close(modal);
            })
            .catch(error=>{
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += error.message;
                close(modal);
            })
        });
    });
});