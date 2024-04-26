SELECT
    visited_on,
    ROUND(avg_time_spent, 4) AS avg_time_spent
FROM
    (
        SELECT
            t.visited_on,
            AVG(t.time_spent) OVER (ORDER BY t.visited_on ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) AS avg_time_spent
        FROM
            traffic t, users u
        WHERE
            u.user_type = 'user' and u.id = t.user_id
    ) AS user_traffic;
