<?php

/*

  async_process.php - display the progress of an asynchronous process
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

// standard zukunft header for callable php files to allow debugging and lib loading
use controller\controller;
use html\html_base;
use html\view\view as view_dsp;
use cfg\user;
use cfg\view;

$debug = $_GET['debug'] ?? 0;
const ROOT_PATH = __DIR__ . '/../';
include_once ROOT_PATH . 'src/main/php/zu_lib.php';
if ($debug > 1) {
    echo 'lib loaded<br>';
}

// open database
$db_con = prg_start("progress display");

$result = ''; // reset the html code var
$msg = ''; // to collect all messages that should be shown to the user immediately

// load the session user parameters
$usr = new user;
$result .= $usr->get();
$back = $_GET[controller::API_BACK];     // the word id from which this value change has been called (maybe later any page)

// check if the user is permitted (e.g. to exclude crawlers from doing stupid stuff)
if ($usr->id() > 0) {

    $html = new html_base();

    $usr->load_usr_data();

    // prepare the display
    $msk = new view($usr);
    $msk->load_by_code_id(controller::DSP_IMPORT);

    if ($usr->is_admin()) {

        // load the testing functions
        include_once '../src/main/php/service/import/import_file.php';
        if ($debug > 9) {
            echo 'test base loaded<br>';
        }

        // ---------------------------------------
        // start base configuration load and check
        // ---------------------------------------

        $html = new html_base();
        $msk_dsp = new view_dsp($msk->api_json());
        $html->echo($msk_dsp->dsp_navbar($back));

        $html->echo("loading of base configuration started<br>");

        import_base_config($usr);

        $html->echo("loading of base configuration finished<br>");

        $html->echo($html->dsp_go_back($back, $usr));
    }
}

// Closing connection
prg_end($db_con);
