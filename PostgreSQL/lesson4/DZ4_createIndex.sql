-- Создание частичного B-tree индекса на поле created_at для активных товаров
CREATE INDEX idx_created_at_active ON products (created_at) WHERE active = true;

-- Создание B-tree индекса на поле sort
CREATE INDEX idx_sort ON products (sort);

-- Создание хеш-индекса на поле id (для примера, предположим, что это целочисленный ключ)
CREATE INDEX idx_id_hash ON products USING HASH (id);

-- Создание GIN индекса на поле description (для поиска по тексту)
CREATE INDEX idx_description_gin ON products USING GIN (description gin_trgm_ops);

-- Создание BRIN индекса на временном столбце created_at (для больших таблиц)
CREATE INDEX idx_created_at_brin ON products USING BRIN (created_at);

-- Создание B-tree индекса на поле id
CREATE INDEX idx_id ON products (id);


-- Создание GIST индекса на столбец response_data типа JSONB
CREATE INDEX idx_response_data_gist ON api_responses USING GIST (response_data jsonb_path_ops);
--Установка расширения btree_gist--
CREATE EXTENSION IF NOT EXISTS btree_gist;
-- Установка расширения pg_trgm (необходимо для работы с триграммами)
CREATE EXTENSION IF NOT EXISTS pg_trgm;
-- Проверка доступных методов индексации для JSONB
SELECT am.amname, opc.opcname
FROM pg_am am
JOIN pg_opclass opc ON opc.opcmethod = am.oid
JOIN pg_type t ON t.typname = 'jsonb' AND opc.opcintype = t.oid;
--Создание функции для поиска ключа "error" в JSONB:--
CREATE OR REPLACE FUNCTION jsonb_contains_error(data JSONB)
RETURNS BOOLEAN AS $$
BEGIN
    RETURN jsonb_exists(data, 'error');
END;
$$ LANGUAGE plpgsql IMMUTABLE;
-- Выполнение запроса для поиска записей, содержащих статус "error" в поле response_data
-- Команда EXPLAIN ANALYZE выводит план выполнения запроса для анализа его эффективности
EXPLAIN ANALYZE
SELECT *
FROM api_responses
WHERE response_data @> '{"status": "error"}';



