<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="en"/>
		<title>Error</title>
	</head>
	<body>
		<dl>
			<dt>
				Type:
			</dt>
			<dd>
				<? echo $Error->getType(); ?>

			</dd>
<? if ($Error->getCode()): ?>
			<dt>
				Code:
			</dt>
			<dd>
				<? echo $Error->getCode(); ?>

			</dd>
<? endif; ?>
<? if ($Error->getMessage()): ?>
			<dt>
				Message:
			</dt>
			<dd>
				<? echo $Error->getMessage(); ?>

			</dd>
<? endif; ?>
<? if ($Error->getDetails()): ?>
			<dt>
				Details:
			</dt>
			<dd>
				<? print_r($Error->getDetails()); ?>

			</dd>
<? endif; ?>
<? if ($Error->getTrace()): ?>
			<dt>
				Trace:
			</dt>
			<dd>
<? var_dump($Error->getTrace()); ?>

			</dd>
<? endif; ?>
		</dl>
	</body>
</html>