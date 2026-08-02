<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = '/home/u781960094/domains/komenk.balmonjogja.info/komenk_config.php';
echo '<pre>';
echo htmlspecialchars(file_get_contents($config));
echo '</pre>';
