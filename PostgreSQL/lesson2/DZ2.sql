-- Создание типа ENUM с возможными значениями 'Кот', 'Собака', 'Лошадь'
CREATE TYPE ENUMTYPE AS ENUM('Кот', 'Собака', 'Лошадь');

-- Создание составного типа данных с полями INT и VARCHAR
CREATE TYPE CompositeType AS (
    Field1 INT,
    Field2 VARCHAR(50)
);

-- Создание таблицы ZADANIE2 с различными типами данных
CREATE TABLE ZADANIE2 (
	serialFiled SERIAL not null, -- Автоматическое увеличение идентификатора (первичный ключ)
	varcharField VARCHAR(50), -- Поле текстового типа
    booleanField BOOLEAN, -- Логическое поле (истина/ложь)
    dateTimeField TIMESTAMP, -- Поле для хранения метки времени
	enumField ENUMTYPE, -- Поле типа ENUM (перечисление)
	arrayField INT[10], -- Поле для хранения массива целых чисел
	xmlField XML, -- Поле для хранения XML данных
    jsonField JSON, -- Поле для хранения JSON данных
	compositeField CompositeType, -- Поле составного типа данных
	moneyField MONEY, -- Поле для хранения денежных значений
    binaryField BYTEA, -- Поле для хранения бинарных данных
    geometryField POINT, -- Поле для хранения геометрических точек
    bitStringField BIT VARYING(10), -- Поле для хранения битовых строк переменной длины
    uuidField UUID, -- Поле для хранения UUID
	constraint PK_SERIALFiled primary key (serialFiled) -- Установка первичного ключа на поле serialFiled
);

-- Создание уникального индекса на поле serialFiled
create unique index SerialFiled on ZADANIE2 (serialFiled);

-- Вставка данных с использованием функции ST_GeomFromText()
INSERT INTO ZADANIE2 (varcharField, booleanField, dateTimeField, enumField, arrayField, xmlField, jsonField, compositeField, moneyField, binaryField, geometryField, bitStringField, uuidField)
VALUES 
    ('значение1', true, CURRENT_TIMESTAMP, 'Кот'::ENUMTYPE, '{1,2,3}'::INT[], XML '<xml>some data</xml>', '{"key": "value"}'::JSON, ROW(1, 'field2_value')::CompositeType, '$100.50'::MONEY, E'\\x012345'::BYTEA, POINT(0,0), B'101010', '123e4567-e89b-12d3-a456-426614174000'::UUID),
    ('значение2', false, CURRENT_TIMESTAMP, 'Собака'::ENUMTYPE, '{4,5,6}'::INT[], XML '<xml>other data</xml>', '{"key": "other_value"}'::JSON, ROW(2, 'other_field2_value')::CompositeType, '$200.75'::MONEY, E'\\xabcdef'::BYTEA, POINT(1, 1), B'110011', '123e4567-e89b-12d3-a456-426614174001'::UUID),
    ('значение3', true, CURRENT_TIMESTAMP, 'Лошадь'::ENUMTYPE, '{7,8,9}'::INT[], XML '<xml>more data</xml>', '{"key": "more_value"}'::JSON, ROW(3, 'more_field2_value')::CompositeType, '$300.99'::MONEY, E'\\x00ff00'::BYTEA, POINT(2, 2), B'001100', '123e4567-e89b-12d3-a456-426614174002'::UUID);
-- Вставка данных с разными значениями в различные типы полей, включая ENUM, массивы, XML, JSON, составные типы и геометрические точки

-- Запросы на выборку данных из таблицы ZADANIE2

-- Выборка записи, где varcharField равно 'значение1'
SELECT * FROM ZADANIE2 WHERE varcharField = 'значение1';

-- Выборка всех записей, где booleanField равно true
SELECT * FROM ZADANIE2 WHERE booleanField = true;

-- Выборка всех записей, где dateTimeField больше '2024-03-01'
SELECT * FROM ZADANIE2 WHERE dateTimeField > '2024-03-01';

-- Выборка всех записей, где enumField равно 'Кот'
SELECT * FROM ZADANIE2 WHERE enumField = 'Кот';

-- Выборка всех записей, где arrayField содержит значение 1
SELECT * FROM ZADANIE2 WHERE arrayField @> '{1}';

-- Выборка всех записей, где xmlField равно '<xml>some data</xml>'
SELECT * FROM ZADANIE2 WHERE xmlField::TEXT = '<xml>some data</xml>';

-- Выборка всех записей, где значение ключа 'key' в jsonField равно 'value'
SELECT * FROM ZADANIE2 WHERE jsonField->>'key' = 'value';

-- Выборка всех записей, где compositeField равно составному значению (1, 'field2_value')
SELECT * FROM ZADANIE2 WHERE compositeField = ROW(1, 'field2_value')::CompositeType;

-- Выборка всех записей, где moneyField равно '$200.75'
SELECT * FROM ZADANIE2 WHERE moneyField = '$200.75'::MONEY;

-- Выборка всех записей, где binaryField равно бинарному значению E'\\x012345'
SELECT * FROM ZADANIE2 WHERE binaryField = E'\\x012345'::BYTEA;

-- Выборка всех записей, где geometryField равно точке (0,0)
SELECT * FROM ZADANIE2 WHERE geometryField::TEXT = '(0,0)';

-- Выборка всех записей, где bitStringField равно B'101010'
SELECT * FROM ZADANIE2 WHERE bitStringField = B'101010';

-- Выборка всех записей, где uuidField равно '123e4567-e89b-12d3-a456-426614174000'
SELECT * FROM ZADANIE2 WHERE uuidField = '123e4567-e89b-12d3-a456-426614174000'::UUID;
