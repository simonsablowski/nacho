<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="en"/>
		<title>Error</title>
		<link href="<? echo $this->getApplication()->getConfiguration('basePath'); ?>css/style.css" rel="stylesheet" title="Default" type="text/css" />
	</head>
	<body>
		<div id="document">
			<h1>
				Unfortunately,
			</h1>
			<h2>
				we encountered an error:
			</h2>
			<dl class="content">
				<dt class="head">
					Description
				</dt>
				<dd class="head">
					&nbsp;
				</dd>
				<dt class="even">
					Type:
				</dt>
				<dd class="even">
					<? echo $Error->getType(); ?>

				</dd>
<? if ($Error->getMessage()): ?>
				<dt class="odd">
					Message:
				</dt>
				<dd class="odd">
					<? echo $Error->getMessage(); ?>

				</dd>
<? endif; ?>
<? if ($this->getApplication()->getConfiguration('debugMode')): ?>
<? if ($Error->getDetails()): ?>
				<dt class="even">
					Details:
				</dt>
				<dd class="even">
					<? print_r($Error->getDetails()); ?>

				</dd>
<? endif; ?>
<? if ($Error->getTrace()): ?>
				<dt class="odd">
					Trace:
				</dt>
				<dd class="odd">
					<div class="highlight">
<? var_dump($Error->getTrace()); ?>

					</div>
				</dd>
<? endif; ?>
<? endif; ?>
			</dl>
		</div>
	</body>
</html>