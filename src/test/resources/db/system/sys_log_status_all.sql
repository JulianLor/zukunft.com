PREPARE sys_log_status_all (int) AS
    SELECT
           sys_log_status_id,
           type_name,
           description,
           code_id
      FROM sys_log_status
     LIMIT $1;