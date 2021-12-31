PREPARE source_by_name FROM
    'SELECT s.source_id,
            u.source_id AS user_source_id,
            s.user_id,
            s.source_name,
            s.code_id,
            IF(u.source_name    IS NULL, s.source_name,    u.source_name)    AS source_name,
            IF(u.`url`          IS NULL, s.`url`,          u.`url`)          AS `url`,
            IF(u.comment        IS NULL, s.comment,        u.comment)        AS comment,
            IF(u.source_type_id IS NULL, s.source_type_id, u.source_type_id) AS source_type_id,
            IF(u.excluded       IS NULL, s.excluded,       u.excluded)       AS excluded
       FROM sources s
  LEFT JOIN user_sources u ON s.source_id = u.source_id AND u.user_id = ?
      WHERE s.source_name = ?';