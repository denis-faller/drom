<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Выгрузка в Дром");
?>
<?$APPLICATION->IncludeComponent(
	"richsite:drom",
	"",
	Array(
	)
);?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>