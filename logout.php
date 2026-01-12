<?php
// logout.php - Realiza logout seguro
session_start();
session_destroy();
header('Location: index.php?logout=success');
exit;
?>