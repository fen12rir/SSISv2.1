<?php  
    require_once __DIR__ . '/../../../BackEnd/teacher/view/teacherIsAnAdviserView.php';
    require_once __DIR__ . '/../../../BackEnd/common/UserTypeView.php';
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(!isset($_SESSION['Staff']) || $_SESSION['Staff']['Staff-Type'] !== 2) {
        header('Location: ../../Login.php');
        exit();
    }
    $teacherIsAnAdviser = new teacherIsAnAdviserView();
    $userType = new UserTypeView((int)$_SESSION['Staff']['Staff-Type']);
    $pageTitle_full = (string)$userType;
    if(isset($pageTitle)) {  
        $pageTitle_full .= ' - ' . $pageTitle;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $pageTitle_full?></title> 
    <link rel="stylesheet" href="../../assets/css/teacher/teacher-base-designs.css">
    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/loader.css">
    <link rel="stylesheet" href="../../assets/css/notifications.css">
    <link rel="icon" href="../../../favicon.ico">
    <?php if(isset($pageCss)) {echo $pageCss;} else { echo '';} ?>
</head>
<body>
   <?php
        require_once __DIR__ . '/../loader.php';
   ?>
   <script src="../../assets/js/notifications.js"></script>
   <script src="../../assets/js/teacher/teacher-base-designs.js" defer></script>

   <div class="main-wrapper">
        <?php require_once __DIR__ . '/teacher_sidebar.php';?> 
        <div class="content">
            <?php require_once __DIR__ . '/teacher_header.php'; 
                if(isset($pageContent)) {
                    echo $pageContent;
                }
            ?>
        </div>
   </div>
   <?php if(isset($pageJs)) {echo $pageJs;} else { echo '';} ?>
</body>
</html>
