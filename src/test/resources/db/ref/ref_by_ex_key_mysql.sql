PREPARE ref_by_ex_key FROM
   'SELECT
        ref_id,
        phrase_id,
        ref_type_id,
        external_key
    FROM refs
   WHERE external_key = ?';