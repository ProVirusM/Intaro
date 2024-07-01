-- Выполнение запроса №1 с индексом на id
EXPLAIN ANALYZE
SELECT *
FROM products
WHERE active = true AND id = 4;
-- Этот запрос ищет активный продукт с id = 4
-- Используется индекс на поле id и частичный индекс на поле active

-- Выполнение запроса №2 с индексом на sort
EXPLAIN ANALYZE
SELECT id, code, name, price
FROM products
WHERE active = true
ORDER BY sort
LIMIT 10000;
-- Этот запрос выбирает 10000 активных продуктов, отсортированных по полю sort
-- Используется индекс на поле sort для ускорения сортировки

-- Выполнение запроса №3 с индексом на price
EXPLAIN ANALYZE
SELECT id, code, name, price
FROM products
WHERE active = true
ORDER BY price DESC
LIMIT 10000;
-- Этот запрос выбирает 10000 активных продуктов, отсортированных по полю price в убывающем порядке
-- Индекс на поле price ускоряет сортировку по цене

-- Выполнение запроса №4 с индексом на id
EXPLAIN ANALYZE
SELECT id, code, name, price
FROM products
WHERE active = true AND id IN (1, 5, 7, 8, 10, 34)
ORDER BY sort
LIMIT 10000;
-- Этот запрос выбирает активные продукты с определенными id и сортирует их по полю sort
-- Используется индекс на поле id для поиска по списку id и индекс на поле sort для сортировки

-- Выполнение запроса №5 с индексом на description
EXPLAIN ANALYZE
SELECT id, code, name, price
FROM products
WHERE active = true AND name LIKE 'Product%'
LIMIT 10000;
-- Этот запрос выбирает 10000 активных продуктов, название которых начинается с 'Product'
-- Используется индекс на поле description для ускорения поиска по тексту

-- Выполнение запроса №6 с частичным индексом на created_at
EXPLAIN ANALYZE
SELECT *
FROM products
WHERE active = true AND created_at > '2023-06-01 00:00:00'
LIMIT 10000;
-- Этот запрос выбирает 10000 активных продуктов, созданных после определенной даты
-- Используется частичный индекс на поле created_at для ускорения поиска по временным диапазонам

-- Выполнение запроса на таблице api_responses с индексом на JSONB поле
EXPLAIN ANALYZE
SELECT *
FROM api_responses
WHERE response_data @> '{"status": "error"}';
-- Этот запрос выбирает записи, где в поле response_data содержится JSON с ключом "status" и значением "error"
-- Используется GIST индекс на поле response_data для ускорения поиска по JSONB данным
