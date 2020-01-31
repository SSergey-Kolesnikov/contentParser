<?php
class uBitrix extends App {
    /** @var CUser $CUser */
    public $CUser;

    /** @var CIBlockSection $CIBlockSection */
    public $CIBlockSection;

    /** @var CIBlockProperty $CIBlockProperty */
    public $CIBlockProperty;

    /** @var CIBlockPropertyEnum $CIBlockPropertyEnum */
    public $CIBlockPropertyEnum;

    /** @var CIBlockElement $CIBlockElement */
    public $CIBlockElement;

    /**
     * uBitrix constructor
     *
     * @param array $config
     *
     * @throws Exception
     */
    function __construct(array $config) {
        parent::__construct($config);

        if (!is_file('../bitrix/modules/main/include/prolog_before.php') && is_readable('../bitrix/modules/main/include/prolog_before.php')) {
            throw new Exception('Failed to mount the file api for Bitrix');
        }
        /** @noinspection PhpIncludeInspection */
        require_once('../bitrix/modules/main/include/prolog_before.php');

        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");

        /** @var CUser $CUser */
        $this->CUser = $USER;

        /** @var CIBlockSection $CIBlockSection */
        $this->CIBlockSection = new CIBlockSection();

        /** @var CIBlockProperty $CIBlockProperty */
        $this->CIBlockProperty = new CIBlockProperty();

        /** @var CIBlockPropertyEnum $CIBlockPropertyEnum */
        $this->CIBlockPropertyEnum = new CIBlockPropertyEnum();

        /** @var CIBlockElement $CIBlockElement */
        $this->CIBlockElement = new CIBlockElement();
    }

    /**
     * Получение ID свойства инфоблока по его названию
     *
     * @param string $name Наименование свойства
     * @param integer $iblock ID инфоблока свойства
     * @param boolean|array $data Массив с данными для нового свойства инфоблока
     * @param boolean|string $prefix Префикс для поля "CODE"
     *
     * @return integer
     */
    function getPropertyIDFromName($name, $iblock = 1, $data = false, $prefix = false) {
        $properties = CIBlockProperty::GetList(Array('ID' => 'ASC'), array('NAME' => $name, 'IBLOCK_ID' => $iblock));
        $PropID = ($properties_fields = $properties->Fetch()) ? $properties_fields['ID'] : 0;

        (!$PropID && is_array($data)) ? $PropID = $this->setProperty(array_merge($data, array('NAME' => $name, 'IBLOCK_ID' => $iblock)), $prefix) : false;

        return (integer) $PropID;
    }

    /**
     * Создание свойства инфоблока
     *
     * @param array $data Набор полей свойства инфоблока
     * @param boolean|string $prefix Префикс для поля "CODE"
     *
     * @return integer
     */
    function setProperty(array $data, $prefix = false) {
        foreach (['IBLOCK_ID', 'NAME'] as $required) {
            if (!isset($data[$required])) {
                $this->message('Ошибка добавления свойства! Поле "' . $required . '" обязательно для заполнения.');
                $this->viewEnd($data);
            }
        }

        $data['CODE'] = ($data['CODE']) ? $prefix.$data['CODE'] : $this->transliterationText($prefix.$data['NAME'], '_', true);

        if (!$PropID = $this->CIBlockProperty->Add($data)) {
            $this->message('Ошибка добавления свойства!');
            $this->message($this->CIBlockProperty->LAST_ERROR);
            $this->viewEnd($data);
        }

        return (integer) $PropID;
    }
}