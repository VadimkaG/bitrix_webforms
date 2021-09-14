<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("Формы"),
	"DESCRIPTION" => GetMessage("Формы обратной связи с пользователем"),
	"PATH" => array(
		"ID" => "webshop_components",
		"CHILD" => array(
			"ID" => "webforms",
			"NAME" => "Веб формы"
		)
	)
);