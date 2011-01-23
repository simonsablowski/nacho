<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="en"/>
		<title>Our Menu</title>
		<link href="<? echo $this->getConfiguration('baseDirectory'); ?>css/style.css" rel="stylesheet" title="Default" type="text/css" />
	</head>
	<body>
		<div id="document">
			<h1>
				Dear guest,
			</h1>
			<h2>
				currently we serve:
			</h2>
			<table id="Food" class="content">
				<thead class="head">
					<tr>
						<th class="title number">
							#
						</th>
						<th class="title">
							Dish
							<span class="price">Price</span>
						</th>
					</tr>
				</thead>
				<tbody class="body">
<? foreach ($Food as $n => $SpecialFood): ?>
					<tr class="<? echo $n % 2 ? 'even' : 'odd'; ?>">
						<th class="title number">
							<? echo $n + 1; ?>.
						</th>
						<th class="title">
							<? echo $SpecialFood::getName(); ?>

							<span class="price"><? echo $SpecialFood::getPrice(); ?></span>
							<? if ($SpecialFood::getNutritionFacts()): ?><br/><span class="description">Nutrition facts: <? echo $SpecialFood::getNutritionFacts(); ?></span><? endif; ?>

						</td>
					</tr>
<? endforeach; ?>
				</tbody>
			</table>
			<p>
				If you would like us to extend our menu, feel free to create new <em>Model</em> classes which extend the <em>Food</em> Model.
			</p>
			<p>
				We will gladly provide you which richly populated objects as soon as you give us information about your needs easily by creating Model extending classes.
			</p>
		</div>
	</body>
</html>