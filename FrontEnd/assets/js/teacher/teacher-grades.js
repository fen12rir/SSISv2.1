import {modalHeader,loadingText,close} from '../utils.js';
document.addEventListener('DOMContentLoaded',function(){
    const gradeButton = document.querySelectorAll('#grade-button');
    const modal = document.querySelector('.modal');
    const modalContent = document.querySelector('.modal-content');
    gradeButton.forEach(button=>{
        button.addEventListener('click', async function(e){
            modal.classList.add('show');
            modal.style.display = 'flex';
            modalContent.innerHTML = modalHeader()
            modalContent.innerHTML += loadingText;
            try {
                const secSubId = e.target.getAttribute('data-id');
                const result = await fetchStudentsOfSectionSubject(secSubId);

                if(!result.success) {
                    alert(result.message);
                }
                else {
                    const table = document.createElement('table');
                    table.className = 'grade-table'; // Optional: add a class for styling
                    // Create table header
                    const thead = document.createElement('thead');
                    const headerRow = document.createElement('tr');
                    const nameHeader = document.createElement('th');
                    nameHeader.textContent = 'Student Name';
                    //4 quarters of headers
                    const firstQuarter = document.createElement('th');
                    firstQuarter.textContent = '1st Quarter';
                    const secondQuarter = document.createElement('th');
                    secondQuarter.textContent = '2nd Quarter';
                    const thirdQuarter = document.createElement('th');
                    thirdQuarter.textContent = '3rd Quarter';
                    const fourthQuarter = document.createElement('th');
                    fourthQuarter.textContent = '4th Quarter';
                    
                    headerRow.appendChild(nameHeader);
                    headerRow.appendChild(firstQuarter);
                    headerRow.appendChild(secondQuarter);
                    headerRow.appendChild(thirdQuarter);
                    headerRow.appendChild(fourthQuarter);
                    thead.appendChild(headerRow);
                    table.appendChild(thead);
                    
                    // Create table body
                    const tbody = document.createElement('tbody');
                    const students = result.data;
                    students.forEach(student => {
                        const row = document.createElement('tr');
                        
                        // Student name cell
                        const nameCell = document.createElement('td');
                        nameCell.textContent = student.Last_Name + ', ' + student.First_Name; 
                        // 4 quarter input cell
                        const existingGrades = student.grades || {};
                        
                        const firstCell = document.createElement('td');
                        const firstInput = document.createElement('input');
                        firstInput.type = 'number';
                        firstInput.name = 'student-first';
                        firstInput.value = existingGrades[1] || ''; // Pre-fill existing first quarter if available
                        firstInput.min = 0;
                        firstInput.step = 0.01;
                        firstInput.max = 100;
                        firstInput.setAttribute('data-student-id', student.Student_Id); // Store student ID for submission
                        firstCell.appendChild(firstInput);
                        
                        const secondCell = document.createElement('td');
                        const secondInput = document.createElement('input');
                        secondInput.type = 'number';
                        secondInput.name = 'student-second';
                        secondInput.value = existingGrades[2] || ''; // Pre-fill existing second quarter if available
                        secondInput.min = 0;
                        secondInput.step = 0.01;
                        secondInput.max = 100;
                        secondInput.setAttribute('data-student-id', student.Student_Id); // Store student ID for submission
                        secondCell.appendChild(secondInput);

                        const thirdCell = document.createElement('td');
                        const thirdInput = document.createElement('input');
                        thirdInput.type = 'number';
                        thirdInput.name = 'student-third';
                        thirdInput.value = existingGrades[3] || ''; // Pre-fill existing third quarter if available
                        thirdInput.min = 0;
                        thirdInput.step = 0.01;
                        thirdInput.max = 100;
                        thirdInput.setAttribute('data-student-id', student.Student_Id); // Store student ID for submission
                        thirdCell.appendChild(thirdInput);

                        const fourthCell = document.createElement('td');
                        const fourthInput = document.createElement('input');
                        fourthInput.type = 'number';
                        fourthInput.name = 'student-fourth';
                        fourthInput.value = existingGrades[4] || ''; // Pre-fill existing fourth quarter if available
                        fourthInput.min = 0;
                        fourthInput.step = 0.01;
                        fourthInput.max = 100;
                        fourthInput.setAttribute('data-student-id', student.Student_Id); // Store student ID for submission
                        fourthCell.appendChild(fourthInput);
                        // Append cells to row
                        row.appendChild(nameCell);
                        row.appendChild(firstCell);
                        row.appendChild(secondCell);
                        row.appendChild(thirdCell);
                        row.appendChild(fourthCell);
                        
                        // Append row to tbody
                        tbody.appendChild(row);
                    });
                    
                    table.appendChild(tbody);
                    
                    // Clear loading text and append table
                    modalContent.innerHTML = modalHeader();
                    modalContent.appendChild(table);
                    
                    // Add submit button with styling
                    const submitButton = document.createElement('button');
                    submitButton.textContent = 'Save Grades';
                    submitButton.type = 'button';
                    submitButton.className = 'save-grades-btn';
                    submitButton.style.cssText = 'margin-top: 1rem; padding: 0.75rem 2rem; background: #3e9ec4; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; font-weight: 600; transition: background 0.2s;';
                    modalContent.appendChild(submitButton);
                    
                    // Add event listener for save button
                    submitButton.addEventListener('click', async function() {
                        const grades = [];
                        const inputs = table.querySelectorAll('input[type="number"]');
                        
                        inputs.forEach(input => {
                            const studentId = input.getAttribute('data-student-id');
                            const name = input.name;
                            let quarter = 0;
                            
                            if (name.includes('first')) quarter = 1;
                            else if (name.includes('second')) quarter = 2;
                            else if (name.includes('third')) quarter = 3;
                            else if (name.includes('fourth')) quarter = 4;
                            
                            const gradeValue = parseFloat(input.value);
                            
                            if (!isNaN(gradeValue) && gradeValue >= 0 && gradeValue <= 100) {
                                grades.push({
                                    student_id: parseInt(studentId),
                                    quarter: quarter,
                                    grade: gradeValue
                                });
                            }
                        });
                        
                        if (grades.length === 0) {
                            Notification.show({
                                type: 'error',
                                title: 'Error',
                                message: 'Please enter at least one valid grade'
                            });
                            return;
                        }
                        
                        // Disable button and show loading
                        submitButton.disabled = true;
                        submitButton.textContent = 'Saving...';
                        submitButton.style.opacity = '0.6';
                        submitButton.style.cursor = 'not-allowed';
                        
                        Loader.show();
                        
                        try {
                            const response = await fetch('../../../BackEnd/api/teacher/postSaveGrades.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    sectionSubjectId: secSubId,
                                    grades: grades
                                })
                            });
                            
                            // Get response text first
                            const responseText = await response.text();
                            
                            // Log for debugging
                            console.log('Save Grades - Response status:', response.status);
                            console.log('Save Grades - Response text length:', responseText.length);
                            console.log('Save Grades - Response text (first 500 chars):', responseText.substring(0, 500));
                            
                            // Try to parse JSON
                            let result;
                            try {
                                result = JSON.parse(responseText);
                            } catch (parseError) {
                                console.error('JSON Parse Error:', parseError);
                                console.error('Response text:', responseText);
                                throw new Error('Invalid JSON response from server. Response: ' + responseText.substring(0, 200));
                            }
                            
                            Loader.hide();
                            
                            if (result.success) {
                                Notification.show({
                                    type: 'success',
                                    title: 'Success',
                                    message: result.message || 'Grades saved successfully'
                                });
                                modal.style.display = 'none';
                                modal.classList.remove('show');
                            } else {
                                Notification.show({
                                    type: 'error',
                                    title: 'Error',
                                    message: result.message || 'Failed to save grades'
                                });
                                submitButton.disabled = false;
                                submitButton.textContent = 'Save Grades';
                                submitButton.style.opacity = '1';
                                submitButton.style.cursor = 'pointer';
                            }
                        } catch (error) {
                            Loader.hide();
                            console.error('Save Grades Error:', error);
                            
                            let errorMessage = 'Failed to save grades';
                            if (error.message) {
                                errorMessage = error.message;
                            }
                            
                            Notification.show({
                                type: 'error',
                                title: 'Error',
                                message: errorMessage
                            });
                            
                            submitButton.disabled = false;
                            submitButton.textContent = 'Save Grades';
                            submitButton.style.opacity = '1';
                            submitButton.style.cursor = 'pointer';
                        }
                    });
                }
            }
            catch(error) {
                console.error(error);
                modalContent.innerHTML = modalHeader();
                modalContent.innerHTML += '<div class="error-message" style="padding: 1rem; color: #c33; background: #fee; border-radius: 4px; margin: 1rem 0;">' + error.message + '</div>';
            }
            close(modal);
        })
    })
});
async function fetchStudentsOfSectionSubject(secSubId) {
    try {
        const url = `../../../BackEnd/api/teacher/fetchSectionSubjectStudents.php?secSubId=${encodeURIComponent(secSubId)}`;
        console.log('Fetching from:', url);
        
        const response = await fetch(url);

        // Read response text once
        const responseText = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text length:', responseText.length);
        console.log('Response text (first 500 chars):', responseText.substring(0, 500));
        
        if(!response.ok) {
            let errorData;
            try {
                errorData = JSON.parse(responseText);
            } catch {
                return {
                    success: false,
                    message: `Server error (${response.status}): ${responseText.substring(0, 200)}`,
                    data: null
                };
            }
            return {
                success: false,
                message: errorData.message || `HTTP ERROR ${response.status}`,
                data: null
            };
        }

        // Parse JSON response
        let data;
        try {
            if (!responseText || !responseText.trim()) {
                console.error('Empty response text received');
                return {
                    success: false,
                    message: 'Empty response from server. Please check server logs.',
                    data: null
                };
            }
            data = JSON.parse(responseText);
            console.log('Parsed data:', data);
        }
        catch(parseError) {
            console.error('JSON Parse Error:', parseError);
            console.error('Response text:', responseText);
            console.error('Response text length:', responseText.length);
            return {
                success: false,
                message: `Invalid JSON response from server. Response: ${responseText.substring(0, 200)}`,
                data: null
            };
        }
        
        return data;
    }
    catch(error) {
        console.error('Fetch Error:', error);
        return {
            success: false,
            message: error.message || `There was a problem with the fetch: ${error}`,
            data: null
        };
    }
}