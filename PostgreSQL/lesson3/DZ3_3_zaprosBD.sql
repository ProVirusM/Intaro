--Запрос 1: Рассчитываем среднее время между заказами для каждого клиента
WITH diff_date AS (
    -- Выбираем id клиента и разницу во времени между текущим заказом и следующим заказом
    SELECT 
        ord.customer_id id, 
        lead(ord.created_at) OVER (PARTITION BY ord.customer_id ORDER BY ord.created_at) - ord.created_at diff
    FROM 
        order_c AS ord
)
SELECT 
    diff_date.id "ID клиента", 
    avg(diff_date.diff) "Среднее время между заказами"
FROM 
    diff_date
WHERE
    diff_date.diff IS NOT NULL -- Исключаем NULL значения времени (последний заказ клиента)
GROUP BY
    diff_date.id;

-- Запрос 1:
-- diff_date используется для вычисления временной разницы между последовательными заказами каждого клиента.
-- В основном запросе вычисляется среднее значение разницы времени для каждого клиента, исключая последний заказ клиента, у которого нет следующего.
-- Результат показывает среднее время между заказами для каждого клиента.

--Запрос 2: Рассчитываем количество визитов и заказов для каждого клиента
WITH order_customer_visit AS (
    -- Соединяем таблицы order_c и customer_visit, чтобы выяснить, сколько уникальных визитов и заказов есть у каждого клиента
    SELECT
        order_c.customer_id customer_id,
        order_c.id order_id,
        customer_visit.id visit_id
    FROM
        order_c
        JOIN
        customer_visit
        ON order_c.customer_id = customer_visit.customer_id
)
SELECT
    order_customer_visit.customer_id "ID клиента",
    count(DISTINCT order_customer_visit.visit_id) "Количество визитов",
    count(DISTINCT order_customer_visit.order_id) "Количество заказов"
FROM
    order_customer_visit
GROUP BY
    order_customer_visit.customer_id;

-- Запрос 2:
-- order_customer_visit объединяет таблицы order_c и customer_visit, чтобы подсчитать количество уникальных визитов и заказов для каждого клиента.
-- Основной запрос выводит количество визитов и заказов для каждого клиента, сгруппированные по ID клиента.

--Запрос 3: Сводная информация по источникам трафика (визиты и заказы)
WITH count_visit_table AS (
    -- Подсчитываем количество визитов по каждому источнику трафика
    SELECT
        customer_visit.utm_source utm_source,
        count(customer_visit.id) visit_count
    FROM
        customer_visit
    GROUP BY
        customer_visit.utm_source
), count_order_table AS (
    -- Подсчитываем общее количество заказов, количество оплаченных и выполненных заказов по источникам трафика
    SELECT
        order_c.utm_source,
        count(order_c.id) count_order,
        (
            SELECT 
                count(*) 
            FROM 
                order_c o 
            WHERE 
                o.is_paid = true AND o.utm_source = order_c.utm_source
        ) AS count_is_paid,
        (
            SELECT 
                count(*) 
            FROM 
                order_c o 
            WHERE 
                o.status_id = 1 AND o.utm_source = order_c.utm_source
        ) AS count_status_id
    FROM
        order_c
    GROUP BY
        order_c.utm_source
)
SELECT
    count_order_table.utm_source "Источник трафика",
    count_visit_table.visit_count "Количество визитов",
    count_order_table.count_order "Количество созданных заказов",
    count_order_table.count_is_paid "Количество оплаченных заказов",
    count_order_table.count_status_id "Количество выполненных заказов"
FROM
    count_order_table
    JOIN
    count_visit_table
    ON count_order_table.utm_source = count_visit_table.utm_source;

-- Запрос 3:
-- count_visit_table считает количество визитов по каждому источнику трафика.
-- count_order_table считает общее количество заказов, количество оплаченных и выполненных заказов по источникам трафика.
-- Основной запрос объединяет результаты двух CTE для получения сводной информации по источникам трафика.

--Запрос 4: Анализ производительности менеджеров по выполнению заказов
WITH order_diff AS(
    -- Вычисляем разницу во времени между текущим заказом и следующим заказом того же менеджера
    SELECT
        order_c.manager_id,
        CASE
            WHEN lead(order_c.status_id) OVER (PARTITION BY order_c.manager_id ORDER BY order_c.created_at) != 0
            THEN lead(order_c.created_at) OVER (PARTITION BY order_c.manager_id ORDER BY order_c.created_at) - order_c.created_at
            ELSE NULL
        END diff
    FROM
        order_c
)
SELECT
    order_c.manager_id "ID менеджера",
    avg(order_diff.diff) "Среднее время выполнения заказов",
    ((100/(SELECT count(order_c.id) FROM order_c)::decimal(10, 2)) * count(DISTINCT CASE WHEN order_c.status_id=3 THEN order_c.id END))::decimal(10, 2) "Доля отмененных заказов",
    sum(DISTINCT CASE WHEN order_c.status_id=1 THEN order_c.total_sum END) "Сумма выполненных заказов",
    avg(DISTINCT CASE WHEN order_c.status_id=1 THEN order_c.total_sum END)::decimal(10,2) "Средняя стоимость выполненного заказа"
FROM
    order_c
    JOIN
    order_diff
    ON order_c.manager_id = order_diff.manager_id
GROUP BY
    order_c.manager_id
ORDER BY
    order_c.manager_id;

-- Запрос 4:
-- order_diff вычисляет разницу во времени между текущим заказом и следующим заказом того же менеджера.
-- Основной запрос проводит анализ производительности менеджеров по выполнению заказов, включая среднее время выполнения,
-- долю отмененных заказов, сумму выполненных заказов и среднюю стоимость выполненного заказа.

--Запрос 5: Рейтинг менеджеров на основе различных метрик выполнения заказов

WITH order_diff AS(
    -- order_diff вычисляет разницу во времени между текущим заказом и следующим заказом того же менеджера
    SELECT
        order_c.manager_id,
        CASE
            WHEN lead(order_c.status_id) OVER (PARTITION BY order_c.manager_id ORDER BY order_c.created_at) != 0
            THEN lead(order_c.created_at) OVER (PARTITION BY order_c.manager_id ORDER BY order_c.created_at) - order_c.created_at
            ELSE NULL
        END diff
    FROM
        order_c
)
SELECT
    order_c.manager_id  "ID менеджера",
    (
        -- Рассчитываем процент выполненных заказов
        (100 / count(order_c.id)::decimal(10, 2)) * count(DISTINCT CASE WHEN order_c.status_id = 1 THEN order_c.id END)
        - ( -- Вычитаем процент отмененных заказов
            100 / (SELECT count(*) FROM order_c)::decimal(10, 2) 
            * (SELECT count(CASE WHEN o.status_id = 1 THEN o.id END) FROM order_c o)::decimal(10, 2)
        )
    )::decimal(10, 2)
    + ( -- Добавляем коррекцию на среднее время выполнения заказов по менеджеру
        EXTRACT(EPOCH FROM (avg(order_diff.diff) - (SELECT avg(diff) FROM order_diff)))::decimal(10, 2) / 3600
    )::decimal(10, 2)
    - ( -- Вычитаем процент отмененных заказов по всем заказам
        (100 / count(order_c.id)::decimal(10, 2)) * count(DISTINCT CASE WHEN order_c.status_id = 3 THEN order_c.id END)
        - ( -- Вычитаем процент отмененных заказов по выполненным заказам
            100 / (SELECT count(*) FROM order_c)::decimal(10, 2) 
            * (SELECT count(CASE WHEN o.status_id = 3 THEN o.id END) FROM order_c o)::decimal(10, 2)
        )
    )::decimal(10, 2) "Рейтинг менеджера"
FROM
    order_c
    JOIN
    order_diff
    ON order_c.manager_id = order_diff.manager_id
GROUP BY 
    order_c.manager_id;


-- Запрос 5:
-- order_diff вычисляет разницу во времени между текущим заказом и следующим заказом того же менеджера.
-- Основной запрос вычисляет рейтинг менеджеров на основе различных значений выполнения заказов, включая долю выполненных и отмененных заказов,
-- среднее время выполнения заказов и другие факторы, формирующие общий рейтинг каждого менеджера.
