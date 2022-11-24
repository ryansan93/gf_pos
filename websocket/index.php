<?php 
	// $url = 'c:/xampp_php7/htdocs'.dirname($_SERVER['PHP_SELF']).'/server';

	$out = '';
	$err = '';

	exec("cd \server && node index.js 2>&1", $out, $err); 

	// echo "<pre>";
	// print_r($out);
	// echo "</pre>";
	// echo "<pre>";
	// print_r($err);
	// echo "</pre>";
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<span>COBA WEB SOCKET</span>

	<script type="text/javascript">
		setTimeout(
			function () {
				const ws = new WebSocket("ws://localhost:8032");

				ws.addEventListener("open", () => {
					console.log("We are connected!");
				});
			}, 5000);
	</script>
</body>
</html>