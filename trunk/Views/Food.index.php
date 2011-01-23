<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="en"/>
		<title>Home</title>
	</head>
	<body>
		<h1>
			Dear guest,
		</h1>
		<div>
			<h2>
				currently we serve:
			</h2>
			<ul>
<? foreach ($Food as $SpecialFood): ?>
				<li>
					<? echo $SpecialFood::getName(); ?><br/>

					<? if ($SpecialFood::getNutritionFacts()): ?>Nutrition facts: <? echo $SpecialFood::getNutritionFacts(); ?><? endif; ?>
				</li>
<? endforeach; ?>
			</ul>
			<p>
				If you would like us to extend our menu, feel free to create new Model classes which extend the Food Model.
			</p>
		</div>
	</body>
</html>