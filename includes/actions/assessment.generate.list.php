<?php
include("../../initialize.php");
includeCore();

$db_handle = new DBController();
$output = getList($_POST, 'IDP', 0);

echo(json_encode($output));
?>