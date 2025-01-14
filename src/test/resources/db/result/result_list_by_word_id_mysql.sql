PREPARE result_list_by_word_id FROM
   'SELECT s.group_id,
           s.formula_id,
           s.user_id,
           s.source_group_id,
           s.numeric_value,
           s.last_update,
           l.group_id
      FROM results s
 LEFT JOIN group_links l ON s.group_id = l.group_id
     WHERE l.word_id = ?';