<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("RICHSITE_CATALOG_MODULE_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("currency"))
{
	ShowError(GetMessage("RICHSITE_CURRENCY_MODULE_NOT_INSTALL"));
	return;
}


if($arParams["IBLOCK_ID"] == NULL){
    ShowError(GetMessage("RICHSITE_PARAMS_IBLOCK_NOT_EXIST"));
    return;
}

if(!$USER->isAdmin()){
    if(file_exists("saved_file.xml")){
        $APPLICATION->RestartBuffer();
        $arResult["sxe"] = simplexml_load_file("saved_file.xml");
    }
    else{
        $currentUrl = (CMain::IsHTTPS()) ? "https://" : "http://";

        $currentUrl .= $_SERVER["HTTP_HOST"];
        
        if($arParams["QUANTITY_ALLOW"] == "Y"){
            $quantity_allow = true;
        }
        else{
            $quantity_allow = false;
        }

        $drom = new Drom($arParams["IBLOCK_ID"], $currentUrl, $quantity_allow);

        $sectionsIDs = $drom->getSectionsIDs();

        $arResult["products"] = $drom->getProducts($sectionsIDs);

        if($arResult["products"] == NULL){
            ShowError(GetMessage("RICHSITE_PRODUCTS_NOT_EXIST"));
            return;
        }

        $arResult["sxe"] = $drom->getSimpleXmlElement($arResult["products"]);

        $APPLICATION->RestartBuffer();

        if($arParams["SAVED_FILE"] != NULL){
            if($arParams["SAVED_FILE"] == "Y"){
                if(!file_exists("saved_file.xml")){
                    $arResult["sxe"]->asXML("saved_file.xml");
                }
            }
        }
    }
    $this->IncludeComponentTemplate();

    die;
}

?>