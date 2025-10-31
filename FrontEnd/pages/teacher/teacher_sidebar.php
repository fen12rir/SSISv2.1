<div class="sidebar active">
        <div class="sidebar-wrapper">
            <div class="sidebar-title">
                <span class="SSIS">SSIS</span>
                <button class="menu-btn" onclick="menu()"><img src="../../assets/imgs/bar.svg" class="menu-btn"></button>
            </div>
            <div class="menu-wrappper">
                <!--DASHBOARD-->
                <div class="menu border-100sb" id="dashboard">
                    <img src="../../assets/imgs/easel.svg" class="bi">
                    <a href="./teacher_dashboard.php"><span id="dashboard-spn" class="menu-title">Dashboard</span></a>
                </div>
                <div class="menu border-100sb" id="pending">
                    <img src="../../assets/imgs/newspaper.png" alt="newspaper" class="bi">
                    <span class="menu-title"> <a href="../staff/staff_pending_enrollments.php">Pending Enrollments </a></span>
                </div>
                <div class="menu border-100sb" id="subjects">
                    <img src="../../assets/imgs/subjects-logo.png" alt="newspaper" class="bi">
                    <span class="menu-title"> <a href="./teacher_subjects_handled.php"> Subjects Handled </a></span>
                </div>
                <div class="menu border-100sb" id="grading">
                    <img src="../../assets/imgs/a-plus.png" alt="grade-students" class="bi">
                    <span class="menu-title"> <a href="./teacher_grades.php"> Grade Students </a> </span>
                </div>
                <div class="menu border-100sb" id="locker">
                    <img src="../../assets/imgs/subjects-logo.png" alt="locker" class="bi">
                    <span class="menu-title"> <a href="./teacher_locker.php"> Locker </a> </span>
                </div>
                <?php
                    $teacherIsAnAdviser->displayAdvisoryHyperLink();
                ?>
            </div>
        </div>
    </div>