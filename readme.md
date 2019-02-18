# contentParser
������ �������� ��� ���������� ������.

## �������������
�������� ���� *functions.php*. ��� �������� ����� ������� ��� ����� �������� �� GET-��������� *action*, � ������� �������:
```php
function example() {
    echo 'HelloWorld';
}
```
����� �������� �� url'� `http(s)://���_����.�����/parser/?action=example`

## ������������
- [����� **App**](#class_App)
    - [����� **getHtml**](#method_getHtml) - ��������� DOM-������ �� url ��������
    - [����� **transliterationText**](#method_transliterationText) - �������������� ������ � ��������� � ������ ��� ������� �������
    - [����� **generatePass**](#method_generatePass) - ��������� ������
    - [����� **getMETA**](#method_getMETA) - ��������� �������� ���� �����
    - [����� **message**](#method_message) - ����� ��������� �� �����
    - [����� **view**](#method_view) - ����� ���������� �� �����
- [����� **uModx**](#class_uModx)
    - [����� **createResource**](#method_createResource) - ������� ����� ������
    - [����� **createUser**](#method_createUser) - ������� ������ ������������
- [����� **uBitrix**](#class_uBitrix)
    - [����� **getPropertyIDFromName**](#method_getPropertyIDFromName) - ��������� ID �������� ��������� �� ��� ��������
    - [����� **setProperty**](#method_setProperty) - �������� �������� ���������

### <a name="class_App"></a> ����� App

### <a name="method_getHtml"></a> ����� getHtml
��������� DOM-������ �� url ��������.

```php
$App->getHtml($route, $required = true, $cache = true, $charset = 'utf-8');
```

### <a name="method_transliterationText"></a> ����� transliterationText
�������������� ������ � ��������� � ������ ��� ������� �������.

```php
$App->transliterationText($text, $separator = ' ', $uppercase = false);
```

### <a name="method_generatePass"></a> ����� generatePass
��������� ������.

```php
$App->generatePass($length = 10);
```

### <a name="method_getMETA"></a> ����� getMETA
��������� �������� ���� �����.

```php
$App->getMETA($html, array $index = [], $clean_text = true);
```

### <a name="method_message"></a> ����� message
����� ��������� �� �����.

```php
$App->message($arguments = null);
```

### <a name="method_view"></a> ����� view
����� ���������� �� �����.

```php
$App->view($arguments = null);
```

### <a name="class_uModx"></a> ����� uModx

### <a name="method_createResource"></a> ����� createResource
������� ����� ������.

```php
$uModx->createResource(array $data, $class = 'modDocument', $processor = 'resource/create');
```

### <a name="method_createUser"></a> ����� createUser
������� ������ ������������.

```php
$uModx->createUser(array $data, array $groupsList = array('Administrator'), $role = 1);
```

### <a name="class_uBitrix"></a> ����� uBitrix

### <a name="method_getPropertyIDFromName"></a> ����� getPropertyIDFromName
��������� ID �������� ��������� �� ��� ��������.

```php
$uBitrix->getPropertyIDFromName($name, $iblock = 1, $data = false, $prefix = false);
```

#### ������ ����������
- **$name** (string) - ������������ ��������, ������������ ��������;
- **$iblock** (integer) - ID ��������� ��������, ������������ ��������;
- **$data** (boolean|array) - [������](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "�������� ��������� ��������� (b_iblock_property)") � ������� ��� ������ �������� ���������, �������������� ��������;
    - � ������ �������� [�������](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "�������� ��������� ��������� (b_iblock_property)") � ���������� ������ �������� � ��������� - �������� ����� �������;
    - ���������� ���� "NAME" � "IBLOCK_ID" �������������;
    - ���� "CODE" ����������������� ������������� �� ������������ �������� � ���������� � �������� ��������;
- **$prefix** (boolean|string) - ������� ��� ���� "CODE", �������������� ��������;

#### ������������ ��������
���� ����� ���������� ID �������� (integer) ���������.

#### �������
```php
$arFields = array(
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->getPropertyIDFromName('����', 4, $arFields, 'OPTION_');
```

### <a name="method_setProperty"></a> ����� setProperty
�������� �������� ���������

```php
$uBitrix->setProperty(array $data, $prefix = false);
```

#### ������ ����������
- **$data** (array) - [������](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "�������� ��������� ��������� (b_iblock_property)") � ������� ��� ������������ �������� ���������, ������������ ��������;
    - ���� "CODE" ����������������� ������������� �� ������������ �������� � ���������� � �������� ��������;
- **$prefix** (boolean|string) - ������� ��� ���� "CODE", �������������� ��������;

#### ������������ ��������
���� ����� ���������� ID �������� (integer) ���������.

#### �������
```php
$arFields = array(
    'NAME' => '����',
    'IBLOCK_ID' => 4,
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->setProperty($arFields, 'OPTION_');
```