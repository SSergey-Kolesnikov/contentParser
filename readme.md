# contentParser
Парсер контента для наполнения сайтов.

## Документация
- [Класс **App**](#class_App)
- [Класс **uModx**](#class_uModx)
- [Класс **uBitrix**](#class_uBitrix)
    - [Метод **getPropertyIDFromName**](#method_getPropertyIDFromName) - получение ID свойства инфоблока по его названию
    - [Метод **setProperty**](#method_setProperty) - создание свойства инфоблока

### <a name="class_App"></a> Класс App

### <a name="class_uModx"></a> Класс uModx

### <a name="class_uBitrix"></a> Класс uBitrix

### <a name="method_getPropertyIDFromName"></a> Метод getPropertyIDFromName
Получение ID свойства инфоблока по его названию.

```
$uBitrix->getPropertyIDFromName($name, $iblock = 1, $data = false, $prefix = false);
```

#### Список параметров
- **$name** (string) - Наименование свойства, обязательный параметр;
- **$iblock** (integer) - ID инфоблока свойства, обязательный параметр;
- **$data** (boolean|array) - [Массив](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "Свойства элементов инфоблока (b_iblock_property)") с данными для нового свойства инфоблока, необязательный параметр;
    - В случае передачи [массива](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "Свойства элементов инфоблока (b_iblock_property)") и отсутствии такого свойства у инфоблока - свойство будет создано;
    - Передавать поля "NAME" и "IBLOCK_ID" необязательно;
    - Поле "CODE" транслитерируется автоматически из наименования свойства и приводится к верхнему регистру;
- **$prefix** (boolean|string) - Префикс для поля "CODE", необязательный параметр;

#### Возвращаемые значения
Этот метод возвращает ID свойства (integer) инфоблока.

#### Примеры
```
$arFields = array(
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->getPropertyIDFromName('Цвет', 4, $arFields, 'OPTION_');
```

### <a name="method_setProperty"></a> Метод setProperty
Создание свойства инфоблока

```
$uBitrix->setProperty(array $data, $prefix = false);
```

#### Список параметров
- **$data** (array) - [Массив](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "Свойства элементов инфоблока (b_iblock_property)") с данными для создаваемого свойства инфоблока, обязательный параметр;
    - Поле "CODE" транслитерируется автоматически из наименования свойства и приводится к верхнему регистру;
- **$prefix** (boolean|string) - Префикс для поля "CODE", необязательный параметр;

#### Возвращаемые значения
Этот метод возвращает ID свойства (integer) инфоблока.

#### Примеры
```
$arFields = array(
    'NAME' => 'Цвет',
    'IBLOCK_ID' => 4,
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->setProperty($arFields, 'OPTION_');
```