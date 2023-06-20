PREPARE value_by_3phrase_id (int, int, int) AS
    SELECT s.value_id,
           u.value_id                                                            AS user_value_id,
           s.user_id,
           s.phrase_group_id,
           CASE WHEN (u.numeric_value      IS NULL) THEN s.numeric_value      ELSE u.numeric_value      END  AS numeric_value,
           CASE WHEN (u.source_id          IS NULL) THEN s.source_id          ELSE u.source_id          END  AS source_id,
           CASE WHEN (u.last_update        IS NULL) THEN s.last_update        ELSE u.last_update        END  AS last_update,
           CASE WHEN (u.excluded           IS NULL) THEN s.excluded           ELSE u.excluded           END  AS excluded,
           CASE WHEN (u.protect_id         IS NULL) THEN s.protect_id         ELSE u.protect_id         END  AS protect_id,
           u.share_type_id
      FROM values s
 LEFT JOIN user_values u ON s.value_id = u.value_id AND u.user_id = $1
     WHERE s.phrase_group_id IN (SELECT l1.phrase_group_id
                                  FROM phrase_group_word_links l1,
                                       phrase_group_word_links l2
                                 WHERE l1.word_id = $2
                                   AND l1.phrase_group_id = l2.phrase_group_id
                                   AND l2.word_id = $3);