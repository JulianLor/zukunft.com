PREPARE word_link_std_by_id FROM
   'SELECT word_link_id,
           word_link_name,
           word_type_id,
           user_id
      FROM word_links
     WHERE word_link_id = ?';