<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}
 
$APPLICATION->SetTitle(Loc::getMessage('LISTING_CARS_TITLE'));

?>

<div id="listing-cars" class="listing-cars">
	<div class="cards">
		<?php foreach($arResult['cars'] as $car): ?>
			<div class="card">
				<h4>Наименование авто: <?= $car['_UF_MODEL'] ?></h4>
				<h6>Дата бронирования:</h6>
				<div class="card__info">
					<p>Дата начала: <br> <?= new DateTime($car['UF_DATE_START'])->format('Y-m-d') ?></p>
					<p>Дата окончания: <br> <?= new DateTime($car['UF_DATE_END'])->format('Y-m-d') ?></p>
				</div>
			</div>
		<?php endforeach ?>
	</div>
</div>