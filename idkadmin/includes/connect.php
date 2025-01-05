<?php
global $db;

$db = new PDO('mysql:dbname=pikiba_b2b;host=localhost', 'pikiba_b2b_dbu', 'u=UwfGEW6KQb');

$db->query('set character_set_client=utf8');
$db->query('set character_set_connection=utf8');
$db->query('set character_set_results=utf8');
$db->query('set character_set_server=utf8');
?>