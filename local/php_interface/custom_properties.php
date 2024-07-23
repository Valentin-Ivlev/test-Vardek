<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;
use Bitrix\Main\UserField\Types\StringType;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;

class FileStringHtmlType extends StringType
{
    public const USER_TYPE_ID = 'file_string_html';

    public static function getDescription(): array
    {
        return [
            'DESCRIPTION' => 'Файл + Строка + HTML/текст',
            'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_STRING,
        ];
    }

    public static function prepareSettings(array $userField): array
    {
        return [
            'SIZE' => (int)($userField['SETTINGS']['SIZE'] ?? 0),
            'ROWS' => (int)($userField['SETTINGS']['ROWS'] ?? 1),
            'MIN_LENGTH' => (int)($userField['SETTINGS']['MIN_LENGTH'] ?? 0),
            'MAX_LENGTH' => (int)($userField['SETTINGS']['MAX_LENGTH'] ?? 0),
        ];
    }

    public static function getDbColumnType(): string
    {
        return 'text';
    }

    public static function onBeforeSave($userField, $value)
    {
        if (!is_array($value)) {
            $value = [];
        }

        $request = Context::getCurrent()->getRequest();
        $files = $request->getFile($userField['FIELD_NAME']);

        if (!empty($files['FILE'])) {
            $fileId = \CFile::SaveFile($files['FILE'], 'custom_property');
            if ($fileId) {
                $value['FILE'] = $fileId;
            }
        }

        $value['STRING'] = $request->get($userField['FIELD_NAME'])['STRING'] ?? '';
        $value['HTML'] = $request->get($userField['FIELD_NAME'])['HTML'] ?? '';

        return Json::encode($value);
    }

    public static function onAfterFetch($userField, $value)
    {
        if (!empty($value['VALUE'])) {
            return Json::decode($value['VALUE']);
        }
        return [];
    }

    public static function getFilterHtml($userField, $control): string
    {
        return '';  // Implement if needed
    }

    public static function getAdminListViewHtml($userField, $control): string
    {
        $value = static::onAfterFetch($userField, ['VALUE' => $control['VALUE']]);
        return "Строка: {$value['STRING']}, Файл: {$value['FILE']}, HTML: " . substr($value['HTML'], 0, 50) . '...';
    }

    public static function getEditFormHtml($userField, $control): string
    {
        \CModule::IncludeModule('fileman');

        $value = static::onAfterFetch($userField, ['VALUE' => $control['VALUE']]);
        $fieldName = $control['NAME'];

        $stringInput = "<input type='text' name='{$fieldName}[STRING]' value='" . htmlspecialcharsbx($value['STRING']) . "' />";

        $fileInput = \CFileInput::Show($fieldName . "[FILE]", $value['FILE'],
            ["IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "Y", "MAX_SIZE" => ["W" => 200, "H" => 200]],
            ["upload" => true, "medialib" => true, "file_dialog" => true, "cloud" => true, "del" => true, "description" => false]
        );

        ob_start();
        \CFileMan::AddHTMLEditorFrame(
            $fieldName . "[HTML]",
            $value['HTML'],
            "HTML",
            "html",
            ['height' => 200, 'width' => '100%']
        );
        $htmlEditor = ob_get_clean();

        return 'Строка: ' . $stringInput . '<br>Файл: ' . $fileInput . '<br>HTML/TEXT: ' . $htmlEditor;
    }
}

EventManager::getInstance()->addEventHandler('main', 'OnUserTypeBuildList', [FileStringHtmlType::class, 'getDescription']);