<!DOCTYPE html>
<html lang="pl-PL">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="./public/upgrade/bootstrap.min.css">
		<link rel="stylesheet" href="./public/setup/setup.css?ver=<?php echo rand(1000,9999); ?>">
		<script src="./public/upgrade/jquery-3.6.1.min.js"></script>
		<script src="./public/setup/setup.js?ver=<?php echo rand(1000,9999); ?>"></script>
		<title><?php echo APP_NAME; ?> Installer</title>
		<link rel="shortcut icon" href="favicon2022.ico">
	</head>
	<body class="disable-text-select">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<br>
					<h1 class="text-center">
						<?php echo APP_NAME; ?> <span id="app_title">Installer</span>
					</h1>
					<br><br><br>
					<center>
						<table id="version_table">
							<tr>
								<th>App version</th>
								<th>PHP version detected</th>
								<th>PHP version required</th>
							</tr>
							<tr>
								<td><?php echo $GLOBALS['app_version']; ?><br>&nbsp;</td>
								<td><?php echo str_replace(preg_replace('/^(\d{1,2})\.(\d{1,2})\.(\d{1,2})/', '', PHP_VERSION), "", PHP_VERSION); ?><br>&nbsp;</td>
								<td><?php echo PHP_REQUIRED; ?><br>&nbsp;</td>
							</tr>
						</table>
					</center>
					<br>
				</div>
			</div>
			<center>
				<div id="app_content">{{content}}</div>
			</center>
			<div style="position:absolute;top:10px;left:10px;display:flex">
				<input type="button" class="btn btn-primary" id="setup_button_panel" value="Strona główna" style="width:120px">
				<a href="logs.php" target="_blank" class="btn btn-primary center-block" style="width:120px;margin-left:10px">Logi</a>
				<a href="recovery.php" target="_blank" class="btn btn-primary center-block" style="width:120px;margin-left:10px">Odzyskiwanie</a>
				<a href="upgrade.php" target="_blank" class="btn btn-primary center-block" style="width:120px;margin-left:10px">Aktualizacja</a>
			</div>
		</div>
	</body>
</html>
