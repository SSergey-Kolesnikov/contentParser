# contentParser
Парсер контента для наполнения сайтов.

## Использование
Пользуем файл *functions.php*. При создании новой функции она будет доступна по GET-параметру *action*, к примеру функция:
```php
function example() {
    echo 'HelloWorld';
}
```
будет доступна по url'у `http(s)://ваш_сайт.домен/parser/?action=example`

## Документация
- [Класс **App**](#class_App)
    - [Метод **getHtml**](#method_getHtml) - получение DOM-дерева по url страницы
    - [Метод **transliterationText**](#method_transliterationText) - транслитерация текста с переводом в нижний или верхний регистр
    - [Метод **generatePass**](#method_generatePass) - генерация пароля
    - [Метод **getMETA**](#method_getMETA) - получение основных мета тегов
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
$App->getHtml($route, $required = true, $cache = true, $charset = 'utf-8');
```

### <a name="method_transliterationText"></a> Метод transliterationText
Транслитерация текста с переводом в нижний или верхний регистр.

```php
$App->transliterationText($text, $separator = ' ', $uppercase = false);
```

### <a name="method_generatePass"></a> Метод generatePass
Генерация пароля.

```php
$App->generatePass($length = 10);
```

### <a name="method_getMETA"></a> Метод getMETA
Получение основных мета тегов.

```php
$App->getMETA($html, array $index = [], $clean_text = true);
```

### <a name="method_message"></a> Метод message
Вывод сообщения на экран.

```php
$App->message($arguments = null);
```

### <a name="method_view"></a> Метод view
Вывод переменной на экран.

```php
$App->view($arguments = null);
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

### <a name="method_getPropertyIDFromName"></a> Метод getPropertyIDFromName
Получение ID свойства инфоблока по его названию.

```php
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
```php
$arFields = array(
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->getPropertyIDFromName('Цвет', 4, $arFields, 'OPTION_');
```

### <a name="method_setProperty"></a> Метод setProperty
Создание свойства инфоблока

```php
$uBitrix->setProperty(array $data, $prefix = false);
```

#### Список параметров
- **$data** (array) - [Массив](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "Свойства элементов инфоблока (b_iblock_property)") с данными для создаваемого свойства инфоблока, обязательный параметр;
    - Поле "CODE" транслитерируется автоматически из наименования свойства и приводится к верхнему регистру;
- **$prefix** (boolean|string) - Префикс для поля "CODE", необязательный параметр;

#### Возвращаемые значения
Этот метод возвращает ID свойства (integer) инфоблока.

#### Примеры
```php
$arFields = array(
    'NAME' => 'Цвет',
    'IBLOCK_ID' => 4,
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->setProperty($arFields, 'OPTION_');
```