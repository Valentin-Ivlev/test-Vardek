<?php
use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class FileStringHtmlPropertyComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->startResultCache()) {
            $this->arResult = [];

            if ($this->arParams["IBLOCK_ID"] && $this->arParams["ELEMENT_ID"] && $this->arParams["PROPERTY_CODE"]) {
                if (!Loader::includeModule("iblock")) {
                    $this->abortResultCache();
                    ShowError('Module "iblock" is not installed');
                    return;
                }

                $element = ElementTable::getList([
                    'filter' => [
                        'IBLOCK_ID' => $this->arParams["IBLOCK_ID"],
                        'ID' => $this->arParams["ELEMENT_ID"]
                    ],
                    'select' => ['ID', 'IBLOCK_ID', 'PROPERTY_' . $this->arParams["PROPERTY_CODE"]]
                ])->fetch();

                if ($element) {
                    $this->arResult["VALUE"] = $element['PROPERTY_' . $this->arParams["PROPERTY_CODE"] . '_VALUE'];
                }
            }

            $this->includeComponentTemplate();
        }
    }
}