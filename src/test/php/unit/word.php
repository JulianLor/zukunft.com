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
    along with zukunft.com. If not, see <http://www.gnu.org/licenses/agpl.html>.

    To contact the authors write to:
    Timon Zielonka <timon@zukunft.com>

    Copyright (c) 1995-2022 zukunft.com AG, Zurich
    Heang Lor <heang@zukunft.com>

    http://zukunft.com

*/

namespace test;

include_once DB_PATH . 'sql_db.php';
include_once MODEL_WORD_PATH . 'word.php';
include_once API_WORD_PATH . 'word.php';
include_once WEB_WORD_PATH . 'word.php';

use model\sql_db;
use cfg\phrase_type;
use model\word;
use api\word_api;
use html\word\word as word_dsp;

class word_unit_tests
{

    function run(test_cleanup $t): void
    {

        global $usr;

        // init
        $db_con = new sql_db();
        $t->name = 'word->';
        $t->resource_path = 'db/word/';
        $json_file = 'unit/word/second.json';
        $usr->set_id(1);

        $t->header('Unit tests of the word class (src/main/php/model/word/word.php)');


        $t->subheader('SQL user sandbox statement tests');

        $wrd = new word($usr);
        $t->assert_load_sql_id($db_con, $wrd);
        $t->assert_load_sql_name($db_con, $wrd);


        $t->subheader('SQL statement tests');

        // sql to load the word by id
        $wrd = new word($usr);
        $wrd->set_id(2);
        $t->assert_load_standard_sql($db_con, $wrd);
        $t->assert_not_changed_sql($db_con, $wrd);

        // get the most often used view
        $db_con->db_type = sql_db::POSTGRES;
        $qp = $wrd->view_sql($db_con);
        $t->assert_qp($qp, $db_con->db_type);

        $db_con->db_type = sql_db::MYSQL;
        $qp = $wrd->view_sql($db_con);
        $t->assert_qp($qp, $db_con->db_type);


        $t->subheader('API unit tests');

        $wrd = new word($usr);
        $wrd->set(1, word_api::TN_READ, phrase_type::MATH_CONST);
        $wrd->description = word_api::TD_READ;
        $api_wrd = $wrd->api_obj();
        $t->assert($t->name . 'api->id', $api_wrd->id, $wrd->id());
        $t->assert($t->name . 'api->name', $api_wrd->name, $wrd->name_dsp());
        $t->assert($t->name . 'api->description', $api_wrd->description, $wrd->description);


        $t->subheader('Im- and Export tests');

        $t->assert_json(new word($usr), $json_file);


        $t->subheader('HTML frontend unit tests');

        $wrd = $t->dummy_word();
        $t->assert_api_to_dsp($wrd, new word_dsp());

    }

}