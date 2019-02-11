# contentParser
������ �������� ��� ���������� ������.

## ������������
- [����� **App**](#class_App)
- [����� **uModx**](#class_uModx)
- [����� **uBitrix**](#class_uBitrix)
    - [����� **getPropertyIDFromName**](#method_getPropertyIDFromName) - ��������� ID �������� ��������� �� ��� ��������
    - [����� **setProperty**](#method_setProperty) - �������� �������� ���������

### <a name="class_App"></a> ����� App

### <a name="class_uModx"></a> ����� uModx

### <a name="class_uBitrix"></a> ����� uBitrix

### <a name="method_getPropertyIDFromName"></a> ����� getPropertyIDFromName
��������� ID �������� ��������� �� ��� ��������.

```
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
```
$arFields = array(
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->getPropertyIDFromName('����', 4, $arFields, 'OPTION_');
```

### <a name="method_setProperty"></a> ����� setProperty
�������� �������� ���������

```
$uBitrix->setProperty(array $data, $prefix = false);
```

#### ������ ����������
- **$data** (array) - [������](https://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty "�������� ��������� ��������� (b_iblock_property)") � ������� ��� ������������ �������� ���������, ������������ ��������;
    - ���� "CODE" ����������������� ������������� �� ������������ �������� � ���������� � �������� ��������;
- **$prefix** (boolean|string) - ������� ��� ���� "CODE", �������������� ��������;

#### ������������ ��������
���� ����� ���������� ID �������� (integer) ���������.

#### �������
```
$arFields = array(
    'NAME' => '����',
    'IBLOCK_ID' => 4,
    'PROPERTY_TYPE' => 'S',
    'SORT' => 100
);
$PropID = $uBitrix->setProperty($arFields, 'OPTION_');
```