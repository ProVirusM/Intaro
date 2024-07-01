-- Создание таблицы с объектами
CREATE TABLE TableName (
    ID SERIAL PRIMARY KEY, -- Поле идентификатора с автоинкрементом и первичным ключом
    ObjectName VARCHAR(100) -- Поле для хранения названия объекта
);

-- Создание таблицы с событиями
CREATE TABLE SportEvents (
    EventID SERIAL PRIMARY KEY, -- Поле идентификатора события с автоинкрементом и первичным ключом
    EventName VARCHAR(100), -- Поле для хранения названия события
    ObjectID INT, -- Поле для хранения идентификатора объекта (внешний ключ)
    CONSTRAINT FK_ObjectID FOREIGN KEY (ObjectID) REFERENCES TableName(ID) -- Установка внешнего ключа, связывающего с таблицей TableName
);

-- Создание таблицы с транзакциями
CREATE TABLE Transactions (
    TransactionID SERIAL PRIMARY KEY, -- Поле идентификатора транзакции с автоинкрементом и первичным ключом
    TransactionDate DATE, -- Поле для хранения даты транзакции
    Amount DECIMAL(10,2), -- Поле для хранения суммы транзакции
    EventType VARCHAR(50), -- Поле для хранения типа события
    EventID INT, -- Поле для хранения идентификатора события (внешний ключ)
    CONSTRAINT FK_EventID FOREIGN KEY (EventID) REFERENCES SportEvents(EventID) -- Установка внешнего ключа, связывающего с таблицей SportEvents
);

-- Заполнение таблицы с объектами (TableName)
INSERT INTO TableName (ObjectName) VALUES
    ('Object1'), ('Object2'), ('Object3'), ('Object4'), ('Object5'),
    ('Object6'), ('Object7'), ('Object8'), ('Object9'), ('Object10'),
    ('Object11'), ('Object12'), ('Object13'), ('Object14'), ('Object15'),
    ('Object16'), ('Object17'), ('Object18'), ('Object19'), ('Object20');


-- Заполнение таблицы с событиями (SportEvents)
INSERT INTO SportEvents (EventName, ObjectID) VALUES
    ('Event1', 1), ('Event2', 2), ('Event3', 3), ('Event4', 4), ('Event5', 5),
    ('Event6', 6), ('Event7', 7), ('Event8', 8), ('Event9', 9), ('Event10', 10),
    ('Event11', 11), ('Event12', 12), ('Event13', 13), ('Event14', 14), ('Event15', 15),
    ('Event16', 16), ('Event17', 17), ('Event18', 18), ('Event19', 19), ('Event20', 20);


-- Вставка 20 транзакций в таблицу Transactions, связанных с событиями из SportEvents
INSERT INTO Transactions (TransactionDate, Amount, EventType, EventID) VALUES
    ('2024-04-01', 100.00, 'Type1', 1), ('2024-04-02', 200.00, 'Type2', 2), ('2024-04-03', 150.00, 'Type1', 3),
    ('2024-04-04', 300.00, 'Type2', 4), ('2024-04-05', 50.00, 'Type1', 5), ('2024-04-06', 120.00, 'Type2', 6),
    ('2024-04-07', 180.00, 'Type1', 7), ('2024-04-08', 90.00, 'Type2', 8), ('2024-04-09', 75.00, 'Type1', 9),
    ('2024-04-10', 250.00, 'Type2', 10), ('2024-04-11', 80.00, 'Type1', 11), ('2024-04-12', 110.00, 'Type2', 12),
    ('2024-04-13', 200.00, 'Type1', 13), ('2024-04-14', 140.00, 'Type2', 14), ('2024-04-15', 160.00, 'Type1', 15),
    ('2024-04-16', 190.00, 'Type2', 16), ('2024-04-17', 220.00, 'Type1', 17), ('2024-04-18', 130.00, 'Type2', 18),
    ('2024-04-19', 170.00, 'Type1', 19), ('2024-04-20', 210.00, 'Type2', 20);


-- Нумерация строк в таблице SportEvents
WITH NumberedRows AS (
    SELECT ROW_NUMBER() OVER () AS RowNumber, * FROM SportEvents
)
SELECT * FROM NumberedRows;
-- Использование ROW_NUMBER() для нумерации строк в таблице SportEvents

-- Нумерация строк в каждой группе (например, по EventName)
WITH NumberedGroups AS (
    SELECT *, ROW_NUMBER() OVER (PARTITION BY EventName ORDER BY EventID) AS RowNumber FROM SportEvents
)
SELECT * FROM NumberedGroups;
-- Использование ROW_NUMBER() с PARTITION BY для нумерации строк в каждой группе событий

-- Составление таблицы транзакций с отражением номера операции, суммы и конечного баланса
WITH TransactionDetails AS (
    SELECT *, SUM(Amount) OVER (ORDER BY TransactionDate) AS RunningTotal FROM Transactions
)
SELECT ROW_NUMBER() OVER () AS OperationNumber, TransactionID, Amount, RunningTotal FROM TransactionDetails;
-- Использование SUM() OVER для вычисления итогового баланса и ROW_NUMBER() для нумерации операций

-- Дополнение таблицы с транзакциями дополнительными столбцами (например, процент от общей суммы)
WITH TransactionDetails AS (
    SELECT *, SUM(Amount) OVER (ORDER BY TransactionDate) AS RunningTotal FROM Transactions
)
SELECT *, (Amount / RunningTotal) * 100 AS Percent FROM TransactionDetails;
-- Вычисление процента каждой транзакции от общего баланса с помощью SUM() OVER и расчета процента

-- Модификация запроса из п.4 с использованием WINDOW для одинаковых выражений
WITH TransactionDetails AS (
    SELECT *, SUM(Amount) OVER w AS RunningTotal FROM Transactions WINDOW w AS (ORDER BY TransactionDate)
)
SELECT *, (Amount / RunningTotal) * 100 AS Percent FROM TransactionDetails;


-- Отфильтровывание результатов запроса из пункта 5 (оставить транзакции, при которых баланс становится больше 2000)
WITH TransactionDetails AS (
    SELECT *, SUM(Amount) OVER w AS RunningTotal FROM Transactions WINDOW w AS (ORDER BY TransactionDate)
)
SELECT * FROM (
    SELECT *, (Amount / RunningTotal) * 100 AS Percent FROM TransactionDetails
) AS FilteredResults
WHERE RunningTotal > 2000;
-- Фильтрация транзакций, где общий баланс превышает 2000, используя подзапросы и SUM() OVER
