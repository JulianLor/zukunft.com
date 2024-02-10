PREPARE value_std_by_id (text) AS
    SELECT group_id,
           numeric_value,
           source_id,
           last_update,
           excluded,
           protect_id,
           user_id
    FROM values
    WHERE group_id = $1;