<?php

/*

    test/unit/ref.php - unit testing of the reference and source functions
    -----------------
  

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

use api\ref\source as source_api;
use cfg\ref_type_list;
use cfg\source_list;
use cfg\source_type_list;
use html\ref\ref as ref_dsp;
use html\ref\source as source_dsp;
use cfg\ref;
use cfg\source;
use cfg\db\sql_db;

class ref_unit_tests
{
    function run(test_cleanup $t): void
    {

        global $usr;

        // init for reference
        $db_con = new sql_db();
        $t->name = 'ref->';
        $t->resource_path = 'db/ref/';
        $json_file = 'unit/ref/wikipedia.json';
        $usr->set_id(1);

        $t->header('Unit tests of the reference class (src/main/php/model/ref/ref.php)');

        $t->subheader('SQL statement tests');
        $ref = new ref($usr);
        $t->assert_sql_by_id($db_con, $ref);
        $this->assert_sql_link_ids($t, $db_con, $ref);

        // sql to load a ref by id
        $ref = new ref($usr);
        $ref->set_id(3);
        $t->assert_sql_standard($db_con, $ref);

        // sql to load the ref types
        $ref_type_list = new ref_type_list();
        $t->assert_sql_all($db_con, $ref_type_list, sql_db::TBL_REF_TYPE);

        $t->subheader('Im- and Export tests');
        $t->assert_json_file(new ref($usr), $json_file);

        $t->subheader('API and frontend cast unit tests for references');
        $ref = $t->dummy_reference();
        $t->assert_api($ref);
        $t->assert_api_to_dsp($ref, new ref_dsp());


        // init for source
        $t->name = 'source->';
        $t->resource_path = 'db/ref/';
        $json_file = 'unit/ref/bipm.json';

        $t->header('Unit tests of the source class (src/main/php/model/ref/source.php)');

        $t->subheader('SQL statement tests');
        $src = new source($usr);
        $t->assert_sql_table_create($db_con, $src);
        $t->assert_sql_index_create($db_con, $src);
        $t->assert_sql_foreign_key_create($db_con, $src);
        $t->assert_sql_by_id($db_con, $src);
        $t->assert_sql_by_name($db_con, $src);
        $t->assert_sql_by_code_id($db_con, $src);

        // sql to load a source by id
        $src = new source($usr);
        $src->set_id(4);
        $t->assert_sql_standard($db_con, $src);

        // sql to load a source by name
        $src = new source($usr);
        $src->set_name(source_api::TN_READ);
        $t->assert_sql_standard($db_con, $src);
        $src->set_id(5);
        $t->assert_sql_not_changed($db_con, $src);
        $t->assert_sql_user_changes($db_con, $src);

        // sql to load the source types
        $source_type_list = new source_type_list();
        $t->assert_sql_all($db_con, $source_type_list, sql_db::TBL_SOURCE_TYPE);

        $t->subheader('Im- and Export tests');
        $t->assert_json_file(new source($usr), $json_file);

        $t->subheader('API and frontend cast unit tests for sources');
        $src = $t->dummy_source();
        $t->assert_api_msg($db_con, $src);
        $t->assert_api_to_dsp($src, new source_dsp());


        // init for source list
        $t->name = 'source_list->';

        $src_lst = new source_list($usr);
        $trm_ids = array(1, 2, 3);
        $t->assert_sql_by_ids($db_con, $src_lst, $trm_ids);
        $src_lst = new source_list($usr);
        $t->assert_sql_like($db_con, $src_lst);

    }

    /**
     * test the SQL statement creation for a value phrase link list in all SQL dialect
     * and check if the statement name is unique
     *
     * @param test_cleanup $t the test environment
     * @param sql_db $db_con the test database connection
     * @param ref $ref the reference object for which the load by link ids sql statement creation should be tested
     * @return void
     */
    private function assert_sql_link_ids(
        test_cleanup $t,
        sql_db $db_con,
        ref $ref): void
    {
        // check the Postgres query syntax
        $db_con->db_type = sql_db::POSTGRES;
        $qp = $ref->load_sql_by_link_ids($db_con->sql_creator(), 1, 2);
        $t->assert_qp($qp, $db_con->db_type);

        // check the MySQL query syntax
        $db_con->db_type = sql_db::MYSQL;
        $qp = $ref->load_sql_by_link_ids($db_con->sql_creator(), 1, 2);
        $t->assert_qp($qp, $db_con->db_type);
    }

}

