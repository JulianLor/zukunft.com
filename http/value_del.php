<?php

/*

  value_del.php - delete a value
  -------------
  
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

// standard zukunft header for callable php files to allow debugging and lib loading
use controller\controller;
use html\api;
use html\button;
use html\html_base;
use html\msg;
use html\view\view_dsp_old;
use cfg\user;
use cfg\value;
use cfg\view;

$debug = $_GET['debug'] ?? 0;
const ROOT_PATH = __DIR__ . '/../';
include_once ROOT_PATH . 'src/main/php/zu_lib.php';

// to create the code for the html frontend
$html = new html_base();

// open database
$db_con = prg_start("value_del");

$result = ''; // reset the html code var

// load the session user parameters
$usr = new user;
$result .= $usr->get();

// check if the user is permitted (e.g. to exclude crawlers from doing stupid stuff)
if ($usr->id() > 0) {

    $usr->load_usr_data();

    // prepare the display
    $dsp = new view_dsp_old($usr);
    $dsp->load_by_code_id(controller::DSP_VALUE_DEL);
    $back = $_GET[controller::API_BACK];  // the page from which the value deletion has been called

    // get the parameters
    $val_id = $_GET[controller::URL_VAR_ID];
    $confirm = $_GET['confirm'];

    if ($val_id > 0) {

        // create the value object to have an object to update the parameters
        $val = new value($usr);
        $val->load_by_id($val_id);

        if ($confirm == 1) {
            // actually delete the value (at least for this user)
            $val->del();

            $result .= $html->dsp_go_back($back, $usr);
        } else {
            // display the view header
            $result .= $dsp->dsp_navbar($back);

            $val->load_phrases();
            $url = $html->url(api::VALUE . api::REMOVE, $val_id, $back);
            $ui_msg = new msg();
            $result .= (new button($url, $back))->yesno(
                msg::VALUE_DEL, $val->number() . $ui_msg->txt(msg::FOR) . $val->phr_lst()->dsp_name() . '?');
        }
    } else {
        $result .= $html->dsp_go_back($back, $usr);
    }
}

echo $result;

prg_end($db_con);
