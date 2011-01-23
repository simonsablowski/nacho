<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="en"/>
		<title>Error</title>
	</head>
	<body>
		<pre>
			<type><? echo $Error->getType(); ?></type>
			<code><? echo $Error->getCode(); ?></code>
			<message><? echo $Error->getMessage(); ?></message>
			<details><? print_r($Error->getDetails()); ?></details>
			<trace><? echo $Error->getTraceAsString(); ?></trace>
		</pre>
	</body>
</html>