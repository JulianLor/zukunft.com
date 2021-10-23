<?php

/*

  test_base.php - for internal code consistency TESTing the BASE functions and definitions
  -------------
  
  used functions
  ----
  
  test_exe_time    - show the execution time for the last test and create a warning if it took too long
  test_dsp - simply to display the function test result
  test_show_db_id  - to get a database id because this may differ from instance to instance


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

// TODO move the names and values for testing to the single objects and check that they cannot be used by an user

global $debug;
global $root_path;

//const ROOT_PATH = __DIR__;

if ($root_path == '') {
    $root_path = '../';
}

// set the paths of the program code
$path_test = $root_path . 'src/test/php/utils/';            // for the general tests and test setup
$path_unit = $root_path . 'src/test/php/unit/';             // for unit tests
$path_unit_db = $root_path . 'src/test/php/unit_db/';       // for the unit tests with database real only
$path_unit_dsp = $root_path . 'src/test/php/unit_display/'; // for the unit tests that create HTML code
$path_unit_ui = $root_path . 'src/test/php/unit_ui/';       // for the unit tests that create JSON messages for the frontend
$path_unit_save = $root_path . 'src/test/php/unit_save/';   // for the unit tests that save to database (and cleanup the test data after completion)
$path_it = $root_path . 'src/test/php/integration/';        // for integration tests
$path_dev = $root_path . 'src/test/php/dev/';               // for test still in development

include_once $root_path . 'src/main/php/service/config.php';

// load the other test utility modules (beside this base configuration module)
include_once $path_test . 'test_system.php';
include_once $path_test . 'test_db_link.php';
include_once $path_test . 'test_user.php';
include_once $path_test . 'test_user_sandbox.php';

// load the unit testing modules
include_once $path_unit . 'all.php';
include_once $path_unit . 'test_lib.php';
include_once $path_unit . 'user_sandbox.php';
include_once $path_unit . 'word.php';
include_once $path_unit . 'word_list.php';
include_once $path_unit . 'word_link.php';
include_once $path_unit . 'word_link_list.php';
include_once $path_unit . 'phrase_list.php';
include_once $path_unit . 'phrase_group.php';
include_once $path_unit . 'value.php';
include_once $path_unit . 'value_list.php';
include_once $path_unit . 'formula.php';
include_once $path_unit . 'view.php';
include_once $path_unit . 'view_component_link.php';
include_once $path_unit . 'ref.php';
include_once $path_unit . 'user_log.php';

// load the unit testing modules with database real only
include_once $path_unit_db . 'all.php';
include_once $path_unit_db . 'system.php';
include_once $path_unit_db . 'sql_db.php';
include_once $path_unit_db . 'user.php';
include_once $path_unit_db . 'word.php';
include_once $path_unit_db . 'verb.php';
include_once $path_unit_db . 'formula.php';
include_once $path_unit_db . 'view.php';
include_once $path_unit_db . 'ref.php';
include_once $path_unit_db . 'share.php';
include_once $path_unit_db . 'protection.php';

// load the testing functions for creating HTML code
include_once $path_unit_dsp . 'test_display.php';

// load the testing functions for creating JSON messages for the frontend code
include_once $path_unit_ui . 'test_formula_ui.php';
include_once $path_unit_ui . 'test_word_ui.php';
include_once $path_unit_ui . 'value_test_ui.php';

// load the testing functions that save data to the database
include_once $path_unit_save . 'test_math.php';
include_once $path_unit_save . 'test_word.php';
include_once $path_unit_save . 'test_word_display.php';
include_once $path_unit_save . 'test_word_list.php';
include_once $path_unit_save . 'test_word_link.php';
include_once $path_unit_save . 'phrase_test.php';
include_once $path_unit_save . 'phrase_list_test.php';
include_once $path_unit_save . 'phrase_group_test.php';
include_once $path_unit_save . 'phrase_group_list_test.php';
include_once $path_unit_save . 'ref_test.php';
include_once $path_unit_save . 'test_graph.php';
include_once $path_unit_save . 'test_verb.php';
include_once $path_unit_save . 'test_term.php';
include_once $path_unit_save . 'value_test.php';
include_once $path_unit_save . 'test_source.php';
include_once $path_unit_save . 'test_expression.php';
include_once $path_unit_save . 'test_formula.php';
include_once $path_unit_save . 'test_formula_link.php';
include_once $path_unit_save . 'test_formula_trigger.php';
include_once $path_unit_save . 'test_formula_value.php';
include_once $path_unit_save . 'test_formula_element.php';
include_once $path_unit_save . 'test_formula_element_group.php';
include_once $path_unit_save . 'test_batch.php';
include_once $path_unit_save . 'test_view.php';
include_once $path_unit_save . 'test_view_component.php';
include_once $path_unit_save . 'test_view_component_link.php';
include_once $path_unit_save . 'test_value.php';
include_once $path_unit_save . 'test_cleanup.php';

// load the integration test functions
include_once $path_it . 'test_import.php';
include_once $path_it . 'test_export.php';

// libraries that can be dismissed, but still used to compare the result with the result of the legacy function
include_once $root_path . 'src/main/php/service/test/zu_lib_word_dsp.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_sql.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_link.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_sql_naming.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_value.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_word.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_word_db.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_calc.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_value_db.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_value_dsp.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_user.php';
include_once $root_path . 'src/main/php/service/test/zu_lib_html.php';

// load the test functions still in development
include_once $path_dev . 'test_legacy.php';

// the fixed system user used for testing
const TEST_USER_NAME = "zukunft.com system test";
const TEST_USER_PARTNER_NAME = "zukunft.com system test partner";
const TEST_USER_ID = "2";
const TEST_USER_PARTNER_ID = "2";
const TEST_USER_DESCRIPTION = "standard user view for all users";
const TEST_USER_IP = "66.249.64.95"; // used to check the blocking of an IP address

/*
Setting that should be moved to the system config table
*/

// switch for the email testing
const TEST_EMAIL = FALSE; // if set to true an email will be sent in case of errors and once a day an "everything fine" email is send

// TODO move the test names to the single objects and check for reserved names to avoid conflicts
// the basic test record for doing the pre check
// the word "Company" is assumed to have the ID 1
const TEST_WORD = "Company";

// some test words used for testing
const TW_ABB = "ABB";
const TW_NESN = "Nestlé";
const TW_VESTAS = "Vestas";
const TW_SALES = "Sales";
const TW_PRICE = "Price";
const TW_CHF = "CHF";
const TW_YEAR = "Year";
const TW_2013 = "2013";
const TW_2014 = "2014";
const TW_2015 = "2015";
const TW_2016 = "2016";
const TW_2017 = "2017";
const TW_2020 = "2020";
const TW_MIO = "million";
const TW_K = "thousand";
const TW_M = "mio";
const TW_PCT = "percent";
const TW_CF = "cash flow statement";
const TW_TAX = "Income taxes";
const TW_SECT_AUTO = "Discrete Automation and Motion";
const TW_BALANCE = "balance sheet";

// some test phrases used for testing
const TP_ABB = "ABB (Company)";
const TP_FOLLOW = "2014 is follower of 2013";
const TP_TAXES = "Income taxes is part of cash flow statement";

// some formula parameter used for testing
const TF_INCREASE = "increase";
const TF_SECTOR = "sectorweight";

// some numbers used to test the program
const TV_TEST_SALES_2016 = 1234;
const TV_TEST_SALES_2017 = 2345;
const TV_ABB_SALES_2013 = 45548;
const TV_ABB_SALES_2014 = 46000;
const TV_ABB_PRICE_20200515 = 17.08;
const TV_NESN_SALES_2016 = 89469;
const TV_ABB_SALES_AUTO_2013 = 9915;
const TV_DAN_SALES_USA_2016 = '11%';

const TV_TEST_SALES_INCREASE_2017_FORMATTED = '90.03 %';
const TV_NESN_SALES_2016_FORMATTED = '89\'469';

// some source used to test the program
const TS_IPCC_AR6_SYNTHESIS = 'IPCC AR6 Synthesis Report: Climate Change 2022';
const TS_IPCC_AR6_SYNTHESIS_URL = 'https://www.ipcc.ch/report/sixth-assessment-report-cycle/';
const TS_NESN_2016_NAME = 'Nestlé Financial Statement 2016';


// settings for add, change and deletion tests
// these names should not exist in the database
const TM_ADD = "Test Mask";
const TM_ADD_RENAMED = "Mask Test";
const TC_ADD = "Test Mask Component";
const TC_ADD_RENAMED = "Mask Component Test";
const TC_ADD2 = "Test Mask Component two";

// max time expected for each function execution
const TIMEOUT_LIMIT = 0.03; // time limit for normal functions
const TIMEOUT_LIMIT_PAGE = 0.1;  // time limit for complete webpage
const TIMEOUT_LIMIT_PAGE_SEMI = 0.6;  // time limit for complete webpage
const TIMEOUT_LIMIT_PAGE_LONG = 1.2;  // time limit for complete webpage
const TIMEOUT_LIMIT_DB = 0.2;  // time limit for database modification functions
const TIMEOUT_LIMIT_DB_MULTI = 0.9;  // time limit for many database modifications
const TIMEOUT_LIMIT_LONG = 3;    // time limit for complex functions
const TIMEOUT_LIMIT_IMPORT = 12;    // time limit for complex import tests in seconds

// views used for testing
const TD_COMPLETE = "complete";                      // the default and base view for all words
const TD_COMPANY_LIST = "Company list with main ratios"; // the default view for the company list

// ---------------
// prepare testing
// ---------------

function test_start()
{

    global $usr1; // the main user for testing
    global $usr2; // a second testing user e.g. to test the user sandbox
    global $start_time; // time when all tests have started
    global $exe_start_time; // time when the single test has started (end the end time of all tests)

    // the counter of the error for the summery
    global $error_counter;
    global $timeout_counter;
    global $total_tests;

    // create the system test user to simulate the user sandbox
    // e.g. a value owned by the first user cannot be adjusted by the second user instead a user specific value is created
    // instead a user specific value is created
    // for testing $usr is the user who has started the test ans $usr1 and $usr2 are the users used for simulation
    $usr1 = new user_dsp;
    $usr1->name = TEST_USER_NAME;
    $usr1->load_test_user();

    $usr2 = new user_dsp;
    $usr2->name = TEST_USER_PARTNER_NAME;
    $usr2->load_test_user();

    // init the times to be able to detect potential timeouts
    $start_time = microtime(true);
    $exe_start_time = $start_time;

    // reset the error counters
    $error_counter = 0;
    $timeout_counter = 0;
    $total_tests = 0;

}


// ---------------------------
// function to support testing
// ---------------------------

/**
 * display the result of one test e.g. if adding a value has been successful
 *
 * @return bool true if the test result is fine
 */
function test_dsp($msg, $target, $result, $exe_max_time = TIMEOUT_LIMIT, $comment = '', $test_type = ''): bool
{
    global $error_counter;
    global $timeout_counter;
    global $total_tests;
    global $exe_start_time;

    // init the test result vars
    $test_result = false;
    $txt = '';
    $new_start_time = microtime(true);
    $since_start = $new_start_time - $exe_start_time;

    // do the compare depending on the type
    if (is_array($target) and is_array($result)) {
        sort($target);
        sort($result);
        // in an array each value needs to be the same
        $test_result = true;
        foreach ($result as $key => $value) {
            if ($value != $target[$key]) {
                $test_result = false;
            }
        }
    } elseif (is_numeric($result) && is_numeric($target)) {
        $result = round($result, 7);
        $target = round($target, 7);
        if ($result == $target) {
            $test_result = true;
        }
    } else {
        $result = test_uncolor($result);
        if ($result == $target) {
            $test_result = true;
        }
    }

    // display the result
    if ($test_result) {
        // check if executed in a reasonable time and if the result is fine
        if ($since_start > $exe_max_time) {
            $txt .= '<p style="color:orange">TIMEOUT' . $msg;
            $timeout_counter++;
        } else {
            $txt .= '<p style="color:green">OK' . $msg;
            $test_result = true;
        }
    } else {
        $txt .= '<p style="color:red">Error' . $msg;
        $error_counter++;
        // todo: create a ticket
    }

    // explain the check
    if (is_array($target)) {
        if ($test_type == 'contains') {
            $txt .= " should contain \"" . dsp_array($target) . "\"";
        } else {
            $txt .= " should be \"" . dsp_array($target) . "\"";
        }
    } else {
        if ($test_type == 'contains') {
            $txt .= " should contain \"" . $target . "\"";
        } else {
            $txt .= " should be \"" . $target . "\"";
        }
    }
    if ($result == $target) {
        if ($test_type == 'contains') {
            $txt .= " and it contains ";
        } else {
            $txt .= " and it is ";
        }
    } else {
        if ($test_type == 'contains') {
            $txt .= ", but does not contain ";
        } else {
            $txt .= ", but it is ";
        }
    }
    if (is_array($result)) {
        if ($result != null) {
            if (is_array($result[0])) {
                $txt .= "\"";
                foreach ($result as $result_item) {
                    if ($result_item <> $result[0]) {
                        $txt .= ",";
                    }
                    $txt .= implode(":", $result_item);
                }
                $txt .= "\"";
            } else {
                $txt .= "\"" . dsp_array($result) . "\"";
            }
        }
    } else {
        $txt .= "\"" . $result . "\"";
    }
    if ($comment <> '') {
        $txt .= ' (' . $comment . ')';
    }

    // show the execution time
    $txt .= ', took ';
    $txt .= round($since_start, 4) . ' seconds';

    // --- and finally display the test result
    $txt .= '</p>';
    echo $txt;
    echo "\n";
    flush();

    $total_tests++;
    $exe_start_time = $new_start_time;

    return $test_result;
}

// to test if the save result is fine which is the case if the text is only a number or empty
function num2bool($in_txt): bool
{
    $result = false;
    if (intval($in_txt) > 0 or $in_txt == '') {
        $result = true;
    }
    return $result;
}

function str2bool(string $in_txt): bool
{
    $result = false;
    if ($in_txt == '') {
        $result = true;
    }
    return $result;
}

// display the difference between strings excluding non display chars
function test_show_diff($target, $result)
{
    $diff_lst = str_diff($target, $result);
    foreach (array_keys($diff_lst) as $diff_key) {
        echo $diff_key;
    }
    echo '<br>';
}

// remove color setting from the result to reduce confusion by misleading colors
function test_uncolor($result)
{
    $result = str_replace('<p style="color:red">', '', $result);
    $result = str_replace('<p class="user_specific">', '', $result);
    $result = str_replace('</p>', '', $result);
    return $result;
}

// similar to test_show_result, but the target only needs to be part of the result
// e.g. "ABB" is part of the company word list
function test_dsp_contains($test_text, $target, $result, $exe_max_time = TIMEOUT_LIMIT, $comment = ''): bool
{
    if (strpos($result, $target) === false) {
        $result = $target . ' not found in ' . $result;
    } else {
        $result = $target;
    }
    return test_dsp($test_text, $target, $result, $exe_max_time, $comment, 'contains');
}

// the HTML code to display the header text
function test_header($header_text)
{
    echo '<br><br><h2>' . $header_text . '</h2><br>';
}

// the HTML code to display the subheader text
function test_subheader($header_text)
{
    echo '<br><h3>' . $header_text . '</h3><br>';
}

// external string diff only for testing
// TODO review or remove
function str_diff($from, $to): array
{
    $diffValues = array();
    $diffMask = array();

    $dm = array();
    $do_diff = false;
    if ($from != $to) {
        $do_diff = true;
    }
    if (is_array($from)) {
        $n1 = count($from);
    } else {
        if ($from != "") {
            log_warning('Array expected in str_diff');
            $do_diff = false;
        }
    }
    if (is_array($to)) {
        $n2 = count($to);
    } else {
        if ($to != "") {
            log_warning('Array expected in str_diff');
            $do_diff = false;
        }
    }


    if ($do_diff) {
        for ($j = -1; $j < $n2; $j++) $dm[-1][$j] = 0;
        for ($i = -1; $i < $n1; $i++) $dm[$i][-1] = 0;
        for ($i = 0; $i < $n1; $i++) {
            for ($j = 0; $j < $n2; $j++) {
                if ($from[$i] == $to[$j]) {
                    $ad = $dm[$i - 1][$j - 1];
                    $dm[$i][$j] = $ad + 1;
                } else {
                    $a1 = $dm[$i - 1][$j];
                    $a2 = $dm[$i][$j - 1];
                    $dm[$i][$j] = max($a1, $a2);
                }
            }
        }

        $i = $n1 - 1;
        $j = $n2 - 1;
        while (($i > -1) || ($j > -1)) {
            if ($j > -1) {
                if ($dm[$i][$j - 1] == $dm[$i][$j]) {
                    $diffValues[] = $to[$j];
                    $diffMask[] = 1;
                    $j--;
                    continue;
                }
            }
            if ($i > -1) {
                if ($dm[$i - 1][$j] == $dm[$i][$j]) {
                    $diffValues[] = $from[$i];
                    $diffMask[] = -1;
                    $i--;
                    continue;
                }
            }
            {
                $diffValues[] = $from[$i];
                $diffMask[] = 0;
                $i--;
                $j--;
            }
        }

        $diffValues = array_reverse($diffValues);
        $diffMask = array_reverse($diffMask);
    }

    return array('values' => $diffValues, 'view' => $diffMask);
}

/*  
  testing functions - to check the words, values and formulas that should always be in the system
  -----------------
  
  add_* to create an object and save it in the database to prepare the testing (not used for all classes)
  load_* just load the object, but does not create the object
  test_* additional creates the object if needed and checks if it has been persistent
  
  * is for the name of the class, so the long name e.g. word not wrd
  
*/

function load_word($wrd_name): word
{
    global $usr;
    $wrd = new word;
    $wrd->usr = $usr;
    $wrd->name = $wrd_name;
    $wrd->load();
    return $wrd;
}

function test_word($wrd_name, $wrd_type_code_id = null): word
{
    $wrd = load_word($wrd_name);
    if ($wrd->id == 0) {
        $wrd->name = $wrd_name;
        $wrd->save();
    }
    if ($wrd_type_code_id != null) {
        $wrd->type_id = cl(db_cl::WORD_TYPE, $wrd_type_code_id);
        $wrd->save();
    }
    $target = $wrd_name;
    test_dsp('word', $target, $wrd->name);
    return $wrd;
}

function load_formula($frm_name): formula
{
    global $usr;
    $frm = new formula_dsp;
    $frm->usr = $usr;
    $frm->name = $frm_name;
    $frm->load();
    return $frm;
}

// get or create a formula
function test_formula($frm_name, $frm_text): formula
{
    $frm = load_formula($frm_name);
    if ($frm->id == 0) {
        $frm->name = $frm_name;
        $frm->usr_text = $frm_text;
        $frm->set_ref_text();
        $frm->save();
    }
    $target = $frm_name;
    test_dsp('formula', $target, $frm->name);
    return $frm;
}

function load_phrase($phr_name): phrase
{
    global $usr;
    $phr = new phrase;
    $phr->usr = $usr;
    $phr->name = $phr_name;
    $phr->load();
    return $phr;
}

function test_phrase($phr_name): phrase
{
    $phr = load_phrase($phr_name);
    $target = $phr_name;
    test_dsp('phrase', $target, $phr->name);
    return $phr;
}

// create a phrase list object based on an array of strings
function load_word_list($array_of_word_str): word_list
{
    global $usr;
    $wrd_lst = new word_list;
    $wrd_lst->usr = $usr;
    foreach ($array_of_word_str as $word_str) {
        $wrd_lst->add_name($word_str);
    }
    $wrd_lst->load();
    return $wrd_lst;
}

function test_word_list($array_of_word_str): word_list
{
    $wrd_lst = load_word_list($array_of_word_str);
    $target = '"' . implode('","', $array_of_word_str) . '"';
    $result = $wrd_lst->name();
    test_dsp(', word list', $target, $result);
    return $wrd_lst;
}

// create a phrase list object based on an array of strings
function load_phrase_list($array_of_word_str): phrase_list
{
    global $usr;
    $phr_lst = new phrase_list;
    $phr_lst->usr = $usr;
    foreach ($array_of_word_str as $word_str) {
        $phr_lst->add_name($word_str);
    }
    $phr_lst->load();
    return $phr_lst;
}

function test_phrase_list($array_of_word_str): phrase_list
{
    $phr_lst = load_phrase_list($array_of_word_str);
    $target = '"' . implode('","', $array_of_word_str) . '"';
    $result = $phr_lst->name();
    test_dsp(', phrase list', $target, $result);
    return $phr_lst;
}

function load_value($array_of_word_str): value
{
    global $usr;
    $phr_lst = load_phrase_list($array_of_word_str);
    $val = new value;
    $val->ids = $phr_lst->ids;
    $val->usr = $usr;
    $val->load();
    return $val;
}

function test_value($array_of_word_str, $target): value
{
    global $usr;
    $phr_lst = load_phrase_list($array_of_word_str);
    $val = load_value($array_of_word_str);
    if ($val->id == 0) {
        $val = new value;
        $val->ids = $phr_lst->ids;
        $val->usr = $usr;
        $val->number = $target;
        $val->save();
    }
    $result = $val->number;
    test_dsp(', value->load for a phrase list ' . $phr_lst->name(), $target, $result);
    return $val;
}

function add_value($array_of_word_str, $target): value
{
    global $usr;
    $phr_lst = load_phrase_list($array_of_word_str);
    $val = new value;
    $val->ids = $phr_lst->ids;
    $val->usr = $usr;
    $val->number = $target;
    $val->save();
    return $val;
}

function load_source($src_name): source
{
    global $usr;
    $src = new source;
    $src->usr = $usr;
    $src->name = $src_name;
    $src->load();
    return $src;
}

function test_source($src_name): source
{
    $src = load_source($src_name);
    if ($src->id == 0) {
        $src->name = $src_name;
        $src->save();
    }
    $target = $src_name;
    test_dsp('source', $target, $src->name);
    return $src;
}

function load_ref(string $wrd_name, string $type_name): ref
{
    global $usr;

    $wrd = load_word($wrd_name);
    $phr = $wrd->phrase();

    $ref = new ref;
    $ref->usr = $usr;
    $ref->phr = $phr;
    $ref->ref_type = get_ref_type($type_name);
    if ($phr->id != 0) {
        $ref->load();
    }
    return $ref;
}

function test_ref(string $wrd_name, string $external_key, string $type_name): ref
{
    $wrd = test_word($wrd_name);
    $phr = $wrd->phrase();
    $ref = load_ref($wrd->name, $type_name);
    if ($ref->id == 0) {
        $ref->phr = $phr;
        $ref->ref_type = get_ref_type($type_name);
        $ref->external_key = $external_key;
        $ref->save();
    }
    $target = $external_key;
    test_dsp('ref', $target, $ref->external_key);
    return $ref;
}

function load_view($dsp_name): view
{
    global $usr;
    $dsp = new view_dsp;
    $dsp->usr = $usr;
    $dsp->name = $dsp_name;
    $dsp->load();
    return $dsp;
}

// same as load_view but for a specific usr
function load_view_usr($dsp_name, $usr): view
{
    $dsp = new view_dsp;
    $dsp->usr = $usr;
    $dsp->name = $dsp_name;
    $dsp->load();
    return $dsp;
}

function get_view($dsp_name): view
{
    global $usr;
    $dsp = load_view($dsp_name);
    if ($dsp->id == 0 or $dsp->id == Null) {
        $dsp->usr = $usr;
        $dsp->name = $dsp_name;
        $dsp->save();
    }
    return $dsp;
}

function test_view($dsp_name): view
{
    $dsp = load_view($dsp_name);
    if ($dsp->id == 0) {
        $dsp->name = $dsp_name;
        $dsp->save();
    }
    $target = $dsp_name;
    test_dsp('view', $target, $dsp->name);
    return $dsp;
}

function load_view_component($cmp_name): view_component
{
    global $usr;
    $cmp = new view_component;
    $cmp->usr = $usr;
    $cmp->name = $cmp_name;
    $cmp->load();
    return $cmp;
}

// same as load_view_component but for a specific usr
function load_view_component_usr($cmp_name, $usr): view_component
{
    $cmp = new view_component;
    $cmp->usr = $usr;
    $cmp->name = $cmp_name;
    $cmp->load();
    return $cmp;
}

function get_view_component($cmp_name): view_component
{
    global $usr;
    $cmp = load_view_component($cmp_name);
    if ($cmp->id == 0 or $cmp->id == Null) {
        $cmp->usr = $usr;
        $cmp->name = $cmp_name;
        $cmp->save();
    }
    return $cmp;
}

function test_view_component($cmp_name): view_component
{
    $cmp = load_view_component($cmp_name);
    $target = $cmp_name;
    test_dsp('view component', $target, $cmp->name);
    return $cmp;
}

// check if a word link exists and if not and requested create it
// $phrase_name should be set if the standard name for the link should not be used
function test_word_link($from_name, $verb_code_id, $to_name, $target = '', $phrase_name = '', $autocreate = true)
{
    global $usr;
    global $verbs;

    $result = '';

    // create the words if needed
    $wrd_from = load_word($from_name);
    if ($wrd_from->id <= 0 and $autocreate) {
        $wrd_from->name = $from_name;
        $wrd_from->save();
        $wrd_from->load();
    }
    $wrd_to = load_word($to_name);
    if ($wrd_to->id <= 0 and $autocreate) {
        $wrd_to->name = $to_name;
        $wrd_to->save();
        $wrd_to->load();
    }
    $from = $wrd_from->phrase();
    $to = $wrd_to->phrase();

    $vrb = $verbs->get_verb($verb_code_id);

    $lnk_test = new word_link;
    if ($from->id == 0 or $to->id == 0) {
        log_err("Words " . $from_name . " and " . $to_name . " cannot be created");
    } else {
        // check if the forward link exists
        $lnk_test->from = $from;
        $lnk_test->verb = $vrb;
        $lnk_test->to = $to;
        $lnk_test->usr = $usr;
        $lnk_test->load();
        if ($lnk_test->id > 0) {
            // refresh the given name if needed
            if ($phrase_name <> '' and $lnk_test->description() <> $phrase_name) {
                $lnk_test->description = $phrase_name;
                $lnk_test->save();
                $lnk_test->load();
            }
            $result = $lnk_test;
        } else {
            // check if the backward link exists
            $lnk_test->from = $to;
            $lnk_test->verb = $vrb;
            $lnk_test->to = $from;
            $lnk_test->usr = $usr;
            $lnk_test->load();
            $result = $lnk_test;
            // create the link if requested
            if ($lnk_test->id <= 0 and $autocreate) {
                $lnk_test->from = $from;
                $lnk_test->verb = $vrb;
                $lnk_test->to = $to;
                $lnk_test->save();
                $lnk_test->load();
                // refresh the given name if needed
                if ($lnk_test->id <> 0 and $phrase_name <> '' and $lnk_test->description() <> $phrase_name) {
                    $lnk_test->description = $phrase_name;
                    $lnk_test->save();
                    $lnk_test->load();
                }
                $result = $lnk_test;
            }
        }
    }
    // fallback setting of target f
    $result_text = '';
    if (isset($lnk_test)) {
        $result_text = $lnk_test->description();
        if ($target == '') {
            $target = $lnk_test->name();
        }
    }
    test_dsp('word link', $target, $result_text, TIMEOUT_LIMIT_DB);
    return $result;
}


function test_formula_link($formula_name, $word_name, $autocreate = true): string
{
    global $usr;

    $result = '';

    $frm = new formula;
    $frm->usr = $usr;
    $frm->name = $formula_name;
    $frm->load();
    $phr = new word;
    $phr->name = $word_name;
    $phr->usr = $usr;
    $phr->load();
    if ($frm->id > 0 and $phr->id <> 0) {
        $frm_lnk = new formula_link;
        $frm_lnk->usr = $usr;
        $frm_lnk->fob = $frm;
        $frm_lnk->tob = $phr;
        $frm_lnk->load();
        if ($frm_lnk->id > 0) {
            $result = $frm_lnk->fob->name . ' is linked to ' . $frm_lnk->tob->name;
            $target = $formula_name . ' is linked to ' . $word_name;
            test_dsp('formula_link', $target, $result);
        } else {
            if ($autocreate) {
                $frm_lnk->save();
            }
        }
    }
    return $result;
}


// -----------------------------------------------
// testing functions to create the main time value
// -----------------------------------------------

function zu_test_time_setup(): string
{
    global $usr, $db_con;
    $result = '';
    $this_year = date('Y');
    $prev_year = '';
    $test_years = cfg_get('test_years', $db_con);
    if ($test_years == '') {
        log_warning('Configuration of test years is missing', 'test_base->zu_test_time_setup');
    } else {
        $start_year = $this_year - $test_years;
        $end_year = $this_year + $test_years;
        for ($year = $start_year; $year <= $end_year; $year++) {
            $this_year = $year;
            test_word(strval($this_year));
            $wrd_lnk = test_word_link(TW_YEAR, verb::IS_A, $this_year, true, '');
            $result = $wrd_lnk->name;
            if ($prev_year <> '') {
                test_word_link($prev_year, verb::DBL_FOLLOW, $this_year, true, '');
            }
            $prev_year = $this_year;
        }
    }

    return $result;
}

// display the test results
function zu_test_dsp_result()
{

    global $start_time;
    global $error_counter;
    global $timeout_counter;
    global $total_tests;

    echo '<br>';
    echo '<h2>';
    echo $total_tests . ' test cases<br>';
    echo $timeout_counter . ' timeouts<br>';
    echo $error_counter . ' errors<br>';
    echo "<br>";
    $since_start = microtime(true) - $start_time;
    echo round($since_start, 4) . ' seconds for testing zukunft.com</h2>';
    echo '<br>';
    echo '<br>';
}

// display the test results
function test_dsp_result()
{

    global $start_time;
    global $error_counter;
    global $timeout_counter;
    global $total_tests;

    echo "\n";
    $since_start = microtime(true) - $start_time;
    echo round($since_start, 4) . ' seconds for testing zukunft.com';
    echo "\n";
    echo $total_tests . ' test cases';
    echo "\n";
    echo $timeout_counter . ' timeouts';
    echo "\n";
    echo $error_counter . ' errors';
}