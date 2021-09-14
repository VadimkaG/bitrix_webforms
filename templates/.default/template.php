<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arResult["SUBMITED"]) {
	?><div>Ваше сообщение отправлено</div><?
}
if (isset($arResult["ERRORS"]["@"])) {
	?><div><?=$arResult["ERRORS"]["@"];?></div><?
}

?><form id="wsform_<?=$arParams["FORM_ID"];?>" method="POST" <?if ($arParams["AJAX"] == "Y"):?>webshop-webform-ajax="<?=$arParams["FORM_ID"];?>"<?endif;?>><?
if ($arParams["AJAX"] !== "Y")
	echo $arResult["SYS_FIELD"];
foreach ($arResult["FIELDS"] as $field) {
	switch ($field["PROPERTY_TYPE"]) {
	default:
		$field_id = "wsform_".$arParams["FORM_ID"]."__".$field["CODE"];
		?>
		<div>
			<label for="<?=$field_id;?>"><?=$field["NAME"]?><?if($field["IS_REQUIRED"] == "Y"):?> * <?endif;?></label>
			<?if ((int)$field["ROW_COUNT"] > 1):?>
			<textarea
				id="<?=$field_id;?>"
				name="<?=$field["CODE"]?>"
				<?if($field["IS_REQUIRED"] == "Y"):?>required="Y"<?endif;?>
				><?=isset($field["VALUE"])?$field["VALUE"]:''?></textarea>
			<?else:?>
			<input
				id="<?=$field_id;?>"
				type="text"
				name="<?=$field["CODE"]?>"
				value="<?=isset($field["VALUE"])?$field["VALUE"]:''?>"
				<?if($field["IS_REQUIRED"] == "Y"):?>required="Y"<?endif;?>
				>
			<?endif;?>
			<?if(isset($arResult["ERRORS"][$field["CODE"]])):?>
			<div><?
			switch ($arResult["ERRORS"][$field["CODE"]]) {
				case 1:
					?>Поле <?=$field["NAME"];?> обязательно для заполнения<?
					break;
				default:
					?>Ошибка поля <?=$field["NAME"];?><?
			}
			?></div>
			<?endif;?>
		</div>
		<?
	}
}
?>
<div>
	<input type="submit">
</div>
</form>