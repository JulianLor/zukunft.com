SELECT
    l.triple_id,
    ul.triple_id AS user_triple_id,
    l.user_id,
    l.from_phrase_id,
    l.verb_id,
    l.phrase_type_id,
    l.to_phrase_id,
    l.triple_name,
    l.name_given,
    l.name_generated,
    l.description,
    l.values,
    l.share_type_id,
    l.protect_id,
    v.verb_id,
    v.code_id,
    v.verb_name,
    v.name_plural,
    v.name_reverse,
    v.name_plural_reverse,
    v.formula_name,
    v.description,
    v.words,
    CASE WHEN (ul.excluded         IS     NULL) THEN l.excluded     ELSE ul.excluded    END AS excluded,
    t1.word_id AS word_id1,
    t1.user_id AS user_id1,
    CASE WHEN (u1.word_name <> ''   IS NOT TRUE) THEN t1.word_name      ELSE u1.word_name      END AS word_name1,
    CASE WHEN (u1.plural <> ''      IS NOT TRUE) THEN t1.plural         ELSE u1.plural         END AS plural1,
    CASE WHEN (u1.description <> '' IS NOT TRUE) THEN t1.description    ELSE u1.description    END AS description1,
    CASE WHEN (u1.phrase_type_id    IS     NULL) THEN t1.phrase_type_id ELSE u1.phrase_type_id END AS phrase_type_id1,
    CASE WHEN (u1.view_id           IS     NULL) THEN t1.view_id        ELSE u1.view_id        END AS view_id1,
    CASE WHEN (u1.excluded          IS     NULL) THEN t1.excluded       ELSE u1.excluded       END AS excluded1,
    t1.values AS values1,
    t2.word_id AS word_id2,
    t2.user_id AS user_id2,
    CASE WHEN (u2.word_name   <> '' IS NOT TRUE) THEN t2.word_name      ELSE u2.word_name      END AS word_name2,
    CASE WHEN (u2.plural      <> '' IS NOT TRUE) THEN t2.plural         ELSE u2.plural         END AS plural2,
    CASE WHEN (u2.description <> '' IS NOT TRUE) THEN t2.description    ELSE u2.description    END AS description2,
    CASE WHEN (u2.phrase_type_id    IS     NULL) THEN t2.phrase_type_id ELSE u2.phrase_type_id END AS phrase_type_id2,
    CASE WHEN (u2.view_id           IS     NULL) THEN t2.view_id        ELSE u2.view_id        END AS view_id2,
    CASE WHEN (u2.excluded          IS     NULL) THEN t2.excluded       ELSE u2.excluded       END AS excluded2,
    t2.values AS values2
FROM triples l LEFT JOIN user_triples ul  ON ul.triple_id = l.triple_id
    AND ul.user_id = 1,
     verbs v,
     words t1     LEFT JOIN user_words u1       ON u1.word_id = t1.word_id
         AND u1.user_id = 1 ,
     words t2     LEFT JOIN user_words u2       ON u2.word_id = t2.word_id
         AND u2.user_id = 1
WHERE l.verb_id        = v.verb_id
  AND l.from_phrase_id = t1.word_id
  AND l.to_phrase_id   = t2.word_id
  AND l.from_phrase_id IN (1,2)
ORDER BY l.verb_id, name_given;