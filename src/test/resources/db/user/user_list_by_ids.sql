PREPARE user_list_by_ids (bigint[]) AS
    SELECT
         user_id,
         user_name,
         code_id,
         ip_address,
         email,
         first_name,
         last_name,
         last_word_id,
         source_id,
         user_profile_id
    FROM users
   WHERE user_id = ANY ($1);
