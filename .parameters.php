<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;

$arIBlocks=array();
$db_iblock = CIBlock::GetList(
	array("SORT"=>"ASC"),
	array(
		"SITE_ID"=>$_REQUEST["site"],
		"TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")
	)
);
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(
	array("sort"=>"asc", "name"=>"asc"),
	array(
		"ACTIVE"=>"Y",
		"IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])
	)
);
while ($arr=$rsProp->Fetch()) {
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
}

$eventsRaw = CEventType::GetList(
	array(
		"EVENT_TYPE" => "email",
		"LID" => LANGUAGE_ID
	)
);
$events = array();
while ($row = $eventsRaw->GetNext()) {
	$events[$row["EVENT_NAME"]] = $row["NAME"];
}
unset($eventsRaw);

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => "Информационный блок",
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"REFRESH" => "Y"
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => "Свойства",
			"TYPE" => "LIST",
			"VALUES" => $arProperty_LNS,
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y"
		),
		"MAIL_EVENTS" => array(
			"PARENT" => "BASE",
			"NAME" => "События после отправки",
			"TYPE" => "LIST",
			"VALUES" => $events,
			"MULTIPLE" => "Y"
		),
		"FORM_ID" => array(
			"PARENT" => "BASE",
			"NAME" => "Идентификатор формы",
			"TYPE" => "STRING",
			"DEFAULT" => "wsform"
		),
		"NAME_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => "Шаблон для заголовка",
			"TYPE" => "STRING",
			"DEFAULT" => "#NAME#"
		),
		"AJAX" => array(
			"PARENT" => "BASE",
			"NAME" => "Ajax",
			"TYPE" => "CHECKBOX"
		)
	)
);