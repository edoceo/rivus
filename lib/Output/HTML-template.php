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
<meta name="application-name" content="Saltfan">
<meta name="theme-color" content="#0317a7">
<link rel="stylesheet" href="https://nobscss.com/nobs.css" crossorigin="anonymous">
<title><?= __h(strip_tags($this->name)) ?></title>
<style>
main {
	margin: 0 1vw;
	max-width: 1120px;
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

<header>
	<nav>
		<ul>
			<li><a href="/">/[root]</a></li>
			<li><a href="/home">/home</a></li>
			<li><a href="/ping">/ping</a></li>
			<li><a href="/post">/post</a></li>
		</ul>
	</nav>
</header>
<main>
<?= $this->body ?>
</main>
</body>
</html>
