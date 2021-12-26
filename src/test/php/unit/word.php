<?php

/*

  test/unit/word.php - unit testing of the word functions
  ------------------
  

    This file is part of zukunft.com - calc with words

    zukunft.com is free software: you can redistribute it and/or modify it
    under the terms of the GNU General Public License as
    published by the Free Software Foundation, either version 3 of
    the License, or (at your option) any later version.
    zukunft.com is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with zukunft.com. If not, see <http://www.gnu.org/licenses/gpl.html>.

    To contact the authors write to:
    Timon Zielonka <timon@zukunft.com>

    Copyright (c) 1995-2021 zukunft.com AG, Zurich
    Heang Lor <heang@zukunft.com>

    http://zukunft.com

*/

class word_unit_tests
{
    function run(testing $t)
    {

        global $usr;
        global $sql_names;

        $t->header('Unit tests of the word class (src/main/php/model/word/word.php)');

        $t->subheader('SQL statement tests');

        $db_con = new sql_db();
        $usr->id = 1;

        // sql to load the word by id
        $wrd = new word($usr);
        $wrd->id = 2;
        $db_con->db_type = sql_db::POSTGRES;
        $created_sql = $wrd->load_sql($db_con);
        $expected_sql = $t->file('db/word/word_by_id.sql');
        $t->dsp('word->load_sql by word id', $t->trim($expected_sql), $t->trim($created_sql));

        // ... and check if the prepared sql name is unique
        $t->assert_sql_name_unique($wrd->load_sql($db_con, true));

        // sql to load the word by name
        $wrd = new word($usr);
        $wrd->id = 0;
        $wrd->name = word::TN_READ;
        $db_con->db_type = sql_db::POSTGRES;
        $created_sql = $wrd->load_sql($db_con);
        $expected_sql = "SELECT 
                            s.word_id, 
                            u.word_id AS user_word_id, 
                            s.user_id, 
                            s.values, 
                            CASE WHEN (u.word_name   <> ''  IS NOT TRUE) THEN s.word_name          ELSE u.word_name          END AS word_name, 
                            CASE WHEN (u.plural      <> ''  IS NOT TRUE) THEN s.plural             ELSE u.plural             END AS plural, 
                            CASE WHEN (u.description <> ''  IS NOT TRUE) THEN s.description        ELSE u.description        END AS description, 
                            CASE WHEN (u.word_type_id       IS     NULL) THEN s.word_type_id       ELSE u.word_type_id       END AS word_type_id, 
                            CASE WHEN (u.view_id            IS     NULL) THEN s.view_id            ELSE u.view_id            END AS view_id, 
                            CASE WHEN (u.excluded           IS     NULL) THEN s.excluded           ELSE u.excluded           END AS excluded,
                            CASE WHEN (u.share_type_id      IS     NULL) THEN s.share_type_id      ELSE u.share_type_id      END AS share_type_id,  
                            CASE WHEN (u.protection_type_id IS     NULL) THEN s.protection_type_id ELSE u.protection_type_id END AS protection_type_id 
                       FROM words s LEFT JOIN user_words u ON s.word_id = u.word_id 
                                                          AND u.user_id = 1 
                      WHERE (u.word_name = '" . word::TN_READ . "'
                         OR (s.word_name = '" . word::TN_READ . "' AND u.word_name IS NULL));";
        $expected_sql = $t->file('db/word/word_by_name.sql');
        $t->dsp('word->load_sql by word name', $t->trim($expected_sql), $t->trim($created_sql));

        // ... and check if the prepared sql name is unique
        $t->assert_sql_name_unique($wrd->load_sql($db_con, true));


        $t->subheader('Im- and Export tests');

        $json_in = json_decode(file_get_contents(PATH_TEST_IMPORT_FILES . 'unit/word/second.json'), true);
        $wrd = new word_dsp($usr);
        $wrd->import_obj($json_in, false);
        $json_ex = json_decode(json_encode($wrd->export_obj(false)), true);
        $result = json_is_similar($json_in, $json_ex);
        $target = true;
        $t->dsp('word->import check name', $target, $result);

    }

}