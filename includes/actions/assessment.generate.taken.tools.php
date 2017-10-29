<?php
include("../../initialize.php");
includeCore();

$db_handle = new DBController();
$output = getList($_POST, 'Assessment_taken', $_GET['id']);

echo(json_encode($output));
?>