<?php

namespace Local\Classes\Models;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;


class StatusTable extends DataManager
{

	public static function getTableName()
	{
		return 'statuses';
	}

	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('_ENTITY_ID_FIELD'),
					'size' => 8,
				]
			),
			new TextField(
				'UF_NAME',
				[
					'title' => Loc::getMessage('_ENTITY_UF_NAME_FIELD'),
				]
			),
			new TextField(
				'UF_CODE',
				[
					'title' => Loc::getMessage('_ENTITY_UF_CODE_FIELD'),
				]
			),
		];
	}
}