<?php
global $db;

$db = new PDO('mysql:dbname=pikiintr_crmdb;host=localhost', 'pikiintr_user', '7MX1cv6a!?');

$db->query('set character_set_client=utf8');
$db->query('set character_set_connection=utf8');
$db->query('set character_set_results=utf8');
$db->query('set character_set_server=utf8');
?>