PREPARE formula_link_std_by_id FROM
   'SELECT formula_link_id,
           formula_id,
           phrase_id,
           user_id,
           link_type_id,
           excluded
    FROM formula_links
    WHERE formula_link_id = ?';