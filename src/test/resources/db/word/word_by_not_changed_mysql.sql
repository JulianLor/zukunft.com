PREPARE word_by_not_changed FROM
    'SELECT user_id
       FROM user_words
      WHERE word_id = ?
        AND (excluded <> 1 OR excluded is NULL)';