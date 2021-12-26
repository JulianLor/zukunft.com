PREPARE phrase_list_by_ids_triple_part (int, int, int, int) AS
    SELECT s.word_link_id,
           u.word_link_id                                                                                   AS user_word_link_id,
           s.user_id,
           s.from_phrase_id,
           s.to_phrase_id,
           s.verb_id,
           s.word_type_id,
           CASE
               WHEN (u.word_link_name <> '' IS NOT TRUE) THEN s.word_link_name
               ELSE u.word_link_name END                                                                    AS word_link_name,
           CASE
               WHEN (u.description <> '' IS NOT TRUE) THEN s.description
               ELSE u.description END                                                                       AS description,
           CASE WHEN (u.excluded IS NULL) THEN s.excluded ELSE u.excluded END                               AS excluded,
           CASE
               WHEN (u.share_type_id IS NULL) THEN s.share_type_id
               ELSE u.share_type_id END                                                                     AS share_type_id,
           CASE
               WHEN (u.protection_type_id IS NULL) THEN s.protection_type_id
               ELSE u.protection_type_id END                                                                AS protection_type_id
    FROM word_links s
             LEFT JOIN user_word_links u ON s.word_link_id = u.word_link_id AND u.user_id = $4
    WHERE s.word_link_id IN ($1,$2,$3);