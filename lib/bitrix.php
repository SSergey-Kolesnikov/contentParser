<?php
if (!is_file('../bitrix/modules/main/include/prolog_before.php') && is_readable('../bitrix/modules/main/include/prolog_before.php')) {
    throw new Exception('Failed to mount the file api for Bitrix');
}

require_once '../bitrix/modules/main/include/prolog_before.php';

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

$se = new CIBlockSection;
$ibp = new CIBlockProperty;
$ibpenum = new CIBlockPropertyEnum;
$el = new CIBlockElement;

class uBitrix {
    /**
     * Получение ID свойства инфоблока по его названию
     *
     * @global $app
     * @param string $name Наименование свойства
     * @param integer $iblock ID инфоблока свойства
     * @param boolean|array $data Массив с данными для нового свойства инфоблока
     * @param boolean|string $prefix Префикс для поля "CODE"
     * @return integer
     */
    function getPropertyIDFromName($name, $iblock = 1, $data = false, $prefix = false) {
        global $app;

        $db_properties_list = CIBlockProperty::GetList(Array('ID' => 'ASC'), array('NAME' => $name, 'IBLOCK_ID' => $iblock));
        if ($ar_properties_list = $db_properties_list->Fetch())
            $PropID = $ar_properties_list['ID'];

        if (!$PropID && is_array($data)) {
            $data = array_merge($data, array('NAME' => $name, 'IBLOCK_ID' => $iblock));
            $PropID = $this->setProperty($data, $prefix);
        }

        return (int)$PropID;
    }

    /**
     * Создание свойства инфоблока
     *
     * @global $app
     * @global $ibp
     * @param array $data Набор полей свойства инфоблока
     * @param boolean|string $prefix Префикс для поля "CODE"
     * @return integer
     */
    function setProperty(array $data, $prefix = false) {
        global $app, $ibp;

        foreach (['IBLOCK_ID', 'NAME'] as $required) {
            if (!isset($data[$required])) {
                $app->message('Ошибка добавления свойства! Поле "'.$required.'" обязательно для заполнения.');
                $app->view($data);
                exit;
            }
        }

        $data['CODE'] = $app->transliterationText($prefix.$data['NAME'], '_', true);

        if (!$PropID = $ibp->Add($data)) {
            $app->message('Ошибка добавления свойства!');
            $app->message($ibp->LAST_ERROR);
            $app->view($data);
            exit;
        }

        return (int)$PropID;
    }
}