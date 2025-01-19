<?php
session_start();
session_destroy();
header("Location: ../pages/registration/index.php");
exit();
?>
