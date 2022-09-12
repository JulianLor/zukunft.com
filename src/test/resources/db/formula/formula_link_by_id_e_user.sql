PREPARE formula_link_by_ (int, int) AS
    SELECT formula_link_id,
           link_type_id,
           excluded,
           share_type_id,
           protect_id
      FROM user_formula_links
     WHERE formula_link_id = $1
       AND user_id = $2;
