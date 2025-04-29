<?php
session_start();
session_destroy();
header('Location: /projo/index.php');
exit();
