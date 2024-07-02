<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => "ID инфоблока",
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "ELEMENT_ID" => array(
            "PARENT" => "BASE",
            "NAME" => "ID элемента",
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "PROPERTY_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => "Код свойства",
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
    ),
);