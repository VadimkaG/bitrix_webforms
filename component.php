<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock")) return;

// Все поля необходимо слить в одну строку, для запроса в базу
$props_codes = "";
if (isset($arParams["PROPERTY_CODE"]) && is_array($arParams["PROPERTY_CODE"]))
	foreach ($arParams["PROPERTY_CODE"] as $prop) {
		if (!is_string($prop) || strlen($prop) < 1) continue;
		if (strlen($props_codes) > 0) $props_codes .= ",";
		$props_codes .= $prop;
	}

// Подгружаем поля
$arResult["FIELDS"] = array();
if (isset($arParams["IBLOCK_ID"])) {
	$params = array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"]
		);

	if (strlen($props_codes) > 0)
		$params["@CODE"] = $props_codes;

	$rsProp = CIBlockProperty::GetList(
		array("SORT"=>"ASC", "ID"=>"ASC"),
		$params
	);
	while ($row=$rsProp->GetNext()) {
		$row["REQUIRED"] = false;
		$row["VALUE"] = NULL;
		$arResult["FIELDS"][$row["CODE"]] = $row;
	}
}

// Ошибки
$arResult["ERRORS"] = [];
// Идентификатор о том, что форма была обработана, для выдачи сообщения об успешной отправке
$arResult["SUBMITED"] = false;
// Запущен ли текущй скрипт через ajax
$arResult["IS_AJAX"] = false;

// Обработка сабмита формы
if (isset($_POST["wsform_event"]) && $_POST["wsform_event"] == $arParams["FORM_ID"]) {
	$arResult["IS_AJAX"] = isset($_POST["AJAX"]) && $_POST["AJAX"] === "Y";

	// Валидация полей
	if (isset($arResult["FIELDS"]) && is_array($arResult["FIELDS"]))
	foreach ($arResult["FIELDS"] as $property => $field) {

		// Для каждого типа своя валидация
		switch ($field["PROPERTY_TYPE"]) {

		// Строка
		case "S":
			// Если обзяательное поле не найдено
			if ($field["IS_REQUIRED"] == "Y" && (!isset($_POST[$property]) || ($field["PROPERTY_TYPE"] === "S" && strlen($_POST[$property]) <= 0)))
				$arResult["ERRORS"][$property] = 1;

			// Если поле строка, но нам пришла не строка
			elseif ($field["IS_REQUIRED"] == "Y" && $field["PROPERTY_TYPE"] === "S" && !is_string($_POST[$property]))
				$arResult["ERRORS"][$property] = 2;
			break;

		// Остальные поля
		default:
			// Если обзяательное поле не найдено
			if ($field["IS_REQUIRED"] == "Y" && !isset($_POST[$property]))
				$arResult["ERRORS"][$property] = 1;
		}

		if (isset($_POST[$property]))
			$arResult["FIELDS"][$property]["VALUE"] = $_POST[$property];
	}

	// Если все успешно, то выполняем сам сабмит
	if (count($arResult["ERRORS"]) <= 0) {
		$name = $arParams["NAME_TEMPLATE"];

		$props = array();
		$mailFields = array();
		foreach ($arResult["FIELDS"] as $field) {
			$name = str_replace("#".$field["CODE"]."#", $field["VALUE"], $name);
			$mailFields[$field["CODE"]] = $field["VALUE"];
			switch ($field["PROPERTY_TYPE"]) {
			default:
				$props[$field["ID"]] = $field["VALUE"];
			}
		}
		$el = new CIBlockElement();
		if ($el->Add([
			'MODIFIED_BY' => $GLOBALS['USER']->GetID(),
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"PROPERTY_VALUES" => $props,
			"NAME" => $name,
			"ACTIVE" => "Y"
		])) {
			$arResult["SUBMITED"] = true;
		} else
			$arResult["ERRORS"]["@"] = $el->LAST_ERROR;

		foreach ($arParams["MAIL_EVENTS"] as $eventID) {
			CEvent::Send($eventID, SITE_ID, $mailFields);
		}
	}
}

// Задаем скрытое поле, обозначающее текущую форму
$arResult["SYS_FIELD"] = '<input type="hidden" name="wsform_event" value="'.$arParams["FORM_ID"].'">';

// Если это ajax, то обрезаем все, что было выведено до этого компонента
if ($arResult["IS_AJAX"])
	$APPLICATION->RestartBuffer();

// Подгружаем шаблон
$this->IncludeComponentTemplate();

// Если это ajax, то все, что будет происходить далее - нам не интересно
if ($arResult["IS_AJAX"])
	die();