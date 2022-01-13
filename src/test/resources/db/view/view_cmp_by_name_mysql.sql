PREPARE view_cmp_by_name FROM
    'SELECT s.view_component_id,
            u.view_component_id AS user_view_component_id,
            s.user_id,
            IF(u.view_component_name    IS NULL, s.view_component_name,    u.view_component_name)    AS view_component_name,
            IF(u.comment                IS NULL, s.comment,                u.comment)                AS comment,
            IF(u.view_component_type_id IS NULL, s.view_component_type_id, u.view_component_type_id) AS view_component_type_id,
            IF(u.word_id_row            IS NULL, s.word_id_row,            u.word_id_row)            AS word_id_row,
            IF(u.link_type_id           IS NULL, s.link_type_id,           u.link_type_id)           AS link_type_id,
            IF(u.formula_id             IS NULL, s.formula_id,             u.formula_id)             AS formula_id,
            IF(u.word_id_col            IS NULL, s.word_id_col,            u.word_id_col)            AS word_id_col,
            IF(u.word_id_col2           IS NULL, s.word_id_col2,           u.word_id_col2)           AS word_id_col2,
            IF(u.excluded               IS NULL, s.excluded,               u.excluded)               AS excluded,
            IF(u.share_type_id          IS NULL, s.share_type_id,          u.share_type_id)          AS share_type_id,
            IF(u.protect_id             IS NULL, s.protect_id,             u.protect_id)             AS protect_id,
            IF(ul.code_id               IS NULL, l.code_id,                ul.code_id)               AS code_id
       FROM view_components s
  LEFT JOIN user_view_components u  ON s.view_component_id = u.view_component_id AND u.user_id = ?
  LEFT JOIN view_component_types l  ON s.view_component_type_id =  l.view_component_type_id
  LEFT JOIN view_component_types ul ON u.view_component_type_id = ul.view_component_type_id
      WHERE s.view_component_name = ?';