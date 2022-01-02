PREPARE formula_value_list_by_formula_id (int) AS
    SELECT formula_value_id,
           formula_id,
           user_id,
           source_phrase_group_id,
           source_time_id,
           phrase_group_id,
           time_word_id,
           formula_value,
           last_update,
           dirty
      FROM formula_values
     WHERE formula_id = $1;