PREPARE value_phrase_link_by_id FROM
    'SELECT value_phrase_link_id, user_id, group_id, phrase_id, weight, link_type_id, condition_formula_id
       FROM value_phrase_links
      WHERE value_phrase_link_id = ?';