<?php
session_start();
session_destroy();
header("Location: ../views/registration/login");
exit();
?>
