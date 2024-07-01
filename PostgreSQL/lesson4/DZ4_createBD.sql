-- Создание таблицы products
CREATE TABLE products (
    id SERIAL PRIMARY KEY,               -- Уникальный идентификатор, автоматически увеличивается
    created_at TIMESTAMP,                -- Время создания продукта
    active BOOLEAN,                      -- Статус активности продукта (TRUE или FALSE)
    sort INTEGER,                        -- Поле для сортировки
    price NUMERIC,                       -- Цена продукта
    code CHAR(10),                       -- Код продукта (длина 10 символов)
    name VARCHAR(100),                   -- Название продукта (максимальная длина 100 символов)
    description VARCHAR(255)             -- Описание продукта (максимальная длина 255 символов)
);

-- Добавление 10,000 записей в таблицу products
INSERT INTO products (created_at, active, sort, price, code, name, description)
SELECT
    NOW() - INTERVAL '1 year' * random() AS created_at,  -- Время создания, случайно выбирается в течение последнего года
    random() > 0.5 AS active,                           -- Случайное значение для активности (50% вероятность TRUE)
    random() * 1000 AS sort,                            -- Случайное значение для сортировки (от 0 до 1000)
    random() * 1000 AS price,                           -- Случайное значение для цены (от 0 до 1000)
    substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' FROM (random() * 62 + 1)::integer FOR 10) AS code,  -- Случайный 10-символьный код
    'Product ' || generate_series AS name,              -- Название продукта, например, "Product 1", "Product 2" и т.д.
    'Description for product ' || generate_series AS description  -- Описание продукта, например, "Description for product 1"
FROM generate_series(1, 10000);                         -- Генерация 10,000 записей

-- Создание таблицы api_responses
CREATE TABLE api_responses (
    id SERIAL PRIMARY KEY,               -- Уникальный идентификатор, автоматически увеличивается
    response_data JSONB                  -- Поле для хранения данных в формате JSONB
);

-- Вставляем 10000 случайных записей в таблицу api_responses
WITH random_data AS (
  SELECT
    CASE WHEN random() < 0.5 THEN         -- 50% вероятность успешного ответа
      jsonb_build_object(
        'status', 'success',              -- Статус успешного ответа
        'data', jsonb_build_object('id', (floor(random() * 1000) + 1)::int, 'name', 'Product ' || (floor(random() * 1000) + 1)::text)  -- Случайные данные продукта
      )
    ELSE
      jsonb_build_object(
        'status', 'error',                -- Статус ошибки
        'error_message', 'Error occurred' -- Сообщение об ошибке
      )
    END AS response_data
  FROM generate_series(1, 10000)          -- Генерация 10,000 записей
)
INSERT INTO api_responses (response_data)
SELECT response_data
FROM random_data;                         -- Вставка сгенерированных данных в таблицу api_responses
