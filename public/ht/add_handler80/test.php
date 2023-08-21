<?php
	$version = explode('.',str_replace(preg_replace('/^(\d{1,2})\.(\d{1,2})\.(\d{1,2})/', '', PHP_VERSION), "", PHP_VERSION));
	echo ($version[0] * 10000 + $version[1] * 100 + $version[2]);
?>
