PREPARE config_by_get FROM
   'SELECT config_id,
           config_name,
           code_id,
           `value`
      FROM config
     WHERE code_id = ?';