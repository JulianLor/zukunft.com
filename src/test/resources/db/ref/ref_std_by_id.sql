PREPARE ref_std_by_id (bigint) AS
    SELECT ref_id,
           phrase_id,
           external_key,
           ref_type_id,
           source_id,
           url,
           description,
           excluded,
           user_id
      FROM refs
     WHERE ref_id = $1;