<div class="sidebar active">
            <div class="sidebar-wrapper">
                <div class="sidebar-title" id="sidebar-button-wrapper">
                    <span class="SSIS">SSIS</span>
                    <button class="menu-btn"><img src="../../assets/imgs/bar.svg" id="sidebar-toggle-button"></button>
                </div>

                <div class="menu-wrappper" id="sidebar-menu-wrapper">
                    <!--DASHBOARD-->
                    <nav>
                        <div class="menu border-100sb" id="dashboard">
                            <img src="../../assets/imgs/dashboard-logo.png" class="bi">
                            <span id="dashboard-spn" class="menu-title">
                                <a href="./admin_dashboard.php" class="admin-nav-links">Dashboard</a>
                            </span>
                        </div>

                        <!--STUDENTS-->
                        <div class="menu border-100sb" id="students">
                            <img src="../../assets/imgs/student.svg" class="bi">
                            <span id="students-spn" class="menu-title"> <a href="./admin_all_students.php" class="admin-nav-links">Students </a></span> 
                        </div>    
                        <!--SECTIONS-->
                        <div class="menu border-100sb">
                            <img src="../../assets/imgs/sections-logo.png" class="bi">
                            <span id="sections-spn" class="menu-title"> <a href="./admin_sections.php" class="admin-nav-links"> Sections </a></span>
                        </div>

                        <!--SUBJECTS-->
                        <div class="menu border-100sb">
                            <img src="../../assets/imgs/subjects-logo.png" class="bi">
                            <span id="sections-spn" class="menu-title"> <a href="./admin_subjects.php" class="admin-nav-links"> Subjects</a></span>
                        </div>
                            <ul class="subjects-ul">
                                <li>
                                    <span class="sub-nav"> View Subject Details</span>
                                </li>
                            </ul>
                        <div class="menu border-100sb">
                            <img src="../../assets/imgs/calendar.png" class="bi">
                            <span id="sections-spn" class="menu-title"> <a href="./admin_schedules.php" class="admin-nav-links"> Schedules</a></span>
                        </div>
                        <!--LOCKER-->
                        <div class="menu border-100sb">
                            <img src="../../assets/imgs/subjects-logo.png" class="bi">
                            <span id="locker-spn" class="menu-title"> <a href="./admin_locker.php" class="admin-nav-links"> Locker</a></span>
                        </div>
                        <!--ANNOUNCEMENTS-->
                        <div class="menu border-100sb">
                            <img src="../../assets/imgs/subjects-logo.png" class="bi">
                            <span id="announcements-spn" class="menu-title"> <a href="./admin_announcements.php" class="admin-nav-links"> Announcements</a></span>
                        </div>
                    </nav>
                    <!--TEACHERS-->
                    <div class="menu border-100sb" id="teachers">
                        <img src="../../assets/imgs/teachers.svg" class="bi">
                        <span id="teachers-spn" class="menu-title">Teachers</span>
                        <button class="teachers-btn dropdown"><img src="../../assets/imgs/chevron-down.svg" class ="bi-chevron-down"></button>
                    </div>
                        <ul class="teachers-ul drop-content">
                            <li>
                                <a href="./admin_all_teachers.php" class="allTeachers">All Teachers</a>
                            </li>
                        </ul>
                

                    <!--ENROLLS-->
                    <div class="menu border-100sb" id="enrolls">
                        <img src="../../assets/imgs/enrolls.svg" class="bi">
                        <span id="enrolls-spn" class="menu-title">Enrolls</span>
                        <button class="enrolls-btn dropdown"><img src="../../assets/imgs/chevron-down.svg" class ="bi-chevron-down"></button>
                    </div>
                        <ul class="enrolls-ul drop-content">
                            <li>
                                <a href="./admin_all_enrollees.php" class="enrolled"> Processed Enrollments</a>
                            </li>
                            <li>
                                <a href="admin_unprocessed_enrollments.php" class="unprocessed"> Unprocessed Enrollments </a>
                            </li>
                            <li>
                                <a href="../staff/staff_pending_enrollments.php" class="pending">Pending</a>
                            </li>
                        </ul>
                </div>
            </div>
        </div>  