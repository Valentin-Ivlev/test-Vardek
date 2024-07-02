<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult = array();

if($arParams["IBLOCK_ID"] && $arParams["ELEMENT_ID"] && $arParams["PROPERTY_CODE"])
{
    CModule::IncludeModule("iblock");
    $arSelect = array("ID", "IBLOCK_ID", "PROPERTY_".$arParams["PROPERTY_CODE"]);
    $arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $arParams["ELEMENT_ID"]);
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    if($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arResult["VALUE"] = $arFields["PROPERTY_".$arParams["PROPERTY_CODE"]."_VALUE"];
    }
}

$this->IncludeComponentTemplate();