
php artisan make:migration add_recap_limit_total_to_users_table --table=users




UPDATE users u
JOIN (
    SELECT ph1.username, ph1.recap_limit
    FROM plan_history ph1
    INNER JOIN (
        SELECT username, MAX(renewed_at) AS max_renewed
        FROM plan_history
        GROUP BY username
    ) latest ON ph1.username = latest.username
             AND ph1.renewed_at = latest.max_renewed
) ph ON ph.username = u.username
SET u.recap_limit_total = ph.recap_limit;