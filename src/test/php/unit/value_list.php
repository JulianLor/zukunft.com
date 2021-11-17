<?php

/*

  test/unit/value_list.php - unit testing of the VALUE LIST functions
  ------------------------
  

zukunft.com - calc with words

copyright 1995-2021 by zukunft.com AG, Blumentalstrasse 15, 8707 Uetikon am See, Switzerland

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

function run_value_list_unit_tests(testing $t)
{

    global $usr;
    global $sql_names;

    $t->header('Unit tests of the value list class (src/main/php/model/value/value_list.php)');

    /*
     * SQL creation tests (mainly to use the IDE check for the generated SQL statements)
     */

    $db_con = new sql_db();
    $db_con->db_type = sql_db::POSTGRES;

    // sql to load a list of value by the phrase ids
    $val_lst = new value_list;
    $val_lst->phr_lst = (new phrase_list_unit_tests)->get_phrase_list();
    $val_lst->phr_lst->ids = $val_lst->phr_lst->ids();
    $val_lst->usr = $usr;
    $created_sql = $val_lst->load_by_phr_lst_sql($db_con);
    $expected_sql = "SELECT DISTINCT v.value_id,
                         CASE WHEN (u.word_value IS NULL)  THEN v.word_value  ELSE u.word_value  END AS word_value,
                         CASE WHEN (u.excluded IS NULL)    THEN v.excluded    ELSE u.excluded    END AS excluded,
                         CASE WHEN (u.last_update IS NULL) THEN v.last_update ELSE u.last_update END AS last_update,
                         CASE WHEN (u.source_id IS NULL)   THEN v.source_id   ELSE u.source_id   END AS source_id,
                       v.user_id,
                       v.phrase_group_id,
                       v.time_word_id
                  FROM values v 
             LEFT JOIN user_values u ON u.value_id = v.value_id 
                                    AND u.user_id = 1 
                 WHERE v.value_id IN ( SELECT DISTINCT v.value_id 
                                         FROM  value_phrase_links l1,  value_phrase_links l2, 
                                              values v
                                               WHERE l1.phrase_id = 1 AND l1.value_id = v.value_id  
                                                 AND l2.phrase_id = 2 AND l2.value_id = v.value_id  )
              ORDER BY v.phrase_group_id, v.time_word_id;";
    $t->dsp('value_list->load_by_phr_lst_sql by group and time', zu_trim($expected_sql), zu_trim($created_sql));

    // ... and check if the prepared sql name is unique
    $result = false;
    $sql_name = $val_lst->load_by_phr_lst_sql($db_con,true);
    if (!in_array($sql_name, $sql_names)) {
        $result = true;
        $sql_names[] = $sql_name;
    }
    $target = true;
    $t->dsp('value_list->load_by_phr_lst_sql by group and time', $result, $target);

    // ... and the same for MySQL by replication the SQL builder statements
    $db_con->db_type = sql_db::MYSQL;
    $val_lst->usr = $usr;
    $created_sql = $val_lst->load_by_phr_lst_sql($db_con);
    $sql_avoid_code_check_prefix = "SELECT";
    $expected_sql = $sql_avoid_code_check_prefix . " DISTINCT v.value_id,
                             IF(u.word_value IS NULL, v.word_value, u.word_value)    AS word_value,
                             IF(u.excluded IS NULL, v.excluded, u.excluded)    AS excluded,
                             IF(u.last_update IS NULL, v.last_update, u.last_update)    AS last_update,
                             IF(u.source_id IS NULL, v.source_id, u.source_id)    AS source_id,
                       v.user_id,
                       v.phrase_group_id,
                       v.time_word_id
                  FROM `values` v 
             LEFT JOIN user_values u ON u.value_id = v.value_id 
                                    AND u.user_id = 1 
                 WHERE v.value_id IN ( SELECT DISTINCT v.value_id 
                                         FROM  value_phrase_links l1,  value_phrase_links l2, 
                                              `values` v
                                               WHERE l1.phrase_id = 1 AND l1.value_id = v.value_id 
                                                 AND l2.phrase_id = 2 AND l2.value_id = v.value_id  )
              ORDER BY v.phrase_group_id, v.time_word_id;";
    $t->dsp('value_list->load_by_phr_lst_sql by group and time for MySQL', zu_trim($expected_sql), zu_trim($created_sql));

}

