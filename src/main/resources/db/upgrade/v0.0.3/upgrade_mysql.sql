--
-- upgrade the zukunft.com MySQL database to Version 0.0.3
--

-- --------------------------------------------------------

--
-- Table structure for table`user_refs`
--

CREATE TABLE IF NOT EXISTS `user_refs`
(
    `ref_id`        int(11) NOT NULL,
    `user_id`       int(11) NOT NULL,
    `url`           text         DEFAULT NULL,
    `description`   text         DEFAULT NULL,
    `excluded`      tinyint(4)   DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

--
-- Indexes for table`user_refs`
--
ALTER TABLE `user_refs`
    ADD UNIQUE KEY `ref_id` (`ref_id`, `user_id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `ref_id_2` (`ref_id`);

--
-- Constraints for table`user_refs`
--
ALTER TABLE `user_refs`
    ADD CONSTRAINT `user_refs_fk_1` FOREIGN KEY (`ref_id`) REFERENCES `refs` (`ref_id`),
    ADD CONSTRAINT `user_refs_fk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

-- --------------------------------------------------------

--
-- Table structure for table`user_view_term_links`
--

CREATE TABLE IF NOT EXISTS `user_view_term_links`
(
    `view_term_link_id` int(11) NOT NULL,
    `type_id`           int(11) NOT NULL DEFAULT '1' COMMENT '1 = from_term_id is link the terms table; 2=link to the term_links table;3=to term_groups',
    `link_type_id`      int(11)          DEFAULT NULL,
    `user_id`           int(11) NOT NULL,
    `description`       text             DEFAULT NULL,
    `excluded`          tinyint(4)       DEFAULT NULL,
    `share_type_id`     smallint         DEFAULT NULL,
    `protect_id`        smallint         DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='used to define the default mask for a term or a term group';

-- --------------------------------------------------------

--
-- Structure for view`phrases`
--

DROP TABLE IF EXISTS `phrases`;

CREATE ALGORITHM = UNDEFINED DEFINER =`root`@`localhost` SQL SECURITY DEFINER VIEW `phrases` AS
select `words`.`word_id`            AS `phrase_id`,
       `words`.`user_id`            AS `user_id`,
       `words`.`word_name`          AS `phrase_name`,
       `words`.`description`        AS `description`,
       `words`.`values`             AS `values`,
       `words`.`phrase_type_id`     AS `phrase_type_id`,
       `words`.`excluded`           AS `excluded`,
       `words`.`share_type_id`      AS `share_type_id`,
       `words`.`protect_id` AS `protect_id`
from `words`
union
select (`triples`.`triple_id` * -(1)) AS `phrase_id`,
       `triples`.`user_id`               AS `user_id`,
       if(`triples`.`triple_name` is null,
          if(`triples`.`name_given` is null,
           `triples`.`name_generated`,
           `triples`.`name_given`),
          `triples`.`triple_name` AS `phrase_name`,
       `triples`.`description`           AS `description`,
       `triples`.`values`                AS `values`,
       `triples`.`phrase_type_id`        AS `phrase_type_id`,
       `triples`.`excluded`              AS `excluded`,
       `triples`.`share_type_id`         AS `share_type_id`,
       `triples`.`protect_id`    AS `protect_id`
from `triples`;

--
-- Structure for view`user_phrases`
--
DROP TABLE IF EXISTS `user_phrases`;

CREATE ALGORITHM = UNDEFINED DEFINER =`root`@`localhost` SQL SECURITY DEFINER VIEW `user_phrases` AS
select `user_words`.`word_id`       AS `phrase_id`,
       `user_words`.`user_id`       AS `user_id`,
       `user_words`.`word_name`     AS `phrase_name`,
       `user_words`.`description`   AS `description`,
       `user_words`.`values`        AS `values`,
       `user_words`.`excluded`      AS `excluded`,
       `user_words`.`share_type_id` AS `share_type_id`,
       `user_words`.`protect_id`    AS `protect_id`
from `user_words`
union
select (`user_triples`.`triple_id` * -(1)) AS `phrase_id`,
       `user_triples`.`user_id`               AS `user_id`,
       if(`triples`.`triple_name` is null,
          if(`triples`.`name_given` is null,
             `triples`.`name_generated`,
             `triples`.`name_given`),
          `triples`.`triple_name` AS `phrase_name`,
       `user_triples`.`description`           AS `description`,
       `user_triples`.`values`                AS `values`,
       `user_triples`.`excluded`              AS `excluded`,
       `user_triples`.`share_type_id`         AS `share_type_id`,
       `user_triples`.`protect_id`            AS `protect_id`
from `user_triples`;


--
-- Structure for view`terms`
--
DROP TABLE IF EXISTS `terms`;

CREATE ALGORITHM = UNDEFINED DEFINER =`root`@`localhost` SQL SECURITY DEFINER VIEW `terms` AS
select ((`words`.`word_id` * 2) - 1) AS `term_id`,
       `words`.`user_id`           AS `user_id`,
       `words`.`word_name`         AS `term_name`,
       `words`.`description`       AS `description`,
       `words`.`values`            AS `usage`,
       `words`.`phrase_type_id`    AS `term_type_id`,
       `words`.`excluded`          AS `excluded`,
       `words`.`share_type_id`     AS `share_type_id`,
       `words`.`protect_id`        AS `protect_id`,
       ''                          AS `formula_text`,
       ''                          AS `resolved_text`
from `words`
where `words`.`phrase_type_id` <> 10 OR `words`.`phrase_type_id` is null
union
select ((`triples`.`triple_id` * -2) + 1) AS `term_id`,
       `triples`.`user_id`                 AS `user_id`,
       if(`triples`.`triple_name` is null,
          if(`triples`.`name_given` is null,
             `triples`.`name_generated`,
             `triples`.`name_given`),
          `triples`.`triple_name` AS `phrase_name`,
       `triples`.`description`             AS `description`,
       `triples`.`values`                  AS `usage`,
       `triples`.`phrase_type_id`          AS `term_type_id`,
       `triples`.`excluded`                AS `excluded`,
       `triples`.`share_type_id`           AS `share_type_id`,
       `triples`.`protect_id`              AS `protect_id`,
       ''                                  AS `formula_text`,
       ''                                  AS `resolved_text`
from `triples`
union
select (`formulas`.`formula_id` * 2) AS `term_id`,
       `formulas`.`user_id`         AS `user_id`,
       `formulas`.`formula_name`    AS `term_name`,
       `formulas`.`description`     AS `description`,
       `formulas`.`usage`           AS `usage`,
       `formulas`.`formula_type_id` AS `term_type_id`,
       `formulas`.`excluded`        AS `excluded`,
       `formulas`.`share_type_id`   AS `share_type_id`,
       `formulas`.`protect_id`      AS `protect_id`,
       `formulas`.`formula_text`    AS `formula_text`,
       `formulas`.`resolved_text`   AS `resolved_text`
from `formulas`
union
select (`verbs`.`verb_id` * -2) AS `term_id`,
       NULL                    AS `user_id`,
       `verbs`.`formula_name`  AS `term_name`,
       `verbs`.`description`   AS `description`,
       `verbs`.`words`         AS `usage`,
       NULL                    AS `term_type_id`,
       NULL                    AS `excluded`,
       1                       AS `share_type_id`,
       3                       AS `protect_id`,
       ''                      AS `formula_text`,
       ''                      AS `resolved_text`
from `verbs`
;

--
-- Structure for view `user_terms`
--
DROP TABLE IF EXISTS `user_terms`;

CREATE ALGORITHM = UNDEFINED DEFINER =`root`@`localhost`SQL
    SECURITY DEFINER VIEW `user_terms` AS
select ((`user_words`.`word_id` * 2) - 1) AS `term_id`,
       `user_words`.`user_id`             AS `user_id`,
       `user_words`.`word_name`           AS `term_name`,
       `user_words`.`description`         AS `description`,
       `user_words`.`values`              AS `usage`,
       `user_words`.`excluded`            AS `excluded`,
       `user_words`.`share_type_id`       AS `share_type_id`,
       `user_words`.`protect_id`          AS `protect_id`,
       ''                                 AS `formula_text`,
       ''                                 AS `resolved_text`
from `user_words`
where `user_words`.`phrase_type_id` <> 10
union
select ((`user_triples`.`triple_id` * -2) + 1) AS `term_id`,
       `user_triples`.`user_id`                   AS `user_id`,
       if(`triples`.`triple_name` is null,
          if(`triples`.`name_given` is null,
             `triples`.`name_generated`,
             `triples`.`name_given`),
          `triples`.`triple_name` AS `phrase_name`,
       `user_triples`.`description`               AS `description`,
       `user_triples`.`values`                    AS `usage`,
       `user_triples`.`excluded`                  AS `excluded`,
       `user_triples`.`share_type_id`             AS `share_type_id`,
       `user_triples`.`protect_id`                AS `protect_id`,
       ''                                         AS `formula_text`,
       ''                                         AS `resolved_text`
from `user_triples`
union
select (`user_formulas`.`formula_id` * 2) AS `term_id`,
       `user_formulas`.`user_id`          AS `user_id`,
       `user_formulas`.`formula_name`     AS `term_name`,
       `user_formulas`.`description`      AS `description`,
       `user_formulas`.`usage`            AS `usage`,
       `user_formulas`.`excluded`         AS `excluded`,
       `user_formulas`.`share_type_id`    AS `share_type_id`,
       `user_formulas`.`protect_id`       AS `protect_id`,
       `user_formulas`.`formula_text`     AS `formula_text`,
       `user_formulas`.`resolved_text`    AS `resolved_text`
from `user_formulas`
union
select (`verbs`.`verb_id` * -2) AS `term_id`,
       NULL                     AS `user_id`,
       `verbs`.`formula_name`   AS `term_name`,
       `verbs`.`description`    AS `description`,
       `verbs`.`words`          AS `usage`,
       NULL                     AS `excluded`,
       1                        AS `share_type_id`,
       3                        AS `protect_id`,
       ''                       AS `formula_text`,
       ''                       AS `resolved_text`
from `verbs`
;

--
-- Structure for view`change_table_fields`
--
DROP TABLE IF EXISTS `change_table_fields`;

CREATE ALGORITHM = UNDEFINED DEFINER =`root`@`localhost`SQL
    SECURITY DEFINER VIEW `change_table_fields` AS
select `change_fields`.`change_field_id`                                              AS `change_table_field_id`,
       CONCAT(`change_tables`.`change_table_id`, `change_fields`.`change_field_name`) AS `change_table_field_name`,
       `change_fields`.`description`                                                  AS `description`,
       IF(`change_fields`.`code_id` IS NULL,
          CONCAT(`change_tables`.`change_table_id`, `change_fields`.`change_field_name`),
          `change_fields`.`code_id`) AS `code_id`
from `change_fields`,
     `change_tables`
WHERE `change_fields`.table_id = `change_tables`.change_table_id;

-- --------------------------------------------------------

--
-- Table structure for table`user_value_time_series`
--

CREATE TABLE IF NOT EXISTS `user_value_time_series`
(
    `value_time_series_id` int(11)   NOT NULL,
    `user_id`              int(11)   NOT NULL,
    `source_id`            int(11)        DEFAULT NULL,
    `excluded`             tinyint(4)     DEFAULT NULL,
    `share_type_id`        int(11)        DEFAULT NULL,
    `protect_id`   int(11)   NOT NULL,
    `last_update`          timestamp NULL DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='common parameters for a user specific list of intraday values';

--
-- Indexes for table`user_value_time_series`
--
ALTER TABLE `user_value_time_series`
    ADD PRIMARY KEY (`value_time_series_id`, `user_id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `source_id` (`source_id`),
    ADD KEY `value_id` (`value_time_series_id`),
    ADD KEY `share_type` (`share_type_id`),
    ADD KEY `protect_id` (`protect_id`);

--
-- Constraints for table`user_value_time_series`
--
ALTER TABLE `user_value_time_series`
    ADD CONSTRAINT `user_value_time_series_fk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    ADD CONSTRAINT `user_value_time_series_fk_2` FOREIGN KEY (`source_id`) REFERENCES `sources` (`source_id`),
    ADD CONSTRAINT `user_value_time_series_fk_3` FOREIGN KEY (`share_type_id`) REFERENCES `share_types` (`share_type_id`),
    ADD CONSTRAINT `user_value_time_series_fk_4` FOREIGN KEY (`protect_id`) REFERENCES `protection_types` (`protection_type_id`);

--
-- database corrections
--
--
-- Constraints for table`user_values`
--
ALTER TABLE `user_values`
    ADD CONSTRAINT `user_values_fk_4` FOREIGN KEY (`protect_id`) REFERENCES `protection_types` (`protection_type_id`);
