-- Создание таблицы customer_visit для хранения информации о визитах клиентов
CREATE TABLE customer_visit (
    id SERIAL PRIMARY KEY NOT NULL, -- Уникальный идентификатор визита
    customer_id INTEGER NOT NULL, -- Идентификатор клиента
    created_at TIMESTAMP NOT NULL, -- Дата и время визита
    visit_length INTEGER NOT NULL, -- Длительность визита в секундах
    landing_page VARCHAR(255) NOT NULL, -- Страница входа
    exit_page VARCHAR(255) NOT NULL, -- Страница выхода
    utm_source VARCHAR(255) NOT NULL -- Источник трафика
);

-- Создание таблицы customer_visit_page для хранения информации о страницах, посещенных во время визитов
CREATE TABLE customer_visit_page (
    id SERIAL PRIMARY KEY NOT NULL, -- Уникальный идентификатор записи о странице визита
    visit_id INTEGER NOT NULL, -- Идентификатор визита (ссылка на таблицу customer_visit)
    page VARCHAR(255) NOT NULL, -- URL страницы
    time_on_page INTEGER NOT NULL, -- Время, проведенное на странице в секундах
    FOREIGN KEY (visit_id) REFERENCES customer_visit (id) -- Внешний ключ для связи с таблицей customer_visit
);

-- Вставка данных в таблицу customer_visit
INSERT INTO customer_visit
    (customer_id, created_at, visit_length, landing_page, exit_page, utm_source)
VALUES
    (1, '2024-04-17 13:00:00', 200, 'http.com/welcome', 'site.com/main', 'Google adds'),
    (2, '2024-04-16 11:00:00', 500, 'http.com/news', 'site.com/profile', 'VK adds'),
    (3, '2024-04-18 12:00:00', 180, 'http.com/main', 'site.com/news', 'Yandex adds'),
    (1, '2024-04-18 09:00:00', 260, 'http.com/profile', 'site.com/news', 'VK adds'),
    (2, '2024-04-17 18:00:00', 660, 'http.com/welcome', 'site.com/profile', 'Google adds'),
    (3, '2024-04-18 20:00:00', 860, 'http.com/news', 'site.com/main', 'Google adds'),
    (1, '2024-04-19 12:50:00', 200, 'http.com/welcome', 'site.com/main', 'Google adds'),
    (2, '2024-04-20 11:20:00', 500, 'http.com/news', 'site.com/main', 'Google adds'),
    (3, '2024-04-21 16:00:00', 180, 'http.com/main', 'site.com/main', 'Google adds');

-- Вставка данных в таблицу customer_visit_page
INSERT INTO customer_visit_page
    (visit_id, page, time_on_page)
VALUES
    (1, 'http.com/welcome', 120),
    (1, 'http.com/main', 70),
    (2, 'http.com/news', 130),
    (2, 'http.com/main', 120),
    (2, 'http.com/profile', 90),
    (3, 'http.com/main',75),
    (3, 'http.com/news', 120),
    (4, 'http.com/profile', 84),
    (4, 'http.com/main', 48),
    (4, 'http.com/news', 72),
    (5, 'http.com/welcome', 80),
    (5, 'http.com/news', 250),
    (5, 'http.com/profile', 70),
    (6, 'http.com/profile', 550),
    (6, 'http.com/main', 80),
    (7, 'http.com/welcome', 80),
    (7, 'http.com/main', 80),
    (8, 'http.com/news', 280),
    (8, 'http.com/main', 180),
    (8, 'http.com/profile', 120),
    (9, 'http.com/main', 100),
    (9, 'http.com/news', 100);

-- Создание таблицы customer для хранения информации о клиентах
CREATE TABLE customer (
    id SERIAL PRIMARY KEY NOT NULL, -- Уникальный идентификатор клиента
    created_at TIMESTAMP NOT NULL, -- Дата и время создания записи
    first_name VARCHAR(64) NOT NULL, -- Имя клиента
    last_name VARCHAR(64) NOT NULL, -- Фамилия клиента
    phone CHAR(10) NOT NULL, -- Телефон клиента
    email VARCHAR(64) NOT NULL -- Электронная почта клиента
);

-- Создание таблицы order_c для хранения информации о заказах клиентов
CREATE TABLE order_c (
    id SERIAL PRIMARY KEY NOT NULL, -- Уникальный идентификатор заказа
    created_at TIMESTAMP NOT NULL, -- Дата и время создания заказа
    customer_id INTEGER NOT NULL, -- Идентификатор клиента (ссылка на таблицу customer)
    manager_id INTEGER NOT NULL, -- Идентификатор менеджера, который обработал заказ
    status_id INTEGER NOT NULL, -- Идентификатор статуса заказа
    is_paid BOOLEAN NOT NULL, -- Флаг оплаты заказа
    total_sum INTEGER NOT NULL, -- Общая сумма заказа
    utm_source VARCHAR(255) NOT NULL -- Источник трафика заказа
);

-- Вставка данных в таблицу customer
INSERT INTO customer 
    (created_at, first_name, last_name, phone, email)
VALUES
    ('2023-09-07 07:00:00', 'Даниил', ' Морозов', '8888888888', 'dms@gmail.com'),
    ('2023-06-04 10:00:00', 'Денис', ' Денисов', '9999999999', 'dde@gmail.com'),
    ('2024-12-12 12:00:00', 'Иван', ' Григорьев', '7777777777', 'ign@gmail.com');

-- Вставка данных в таблицу order_c
INSERT INTO order_c
    (created_at, customer_id, manager_id, status_id, is_paid, total_sum, utm_source)
VALUES
    ('2024-06-04 10:00:00', 1, 2, 2, true, 700, 'Google adds'),
    ('2024-06-04 11:01:00', 2, 3, 3, false, 200, 'Telegram adds'),
    ('2024-06-05 12:02:00', 3, 2, 3, false, 700, 'Yandex adds'),
    ('2024-06-05 09:00:00', 1, 2, 1, false, 500, 'Telegram adds'),
    ('2024-06-06 08:59:00', 2, 3, 1, true, 400, 'Google adds'),
    ('2024-06-07 17:58:00', 3, 1, 0, true, 250, 'VK adds'),
    ('2024-06-08 12:03:00', 1, 3, 1, true, 700, 'Google adds'),
    ('2024-06-08 13:00:00', 2, 1, 0, false, 300, 'VK adds'),
    ('2024-04-09 15:00:00', 1, 2, 1, false, 800, 'Yandex adds');
