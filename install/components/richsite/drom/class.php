<?

Class Drom{
    
    private $iblockId;
    private $url;
    private $quantity_allow;
    
    public function __construct($iblockId, $url, $quantity_allow){
        $this->iblockId = $iblockId;
        $this->url = $url;
        $this->quantity_allow = $quantity_allow;
    }
    
    public function getSectionsIDs(){
        $dbSect = CIBlockSection::GetList(array("ID"=>"asc"), array("IBLOCK_ID"=>$this->iblockId, "ACTIVE"=>"Y"), false, array("ID", "LEFT_MARGIN","RIGHT_MARGIN", "DEPTH_LEVEL"));
        while($resSect = $dbSect->GetNext()){
            $sectionsIDs[] = $resSect["ID"];

            $dbSectNested = CIBlockSection::GetList(array("ID"=>"asc"), array("IBLOCK_ID"=>$this->iblockId, ">LEFT_MARGIN"=>$resSect["LEFT_MARGIN"], "<RIGHT_MARGIN"=>$resSect["RIGHT_MARGIN"], ">DEPTH_LEVEL"=>$resSect["DEPTH_LEVEL"], "ACTIVE"=>"Y"));

            while($resSectNested = $dbSectNested->GetNext()){
                $sectionsIDs[] = $resSectNested["ID"];
            }
        }
        if(isset($sectionsIDs)){
            $sectionsIDs = array_unique($sectionsIDs);
        }
        
        return $sectionsIDs;
    }
    
    public function getProducts($sectionsIDs){
        
        $dbRes = CIBlockElement::GetList(array("ID"=>"asc"), array("IBLOCK_ID"=>$this->iblockId, "IBLOCK_SECTION_ID"=>$sectionsIDs, "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "DETAIL_TEXT", "DETAIL_PICTURE"));
        
        $products = [];
        
        while($arRes = $dbRes->GetNext()){
            $products[$arRes["ID"]]["name"] = $arRes["NAME"];
            $products[$arRes["ID"]]["description"] = $arRes["DETAIL_TEXT"];
            $products[$arRes["ID"]]["img"] = CFile::GetPath($arRes["DETAIL_PICTURE"]);
            $products[$arRes["ID"]]["img"] = $this->url.$products[$arRes["ID"]]["img"];
            $products[$arRes["ID"]]["price"] = CPrice::GetBasePrice($arRes["ID"]);
            $products[$arRes["ID"]]["price"] = $products[$arRes["ID"]]["price"]["PRICE"];
            $products[$arRes["ID"]]["quantity"] = CCatalogProduct::GetByID($arRes["ID"]);
            $products[$arRes["ID"]]["quantity"] = $products[$arRes["ID"]]["quantity"]["QUANTITY"];
        }
        
        return $products;
    }
    
    public function getSimpleXmlElement($products){
        $xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?> <!DOCTYPE yml_catalog SYSTEM \"shops.dtd\"><yml_catalog ></yml_catalog>";
        $sxe = new SimpleXMLElement($xmlstr);
        $sxe->addAttribute('date', date("Y-m-d H:i"));
        $shop = $sxe->addChild('shop');
        $offers = $shop->addChild('offers');
        
        $currency = CCurrency::GetBaseCurrency();

        foreach($products as $key=>$value){
            if($this->quantity_allow && ($value["quantity"] <= 0)){
                continue;
            }
            else{
                $offer = $offers->addChild('offer');
                $offer->addAttribute("id", $key);
                $name = $offer->addChild("name", htmlspecialchars($value["name"]));
                $desc = $offer->addChild("description", htmlspecialchars($value["description"]));
                $price = $offer->addChild("price", $value["price"]);
                $currency = $offer->addChild("currencyId", $currency);
                if($value["img"] != $this->url){
                    $picture = $offer->addChild("picture", $value["img"]);
                }
            }
        }
        return $sxe;
    }
}

?>