<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/global.functions.php");
includeCore();

$db_handle = new DBController();
$output = getList($_POST, 'Evac', 0);

echo(json_encode($output));
?>