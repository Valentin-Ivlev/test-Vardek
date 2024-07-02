<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="file-string-html-property">
    <?php if(!empty($arResult["VALUE"])): ?>
        <?php $value = unserialize($arResult["VALUE"]); ?>
        <div class="string-value">
            <strong>Строка:</strong> <?= htmlspecialcharsbx($value["STRING"]) ?>
        </div>
        <?php if(!empty($value["FILE"])): ?>
            <div class="file-value">
                <strong>Файл:</strong>
                <?= CFile::ShowImage($value["FILE"], 200, 200, "border=0", "", true) ?>
            </div>
        <?php endif; ?>
        <div class="html-value">
            <strong>HTML/TEXT:</strong>
            <?= $value["HTML"] ?>
        </div>
    <?php else: ?>
        <p>Значение свойства не задано</p>
    <?php endif; ?>
</div>