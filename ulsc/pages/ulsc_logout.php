<?php
session_start();
session_unset();
session_destroy();
header("Location: ulsc_login.php");
exit();
?>
