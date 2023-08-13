PREPARE phrase_group_list_by_phr_trp (int, int, int) AS
    SELECT
            s.phrase_group_id,
            s.phrase_group_name,
            s.auto_description,
            s.word_ids,
            s.triple_ids,
            s.id_order,
            l.triple_id
       FROM phrase_groups s
  LEFT JOIN phrase_group_triple_links l ON s.phrase_group_id = l.phrase_group_id
      WHERE l.triple_id = $1
   ORDER BY s.phrase_group_id
      LIMIT $2
     OFFSET $3;