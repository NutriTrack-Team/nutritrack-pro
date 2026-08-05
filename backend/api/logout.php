<?php
require_once __DIR__ . '/../config/session.php';
logout();
header("Location: ../../index.php"); // landing page
exit;
