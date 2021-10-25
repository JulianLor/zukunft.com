<?php

/*

  test_cleanup.php - TESTing cleanup to remove any remaining test records
  ---------------
  

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

function run_test_cleanup()
{

    global $db_con;

    global $usr;
    global $usr2;

    global $test_val_lst;

    // make sure that all test elements are removed even if some tests have failed to have a clean setup for the next test
    test_header('Cleanup the test');

    if ($test_val_lst != null) {
        foreach ($test_val_lst as $val_id) {
            if ($val_id > 0) {
                // request to delete the added test value
                $val = new value;
                $val->id = $val_id;
                $val->usr = $usr;
                $val->load();
                // check again, because some id may be added twice
                if ($val->id > 0) {
                    $result = $val->del();
                    $target = true;
                    test_dsp('value->del test value for "' . word::TN_RENAMED . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);
                }
            }
        }
    }

    // secure cleanup the test views
    // todo: if a user has changed the view during the test, delete also the user views

    test_view_cmp_unlink(view::TN_COMPLETE, view_cmp::TN_TITLE);
    test_view_cmp_unlink(view::TN_COMPLETE, view_cmp::TN_VALUES);
    test_view_cmp_unlink(view::TN_COMPLETE, view_cmp::TN_RESULTS);

    test_view_cmp_unlink(view::TN_TABLE, view_cmp::TN_TITLE);
    test_view_cmp_unlink(view::TN_TABLE, view_cmp::TN_TABLE);

    // load the test view
    $dsp = load_view(view::TN_ADD);
    if ($dsp->id <= 0) {
        $dsp = load_view(view::TN_RENAMED);
    }

    // load the test view for user 2
    $dsp_usr2 = load_view_usr(view::TN_ADD, $usr2);
    if ($dsp_usr2->id <= 0) {
        $dsp_usr2 = load_view_usr(view::TN_RENAMED, $usr2);
    }

    // load the first test view component
    $cmp = load_view_component(view_cmp::TN_ADD);
    if ($cmp->id <= 0) {
        $cmp = load_view_component(view_cmp::TN_RENAMED);
    }

    // load the first test view component for user 2
    $cmp_usr2 = load_view_component_usr(view_cmp::TN_ADD, $usr2);
    if ($cmp_usr2->id <= 0) {
        $cmp_usr2 = load_view_component_usr(view_cmp::TN_RENAMED, $usr2);
    }

    // load the second test view component
    $cmp2 = load_view_component(view_cmp::TN_ADD2);

    // load the second test view component for user 2
    $cmp2_usr2 = load_view_component_usr(view_cmp::TN_ADD2, $usr2);

    // check if the test components have been unlinked for user 2
    if ($dsp_usr2->id > 0 and $cmp_usr2->id > 0) {
        $result = $cmp_usr2->unlink($dsp_usr2);
        $target = '';
        test_dsp('cleanup: unlink first component "' . $cmp_usr2->name . '" from "' . $dsp_usr2->name . '" for user 2', $target, $result, TIMEOUT_LIMIT_DB_MULTI);
    }

    // check if the test components have been unlinked
    if ($dsp->id > 0 and $cmp->id > 0) {
        $result = $cmp->unlink($dsp);
        $target = '';
        test_dsp('cleanup: unlink first component "' . $cmp->name . '" from "' . $dsp->name . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);
    }

    // unlink the second component
    // error at the moment: if the second user is still using the link,
    // the second user does not get the owner
    // instead a foreign key error happens
    if ($dsp->id > 0 and $cmp2->id > 0) {
        $result = $cmp2->unlink($dsp);
        $target = '';
        test_dsp('cleanup: unlink second component "' . $cmp2->name . '" from "' . $dsp->name . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);
    }

    // unlink the second component for user 2
    if ($dsp_usr2->id > 0 and $cmp2_usr2->id > 0) {
        $result = $cmp2_usr2->unlink($dsp_usr2);
        $target = '';
        test_dsp('cleanup: unlink second component "' . $cmp2_usr2->name . '" from "' . $dsp_usr2->name . '" for user 2', $target, $result, TIMEOUT_LIMIT_DB_MULTI);
    }

    // request to delete the added test views
    foreach (view_cmp::RESERVED_VIEW_COMPONENTS AS $cmp_name) {
        $cmp = load_view_component($cmp_name);
        if ($cmp->id > 0) {
            $result = $cmp->del();
            $target = true;
            test_dsp('view_component->del of "' . $cmp_name . '"', $target, $result);
        }
    }

    // request to delete the added test views
    foreach (view::RESERVED_VIEWS AS $dsp_name) {
        $dsp = load_view($dsp_name);
        if ($dsp->id > 0) {
            $result = $dsp->del();
            $target = true;
            test_dsp('view->del of "' . $dsp_name . '"', $target, $result);
        }
    }

    // reload the first test view component for user 2
    $cmp_usr2 = load_view_component_usr(view_cmp::TN_ADD, $usr2);
    if ($cmp_usr2->id <= 0) {
        $cmp_usr2 = load_view_component_usr(view_cmp::TN_RENAMED, $usr2);
    }

    // request to delete the test view component for user 2
    if ($cmp_usr2->id > 0) {
        $result = $cmp_usr2->del();
        $target = true;
        test_dsp('cleanup: del of first component "' . view_cmp::TN_ADD . '" for user 2', $target, $result, TIMEOUT_LIMIT_DB);
    }

    // reload the first test view component
    $cmp = load_view_component(view_cmp::TN_ADD);
    if ($cmp->id <= 0) {
        $cmp = load_view_component(view_cmp::TN_RENAMED);
    }

    // request to delete the test view component
    if ($cmp->id > 0) {
        $result = $cmp->del();
        $target = true;
        //$target = '';
        test_dsp('cleanup: del of first component "' . view_cmp::TN_ADD . '"', $target, $result, TIMEOUT_LIMIT_DB);
    }

    // reload the second test view component
    $cmp2 = load_view_component(view_cmp::TN_ADD2);

    // request to delete the second added test view component
    if ($cmp2->id > 0) {
        $result = $cmp2->del();
        $target = true;
        //$target = '';
        test_dsp('cleanup: del of second component "' . view_cmp::TN_ADD2 . '"', $target, $result, TIMEOUT_LIMIT_DB);
    }

    // request to delete the second added test view component for user 2
    if ($cmp2_usr2->id > 0) {
        $result = $cmp2_usr2->del();
        $target = '';
        test_dsp('cleanup: del of second component "' . view_cmp::TN_ADD2 . '" for user 2', $target, $result, TIMEOUT_LIMIT_DB);
    }

    // reload the test view for user 2
    $dsp_usr2 = load_view_usr(view::TN_ADD, $usr2);
    if ($dsp_usr2->id <= 0) {
        $dsp_usr2 = load_view_usr(view::TN_RENAMED, $usr2);
    }

    // request to delete the added test view for user 2 first
    if ($dsp_usr2->id > 0) {
        $result = $dsp_usr2->del();
        $target = true;
        test_dsp('cleanup: del of view "' . view::TN_ADD . '" for user 2', $target, $result, TIMEOUT_LIMIT_DB);
    }

    // reload the test view
    $dsp = load_view(view::TN_ADD);
    if ($dsp->id <= 0) {
        $dsp = load_view(view::TN_RENAMED);
    }

    // request to delete the added test view
    if ($dsp->id > 0) {
        $result = $dsp->del();
        $target = true;
        test_dsp('cleanup: del of view "' . view::TN_ADD . '"', $target, $result, TIMEOUT_LIMIT_DB);
    }

    // request to delete the added test views
    foreach (view::RESERVED_VIEWS AS $dsp_name) {
        $dsp = load_view($dsp_name);
        if ($dsp->id > 0) {
            $result = $dsp->del();
            $target = true;
            test_dsp('view->del of "' . $dsp_name . '"', $target, $result);
        }
    }

    // request to delete the added test reference
    $ref = load_ref(word::TN_ADD, ref_type::WIKIDATA);
    if ($ref->id > 0) {
        $result = $ref->del();
        $target = true;
        test_dsp('ref->del of "' . word::TN_ADD . '"', $target, $result);
    }

    // request to delete the added test formulas
    foreach (formula::RESERVED_FORMULAS AS $frm_name) {
        $dsp = load_formula($frm_name);
        if ($dsp->id > 0) {
            $result = $dsp->del();
            $target = true;
            test_dsp('formula->del of "' . $frm_name . '"', $target, $result);
        }
    }

    // request to delete the added test word
    // todo: if a user has changed the word during the test, delete also the user words
    $wrd = load_word(word::TN_ADD);
    if ($wrd->id > 0) {
        $result = $wrd->del();
        $target = '1';
        test_dsp('word->del of "' . word::TN_ADD . '"', $target, $result);
    }

    // request to delete the renamed test word
    $wrd = load_word(word::TN_RENAMED);
    if ($wrd->id > 0) {
        $result = $wrd->del();
        $target = true;
        test_dsp('word->del of "' . word::TN_RENAMED . '"', $target, $result, TIMEOUT_LIMIT_DB);
    }

    // request to delete the renamed test source
    $src = load_source(source::TN_RENAMED);
    if ($src->id > 0) {
        $result = $src->del();
        $target = true;
        test_dsp('source->del of "' . source::TN_RENAMED . '"', $target, $result, TIMEOUT_LIMIT_DB);
    }

    echo $db_con->seq_reset(DB_TYPE_VALUE) . '<br>';
    echo $db_con->seq_reset(DB_TYPE_WORD) . '<br>';
    echo $db_con->seq_reset(DB_TYPE_FORMULA) . '<br>';
    echo $db_con->seq_reset(DB_TYPE_FORMULA_LINK) . '<br>';
    echo $db_con->seq_reset(DB_TYPE_VIEW) . '<br>';
    echo $db_con->seq_reset(DB_TYPE_VIEW_COMPONENT) . '<br>';
    echo $db_con->seq_reset(DB_TYPE_VIEW_COMPONENT_LINK) . '<br>';
    echo $db_con->seq_reset(DB_TYPE_SOURCE) . '<br>';

}