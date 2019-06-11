<?
CModule::IncludeModule("iblock");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$iblockFilter = (
	!empty($arCurrentValues['IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
unset($arr, $rsIBlock, $iblockFilter);

$arComponentParameters = array(
   "PARAMETERS" => array(
    "IBLOCK_TYPE" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("IBLOCK_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlockType,
        "REFRESH" => "Y",
    ),
    "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_IBLOCK"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
    ),
    "QUANTITY_ALLOW" => array(
        "NAME" => GetMessage("RICHSITE_QUANTITY_ALLOW"),
        "TYPE" => "CHECKBOX",
    ),  
    "SAVED_FILE" => array(
        "NAME" => GetMessage("RICHSITE_SAVED_FILE"),
        "TYPE" => "CHECKBOX",
    ),   
   )
);
?>