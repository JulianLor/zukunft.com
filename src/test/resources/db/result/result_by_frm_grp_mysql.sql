PREPARE result_by_frm_grp FROM
   'SELECT group_id,
           formula_id,
           user_id,
           source_group_id,
           result,
           last_update
      FROM results
     WHERE formula_id = ?
       AND group_id = ?';