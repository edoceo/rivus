<?php
/**
 * Text/HTML Type Output
 */

// Only gets the most recent buffer
$html = ob_get_clean();

while (ob_get_level()) {
	ob_end_clean();
}

header('content-type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="application-name" content="Saltfan">
<meta name="theme-color" content="#0317a7">
<link rel="stylesheet" href="https://nobscss.com/nobs.css" crossorigin="anonymous">
<title>Saltfan</title>
</head>
<body>
	<?= $html ?>
</body>
</html>
