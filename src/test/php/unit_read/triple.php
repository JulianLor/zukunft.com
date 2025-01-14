<?php

/*

    test/php/unit_read/triple.php - database unit testing of the triple functions
    -----------------------------


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

namespace unit_read;

use api\word\triple as triple_api;
use cfg\phrase;
use cfg\phrase_type;
use cfg\phrase_types;
use cfg\verb;
use cfg\triple;
use cfg\triple_list;
use test\test_cleanup;

class triple_unit_db_tests
{

    function run(test_cleanup $t): void
    {

        global $db_con;
        global $usr;
        global $phrase_types;

        // init
        $t->header('Unit database tests of the triple class (src/main/php/model/triple/triple.php)');
        $t->name = 'triple read db->';
        $t->resource_path = 'db/triple/';


        $t->subheader('Triple db read tests');

        $test_name = 'load triple ' . triple_api::TN_READ . ' by name and id';
        $trp = new triple($t->usr1);
        $trp->load_by_name(triple_api::TN_READ, triple::class);
        $trp_by_id = new triple($t->usr1);
        $trp_by_id->load_by_id($trp->id(), triple::class);
        $t->assert($test_name, $trp_by_id->name(), triple_api::TN_READ);
        $t->assert($test_name, $trp_by_id->description, triple_api::TD_READ);

    }
}

