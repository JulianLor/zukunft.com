PREPARE config_by_get (text) AS
    SELECT config_id,
           config_name,
           code_id,
           value
      FROM config
     WHERE code_id = $1;