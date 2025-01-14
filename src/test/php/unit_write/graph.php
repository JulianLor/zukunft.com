<?php

/*

    test/php/unit_write/graph.php - TESTing of the GRAPH functions
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

namespace test\write;

use api\word\word as word_api;
use cfg\foaf_direction;
use cfg\phrase_list;
use cfg\triple_list;
use cfg\value\value_list;
use cfg\verb;
use cfg\word;
use test\test_cleanup;
use const test\TIMEOUT_LIMIT_PAGE;

class graph_test
{

    function run(test_cleanup $t): void
    {

        global $usr;

        $back = 0;

        $t->header('Test the graph class (classes/triple_list.php)');

        // get values related to a phrase list
        // e.g. to get top 10 cities by the number of inhabitants
        // in SQL the statement would be: SELECT inhabitants FROM city ORDER BY inhabitants DESC LIMIT 10;
        // in zukunft.com the statement should be: top 10 cities by inhabitants
        // both statements should be possible in zukunft.com

        // the (slow but first step) internal translation could be

        // interpretation
        // step 1: detect that "top 10" is a limit and order setting
        // step 2: detect that the words to select the values are "city" and "inhabitants"

        // request building
        // step 1: define the phrase list e.g. in this case only the test word for city

        $phr_lst = new phrase_list($usr);
        $phr_lst->load_by_names(array(word_api::TN_CITY));

        // step 2: get all values related to the phrases
        $val_lst = new value_list($usr);
        $val_lst->load_by_phr_lst($phr_lst);
        $wrd_lst_all = $val_lst->phr_lst()->wrd_lst_all();

        // step 3: get all phrases used for the value descriptions
        $phr_lst_used = new phrase_list($usr);
        foreach ($wrd_lst_all->lst() as $wrd) {
            if (!array_key_exists($wrd->id(), $phr_lst_used->id_lst())) {
                $phr_lst_used->add($wrd->phrase());
            }
        }
        // step 4: get the word links for the used phrases
        //         these are the word links that are needed for a complete export
        // TODO activate Prio 1
        $lnk_lst = new triple_list($usr);
        //$lnk_lst->load_by_phr_lst($phr_lst_used, null, foaf_direction::UP);
        //$result = $lnk_lst->name();
        // check if at least the basic relations are in the database
        /*
        $target = '' . word_api::TN_CITY_AS_CATEGORY . ' has a balance sheet';
        $t->dsp_contains(', triple_list->load for ' . $phr_lst->dsp_id(), $target, $result, TIMEOUT_LIMIT_PAGE);
        $target = 'Company has a forecast';
        $t->dsp_contains(', triple_list->load for ' . $phr_lst->dsp_id(), $target, $result, TIMEOUT_LIMIT_PAGE);
        $target = 'Company uses employee';
        $t->dsp_contains(', word ' . $phr_lst->dsp_id(), $target, $result, TIMEOUT_LIMIT_PAGE);
        */

        // similar to above, but just for the zurich
        $phr_lst = new phrase_list($usr);
        $phr_lst->load_by_names(array(word_api::TN_ZH, word_api::TN_INHABITANTS, word_api::TN_MIO));
        $lnk_lst = new triple_list($usr);
        $lnk_lst->load_by_phr_lst($phr_lst, null, foaf_direction::UP);
        //$lnk_lst->wrd_lst = $phr_lst->wrd_lst_all();
        $result = $lnk_lst->name();
        // TODO to be reviewed
        $target = word_api::TN_ZH;
        $t->dsp_contains(', triple_list->load for ' . $phr_lst->dsp_id(), $target, $result, TIMEOUT_LIMIT_PAGE);


        // the other side
        $ZH = new word($usr);
        $ZH->load_by_name(word_api::TN_ZH, word::class);
        $is = new verb;
        $is->set_user($usr);
        $is->load_by_code_id(verb::IS);
        $graph = new triple_list($usr);
        $graph->load_by_phr($ZH->phrase(), $is, foaf_direction::UP);
        //$target = zut_html_list_related($ZH->id, $graph->direction, $usr->id());
        $result = $graph->display($back);
        /*
        $diff = str_diff($target, $result);
        if ($diff != null) {
            if (in_array('view', $diff)) {
                if (in_array(0, $diff['view'])) {
                    if ($diff['view'][0] == 0) {
                        $target = $result;
                    }
                }
            }
        } */
        $target = word_api::TN_COMPANY;
        $t->dsp_contains('graph->load for ZH up is', $target, $result);

    }

}