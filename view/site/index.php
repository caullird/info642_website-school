
<?php 
	if ($_SESSION['user']['idrole'] == 1) {
		include_once "student_view.php";
	}
?>

<?php 
	if ($_SESSION['user']['idrole'] == 2) {
		include_once "teacher_view.php";
	}
?>

<?php 
	if ($_SESSION['user']['idrole'] == 3) {
		include_once "admin_view.php";
	}
?>