<?php 
    require_once __DIR__ . '/../../teacher/views/teacherStudentInformationView.php';
    $view = new teacherStudentInformationView();
?>
<h1> Impormasyon ng Estudyante</h1>
<?php $view->displayStudentInformation(); ?>
<h1> Mga grado ng Estudyante </h1>
<?php $view->displayStudentGrades(); ?>
