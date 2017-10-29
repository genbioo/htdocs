<?php
session_start();
include("includes/global.functions.php");
set_approot_dir(preg_replace('~\\\~', '/', dirname(__FILE__)));
?>