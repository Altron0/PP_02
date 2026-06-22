<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Новости банка");?><?$APPLICATION->IncludeComponent(
	"testComponents:listing.cars", 
	".default", 
	[
		"STATUS" => "repair",
		"COMPONENT_TEMPLATE" => ".default"
	],
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>