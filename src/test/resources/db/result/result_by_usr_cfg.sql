PREPARE result_by_usr_cfg (int, int) AS
    SELECT group_id
      FROM user_results
     WHERE group_id = $1
       AND user_id = $2;
