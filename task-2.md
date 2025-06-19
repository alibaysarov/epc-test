# Задача №2

### Технологии
- Язык программирования - PHP 8.2 (Laravel)
- БД - Postgresql + Clickhouse (для аналитических запросов)
- Redis - кеширования данных для частых запросов 
### Таблицы

<pre>
-- Создание таблицы пользователей
CREATE TABLE users (
id BIGSERIAL PRIMARY KEY,
email VARCHAR(255) NOT NULL UNIQUE,
created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы типов действий
CREATE TABLE action_types (
id SERIAL PRIMARY KEY,
name VARCHAR(100) NOT NULL UNIQUE,
description TEXT
);
-- Создание таблицы кампаний
CREATE TABLE companies (
id BIGSERIAL PRIMARY KEY,
name VARCHAR(255) NOT NULL,
budget DECIMAL(15, 2) NOT NULL,
start_date DATE NOT NULL,
end_date DATE,
created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы действий
CREATE TABLE actions (
id BIGSERIAL PRIMARY KEY,
company_id BIGINT NOT NULL,
user_id BIGINT NOT NULL,
action_type_id INT NOT NULL,
cost DECIMAL(10, 2) NOT NULL,
created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (company_id) REFERENCES campaigns(id) ON DELETE CASCADE,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
FOREIGN KEY (action_type_id) REFERENCES action_types(id) ON DELETE RESTRICT


-- Индексы для оптимизации запросов
CREATE INDEX idx_actions_company_id ON actions(company_id);
CREATE INDEX idx_actions_user_id ON actions(user_id);
CREATE INDEX idx_actions_created_at ON actions(created_at);


-- Таблица для аналитики в ClickHouse
CREATE TABLE campaign_stats (
    campaign_id UInt64,
    action_type_id UInt32,
    action_date Date,
    total_actions UInt64,
    total_cost Decimal(15, 2)
) ENGINE = MergeTree()
ORDER BY (campaign_id, action_date);
</pre>

### Запросы
Вывод статистики кликов по компаниям
<pre>
SELECT
    campaign_id,
    SUM(total_actions) AS click_count
FROM campaign_stats
WHERE action_type_id = 1
GROUP BY campaign_id
ORDER BY click_count DESC;
</pre>

Вывод времени когда число кликов для каждой кампании максимальное
<pre>
SELECT
    campaign_id,
    toStartOfHour(action_date) AS action_hour,
    SUM(total_actions) AS click_count
FROM campaign_stats
WHERE action_type_id = 1
GROUP BY campaign_id, action_hour
HAVING click_count = (
    SELECT MAX(click_count)
    FROM (
        SELECT
            campaign_id,
            toStartOfHour(action_date) AS action_hour,
            SUM(total_actions) AS click_count
        FROM campaign_stats
        WHERE action_type_id = 1
        GROUP BY campaign_id, action_hour
    ) t
    WHERE t.campaign_id = campaign_stats.campaign_id
)
ORDER BY click_count DESC;

</pre>