<?php

/*

  calculate.php - update all formula results
  -------------
  
  The batch version of formula_test.php


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

$debug = $_GET['debug'] ?? 0;
const ROOT_PATH = __DIR__ . '/../';
include_once ROOT_PATH . 'src/main/php/zu_lib.php';

// open database
$db_con = prg_start("calculate");

// load the requesting user
$usr = new user;
$usr_id = $_GET['user']; // to force another user view for testing the formula calculation

// check if the user is permitted (e.g. to exclude crawlers from doing stupid stuff)
if ($usr->id > 0) {

    load_usr_data();

    $back = $_GET['back']; // the original calling page that should be shown after the change if finished

    // start displaying while calculating
    $calc_pos = 0;
    $last_msg_time = time();
    ob_implicit_flush();
    ob_end_flush();
    log_debug("create the calculation queue ... ");

    // estimate the block size for useful UI updates
    $total_formulas = $db_con->count(sql_db::TBL_FORMULA);
    $calc_blocks = (new formula_list($usr))->calc_blocks($db_con, $total_formulas);
    $block_size = max(1, round($total_formulas / $calc_blocks, 0));

    for ($page = 0; $page <= $calc_blocks; $page++) {
        // load the formulas to calculate
        $frm_lst = new formula_list($usr);
        $frm_lst->load_all($block_size, $page);
        echo "Calculate " . dsp_count($frm_lst->lst()) . " formulas<br>";

        foreach ($frm_lst as $frm_request) {

            // build the calculation queue
            $calc_fv_lst = new formula_value_list($usr);
            $calc_lst = $calc_fv_lst->frm_upd_lst($frm_request, $back);
            log_debug("calculate queue is build (number of values to check: " . dsp_count($calc_lst->lst()) . ")");

            // execute the queue
            foreach ($calc_lst->lst() as $r) {

                // calculate one formula result
                $frm = clone $r->frm;
                $fv_lst = $frm->calc($r->wrd_lst);

                // show the user the progress every two seconds
                if ($last_msg_time + UI_MIN_RESPONSE_TIME < time()) {
                    $calc_pct = ($calc_pos / sizeof($calc_lst->lst())) * 100;
                    echo "" . round($calc_pct, 2) . "% calculated (" . $r->frm->name . " for " . $r->wrd_lst->name_linked() . " = " . $fv_lst->names() . ")<br>";
                    ob_flush();
                    flush();
                    $last_msg_time = time();
                }

                $calc_pos++;
            }
        }
        ob_end_flush();
    }

    // display the finish message
    echo "<br>";
    echo "calculation finished.";
}

// Closing connection
prg_end($db_con);
