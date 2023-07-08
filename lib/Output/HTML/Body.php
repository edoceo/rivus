<?php
/**
 * Text/HTML Type Output
 */

header('content-type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="application-name" content="Rivus">
<meta name="theme-color" content="#0317a7">
<link rel="stylesheet" href="/vendor/bootstrap/bootstrap.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<title><?= __h(strip_tags($this->name)) ?></title>
<style>
html {
	min-height: 100vh;
	/* height: -webkit-fill-available; */
}
body {
	min-height: 100vh;
	/* min-height: -webkit-fill-available; */
}
main {
	height: 100vh;
	/* height: -webkit-fill-available; */
	max-height: 100vh;
	/* overflow-x: auto;
	overflow-y: hidden; */
}

section {
	border: 1px solid #ddd;
	border-radius: 0.25rem;
	margin: 0 0 0.50rem 0;
}

section.note {

}

hr {
	background: #333;
	border: 2px solid #333;
	color: #333;
	margin: 0.50rem;
}

</style>
</head>
<body>
<main class="d-flex flex-nowrap">
<?php
require_once(__DIR__ . '/Menu.php')
?>
<div class="d-flex flex-column flex-grow-1 overflow-auto">
	<header>
		<h1>Header</h1>
	</header>
	<div class="container">
		<?= $this->body ?>
	</div>
</div>

<script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script>


</body>
</html>
