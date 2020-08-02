# contentParser
Парсер контента для наполнения сайтов.

## Использование
В корне создаем файл *[имя_файла].php* и он будет доступен по url'у `http(s)://[ваш_сайт].[домен]/parser/?action=[имя_файла]`.

## Документация
- [Класс **App**](#class_App)
    - [Метод **getHtml**](#method_getHtml) - получение DOM-дерева по url страницы
    - [Метод **unbuildTree**](#method_unbuildTree) - Разбор массива в виде иерархического дерева в обычный массив
    - [Метод **transliterationText**](#method_transliterationText) - транслитерация текста с переводом в нижний или верхний регистр
    - [Метод **generatePass**](#method_generatePass) - генерация пароля
    - [Метод **getMETA_SHD**](#method_getMETA_SHD) - получение основных мета тегов
    - [Метод **message**](#method_message) - вывод сообщения на экран
    - [Метод **view**](#method_view) - вывод переменной на экран
- [Класс **uModx**](#class_uModx)
    - [Метод **createResource**](#method_createResource) - создает новый ресурс
    - [Метод **createUser**](#method_createUser) - создает нового пользователя
- [Класс **uBitrix**](#class_uBitrix)
    - [Метод **getPropertyIDFromName**](#method_getPropertyIDFromName) - получение ID свойства инфоблока по его названию
    - [Метод **setProperty**](#method_setProperty) - создание свойства инфоблока

### <a name="class_App"></a> Класс App

### <a name="method_getHtml"></a> Метод getHtml
Получение DOM-дерева по url страницы.

```php
$app->getHtml($route, $required = true, $cache = true, $charset = 'utf-8');
```

### <a name="method_unbuildTree"></a> Метод unbuildTree
Разбор массива в виде иерархического дерева в обычный массив.

```php
$app->unbuildTree(array $tree = [], string $name = 'children', int $level = 0);
```

### <a name="method_transliterationText"></a> Метод transliterationText
Транслитерация текста с переводом в нижний или верхний регистр.

```php
$app->transliterationText($text, $separator = ' ', $uppercase = false);
```

### <a name="method_generatePass"></a> Метод generatePass
Генерация пароля.

```php
$app->generatePass($length = 10);
```

### <a name="method_getMETA_SHD"></a> Метод getMETA_SHD
Получение основных мета тегов.

> Используется библиотека Simple HTML DOM

```php
$app->getMETA_SHD($html, array $index = [], $clean_text = true);
```

### <a name="method_message"></a> Метод message
Вывод сообщения на экран.

```php
$app->message($arguments = null);
```

### <a name="method_view"></a> Метод view
Вывод переменной на экран.

```php
$app->view($arguments = null);
```

### <a name="class_uModx"></a> Класс uModx

### <a name="method_createResource"></a> Метод createResource
Создает новый ресурс.

```php
$uModx->createResource(array $data, $class = 'modDocument', $processor = 'resource/create');
```

### <a name="method_createUser"></a> Метод createUser
Создает нового пользователя.

```php
$uModx->createUser(array $data, array $groupsList = array('Administrator'), $role = 1);
```

### <a name="class_uBitrix"></a> Класс uBitrix
Класс uBitrix наследуется от класса App.

### <a name="method_getPropertyIDFromName"></a> Метод getPropertyIDFromName
Получение ID свойства инфоблока по его названию.

```php
$app->getPropertyIDFromName($name, $iblock = 1, $data = false, $prefix = false);
```

#### Список параметров
- **$name** (string) - Наименование свойства, обязательный параметр;
- **$iblock** (integer) - ID инфоблока свойства, обязательный параметр;
- **$data** (boolean|array) - [Массив](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "Свойства элементов инфоблока (b_iblock_property)") с данными для нового свойства инфоблока, необязательный параметр;
    - В случае передачи [массива](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "Свойства элементов инфоблока (b_iblock_property)") и отсутствии такого свойства у инфоблока - свойство будет создано;
    - Передавать поля "NAME" и "IBLOCK_ID" необязательно;
    - Поле "CODE", в случае если оно не передается в `$data`, транслитерируется автоматически из наименования свойства и приводится к верхнему регистру;
- **$prefix** (boolean|string) - Префикс для поля "CODE", необязательный параметр;

#### Возвращаемые значения
Метод возвращает ID свойства (integer).

#### Примеры
```php
$arFields = array(
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $app->getPropertyIDFromName('Цвет', 4, $arFields, 'OPTION_');
```

### <a name="method_setProperty"></a> Метод setProperty
Создание свойства инфоблока

```php
$app->setProperty(array $data, $prefix = false);
```

#### Список параметров
- **$data** (array) - [Массив](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "Свойства элементов инфоблока (b_iblock_property)") с данными для создаваемого свойства инфоблока, обязательный параметр;
    - Поле "CODE", в случае если оно не передается в `$data`, транслитерируется автоматически из наименования свойства и приводится к верхнему регистру;
- **$prefix** (boolean|string) - Префикс для поля "CODE", необязательный параметр;

#### Возвращаемые значения
Метод возвращает ID свойства (integer).

#### Примеры
```php
$arFields = array(
    'NAME' => 'Цвет',
    'IBLOCK_ID' => 4,
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $app->setProperty($arFields, 'OPTION_');
```