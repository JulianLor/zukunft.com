<?php

/*

    test/unit/figure.php - unit testing of the figure functions
    --------------------
  

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

use model\figure;

class figure_unit_tests
{
    function run(testing $t): void
    {

        global $usr;

        $t->header('Unit tests of the formula class (src/main/php/model/formula/figure.php)');


        $t->subheader('SQL statement tests');

        // if the user has changed the formula, that related figure is not standard anymore
        $frm = new formula($usr);
        $frm->usr_cfg_id = 1;
        $fig = new figure($usr);
        $fig->obj = $frm;
        $result = $fig->is_std();
        $t->assert('figure->is_std if formula is changed by the user', $result, false);


        $t->subheader('API unit tests');

        $fig = $t->dummy_figure_value();
        // TODO: fix it
        //$t->assert_api($fig);

    }

}