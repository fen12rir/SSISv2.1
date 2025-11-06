<?php
require_once __DIR__ . '/../session_init.php';
require_once __DIR__ . '/../../../BackEnd/common/UserTypeView.php';
if (!isset($_SESSION['User']['User-Id']) || !isset($_SESSION['User']['Registration-Id'])) {
    header("Location: ../../Login.php");
    exit();
}
$extraCss1 = isset($pageCss2) ? $pageCss2 : '';
$extraCss2 = isset($pageCss3) ? $pageCss3 : '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php $title = isset($pageTitle) ? $pageTitle : '';  echo $title ?> </title>
    <link rel="stylesheet" href="../../assets/css/user/user-base-design.css">
    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/fonts.css">
    <?php
        if(isset($pageCss)) echo $pageCss;
        echo $extraCss1;
        echo $extraCss2;
    ?>
</head>
<body>
    <div class="main-content">
        <?php include_once __DIR__ . '/./user_header.php'?>
        <?php if(isset($pageContent)) { echo $pageContent; } ?>
    </div>
</body>
<script src="../../assets/js/user/user-base-design.js" defer></script>
<?php if(isset($pageJs)) echo $pageJs; ?>
</html>
