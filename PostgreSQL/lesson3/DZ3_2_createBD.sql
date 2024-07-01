-- Создание таблицы для хранения информации о визитах клиентов
CREATE TABLE customer_visit (
    id SERIAL PRIMARY KEY NOT NULL, -- Первичный ключ, автоматически увеличивается
    customer_id INTEGER NOT NULL, -- Идентификатор клиента
    created_at TIMESTAMP NOT NULL, -- Время создания записи о визите
    visit_length INTEGER NOT NULL, -- Длительность визита в секундах
    landing_page VARCHAR(255) NOT NULL, -- Страница, с которой начался визит
    exit_page VARCHAR(255) NOT NULL, -- Страница, на которой закончился визит
    utm_source VARCHAR(255) NOT NULL -- Источник трафика UTM-метка
);

-- Создание таблицы для хранения информации о посещенных страницах визита
CREATE TABLE customer_visit_page (
    id SERIAL PRIMARY KEY NOT NULL, -- Первичный ключ, автоматически увеличивается
    visit_id INTEGER NOT NULL, -- Идентификатор визита, внешний ключ к таблице customer_visit
    page VARCHAR(255) NOT NULL, -- URL страницы, которую посетил клиент
    time_on_page INTEGER NOT NULL, -- Время, проведенное на этой странице в секундах
    FOREIGN KEY (visit_id) REFERENCES customer_visit (id) -- Установка внешнего ключа для связи с таблицей customer_visit
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