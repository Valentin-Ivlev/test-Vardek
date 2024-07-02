<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;

class CUserTypeFileStringHtml extends CUserTypeString
{
    const USER_TYPE_ID = 'file_string_html';

    public function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => self::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => "Файл + Строка + HTML/текст",
            "BASE_TYPE" => "string",
            "MULTIPLE" => "Y",
        );
    }

    public function GetDBColumnType($arUserField)
    {
        global $DB;
        switch(strtolower($DB->type))
        {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(max)";
        }
    }

    public function PrepareSettings($arUserField)
    {
        return array(
            "SIZE" =>  intval($arUserField["SETTINGS"]["SIZE"]),
            "ROWS" => intval($arUserField["SETTINGS"]["ROWS"]),
            "MIN_LENGTH" => intval($arUserField["SETTINGS"]["MIN_LENGTH"]),
            "MAX_LENGTH" => intval($arUserField["SETTINGS"]["MAX_LENGTH"]),
            "DEFAULT_VALUE" => $arUserField["SETTINGS"]["DEFAULT_VALUE"],
        );
    }

    public function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        CModule::IncludeModule("fileman");

        $fieldName = $arHtmlControl["NAME"];
        $value = $arUserField["VALUE"];

        if (!is_array($value)) {
            $value = unserialize($value);
        }

        $stringInput = '<input type="text" name="' . $fieldName . '[STRING]" value="' . htmlspecialcharsbx($value["STRING"]) . '" />';

        $fileInput = CFileInput::Show($fieldName . "[FILE]", $value["FILE"],
            array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "Y", "MAX_SIZE" => array("W" => 200, "H" => 200)),
            array("upload" => true, "medialib" => true, "file_dialog" => true, "cloud" => true, "del" => true, "description" => false)
        );

        ob_start();
        CFileMan::AddHTMLEditorFrame(
            $fieldName . "[HTML]",
            $value["HTML"],
            "HTML",
            "html",
            array('height' => 200, 'width' => '100%')
        );
        $htmlEditor = ob_get_clean();

        return 'Строка: ' . $stringInput . '<br>Файл: ' . $fileInput . '<br>HTML/TEXT: ' . $htmlEditor;
    }

    public function OnBeforeSave($arUserField, $value)
    {
        if (!is_array($value)) {
            $value = array();
        }

        if (!empty($_FILES[$arUserField["FIELD_NAME"] . '_FILE'])) {
            $fileId = CFile::SaveFile($_FILES[$arUserField["FIELD_NAME"] . '_FILE'], "custom_property");
            if ($fileId) {
                $value['FILE'] = $fileId;
            }
        }

        $value['STRING'] = $_REQUEST[$arUserField["FIELD_NAME"]]['STRING'];
        $value['HTML'] = $_REQUEST[$arUserField["FIELD_NAME"]]['HTML'];

        return serialize($value);
    }

    public function OnAfterFetch($arUserField, $arResult)
    {
        if (!empty($arResult["VALUE"])) {
            return unserialize($arResult["VALUE"]);
        }
        return array();
    }
}

EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array("CUserTypeFileStringHtml", "GetUserTypeDescription"));