<?php

/*

    test/php/unit_write/view.php - write test VIEWS to the database and check the results
    ----------------------------
  

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

use api\view\view as view_api;
use api\word\word as word_api;
use cfg\view_type;
use cfg\view;
use html\view\view as view_dsp;
use cfg\log\change;
use cfg\log\change_log_table;
use cfg\sandbox_named;
use cfg\word;
use test\test_cleanup;
use const test\TIMEOUT_LIMIT_DB;
use const test\TIMEOUT_LIMIT_DB_MULTI;
use const test\TIMEOUT_LIMIT_LONG;

class view_test
{

    function run(test_cleanup $t): void
    {
        global $view_types;

        $back = 0;

        $t->header('Test the view class (classes/view.php)');

        // test the creation and changing of a view

        // test loading of one view
        $dsp_db = new view($t->usr1);
        $result = $dsp_db->load_by_name(view_api::TN_COMPLETE);
        $dsp = new view_dsp($dsp_db->api_json());
        $target = 0;
        if ($result > 0) {
            $target = $result;
        }
        $t->display('view->load of "' . $dsp->name() . '"', $target, $result);

        // test the complete view for one word
        $wrd = new word($t->usr1);
        $wrd->load_by_name(word_api::TN_CH);
        $result = $dsp->display($wrd, $back);
        // check if the view contains the word name
        $target = word_api::TN_CH;
        // TODO review and activate
        //$t->dsp_contains(', view->display "' . $dsp->name() . '" for "' . $wrd->name() . '" contains', $target, $result, TIMEOUT_LIMIT_LONG);
        // check if the view contains at least one value
        $target = 'back=' . $wrd->id() . '">8.51</a>';
        /* TODO fix the result display
        $t->dsp_contains(', view->display "' . $dsp->name . '" for "' . $wrd->name() . '" contains', $target, $result);
        // check if the view contains at least the main formulas
        $target = 'System Test Word Increase';
        $t->dsp_contains(', view->display "' . $dsp->name . '" for "' . $wrd->name() . '" contains', $target, $result);
        */
        /* TODO fix the result loading
        $target = 'back='.$wrd->id.'">0.79%</a>';
        $t->dsp_contains(', view->display "' . $dsp->name . '" for "' . $wrd->name() . '" contains', $target, $result);
        */

        // test adding of one view
        $dsp = new view($t->usr1);
        $dsp->set_name(view_api::TN_ADD);
        $dsp->description = 'Just added for testing';
        $result = $dsp->save();
        if ($dsp->id() > 0) {
            $result = $dsp->description;
        }
        $target = 'Just added for testing';
        $t->display('view->save for adding "' . $dsp->name() . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

        // check if the view name has been saved
        $dsp = new view($t->usr1);
        $dsp->load_by_name(view_api::TN_ADD, view::class);
        $result = $dsp->description;
        $target = 'Just added for testing';
        $t->display('view->load the added "' . $dsp->name() . '"', $target, $result);

        // check if the view adding has been logged
        $log = new change($t->usr1);
        $log->set_table(change_log_table::VIEW);
        $log->set_field(view::FLD_NAME);
        $log->row_id = $dsp->id();
        $result = $log->dsp_last(true);
        $target = 'zukunft.com system test added System Test View';
        $t->display('view->save adding logged for "' . view_api::TN_ADD . '"', $target, $result);

        // check if adding the same view again creates a correct error message
        $dsp = new view($t->usr1);
        $dsp->set_name(view_api::TN_ADD);
        $result = $dsp->save();
        $target = 'A view with the name "' . view_api::TN_ADD . '" already exists. Please use another name.'; // is this error message really needed???
        $target = '';
        $t->display('view->save adding "' . $dsp->name() . '" again', $target, $result, TIMEOUT_LIMIT_DB);

        // check if the view can be renamed
        $dsp = new view($t->usr1);
        $dsp->load_by_name(view_api::TN_ADD, view::class);
        $dsp->set_name(view_api::TN_RENAMED);
        $result = $dsp->save();
        $target = '';
        $t->display('view->save rename "' . view_api::TN_ADD . '" to "' . view_api::TN_RENAMED . '".', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

        // check if the view renaming was successful
        $dsp_renamed = new view($t->usr1);
        $result = $dsp_renamed->load_by_name(view_api::TN_RENAMED, view::class);
        if ($result) {
            if ($dsp_renamed->id() > 0) {
                $result = $dsp_renamed->name();
            }
        }
        $target = 'System Test View Renamed';
        $t->display('view->load renamed view "' . view_api::TN_RENAMED . '"', $target, $result);

        // check if the view renaming has been logged
        $log = new change($t->usr1);
        $log->set_table(change_log_table::VIEW);
        $log->set_field(view::FLD_NAME);
        $log->row_id = $dsp_renamed->id();
        $result = $log->dsp_last(true);
        $target = 'zukunft.com system test changed System Test View to System Test View Renamed';
        $t->display('view->save rename logged for "' . view_api::TN_RENAMED . '"', $target, $result);

        // check if the view parameters can be added
        $dsp_renamed->description = 'Just added for testing the user sandbox';
        $dsp_renamed->type_id = $view_types->id(view_type::WORD_DEFAULT);
        $result = $dsp_renamed->save();
        $target = '';
        $t->display('view->save all view fields beside the name for "' . view_api::TN_RENAMED . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

        // check if the view parameters have been added
        $dsp_reloaded = new view($t->usr1);
        $dsp_reloaded->load_by_name(view_api::TN_RENAMED, view::class);
        $result = $dsp_reloaded->description;
        $target = 'Just added for testing the user sandbox';
        $t->display('view->load comment for "' . view_api::TN_RENAMED . '"', $target, $result);
        $result = $dsp_reloaded->type_id;
        $target = $view_types->id(view_type::WORD_DEFAULT);
        $t->display('view->load type_id for "' . view_api::TN_RENAMED . '"', $target, $result);

        // check if the view parameter adding have been logged
        $log = new change($t->usr1);
        $log->set_table(change_log_table::VIEW);
        $log->set_field(sandbox_named::FLD_DESCRIPTION);
        $log->row_id = $dsp_reloaded->id();
        $result = $log->dsp_last(true);
        // TODO to check
        $target = 'zukunft.com system test added Just added for testing the user sandbox';
        $target = 'zukunft.com system test changed Just added for testing to Just added for testing the user sandbox';
        $t->display('view->load comment for "' . view_api::TN_RENAMED . '" logged', $target, $result);
        $log->set_field(view::FLD_TYPE);
        $result = $log->dsp_last(true);
        $target = 'zukunft.com system test added word default';
        $t->display('view->load view_type_id for "' . view_api::TN_RENAMED . '" logged', $target, $result);

        // check if a user specific view is created if another user changes the view
        $dsp_usr2 = new view($t->usr2);
        $dsp_usr2->load_by_name(view_api::TN_RENAMED, view::class);
        $dsp_usr2->description = 'Just changed for testing the user sandbox';
        $dsp_usr2->type_id = $view_types->id(view_type::ENTRY);
        $result = $dsp_usr2->save();
        $target = '';
        $t->display('view->save all view fields for user 2 beside the name for "' . view_api::TN_RENAMED . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

        // check if a user specific view changes have been saved
        $dsp_usr2_reloaded = new view($t->usr2);
        $dsp_usr2_reloaded->load_by_name(view_api::TN_RENAMED, view::class);
        $result = $dsp_usr2_reloaded->description;
        $target = 'Just changed for testing the user sandbox';
        $t->display('view->load comment for "' . view_api::TN_RENAMED . '"', $target, $result);
        $result = $dsp_usr2_reloaded->type_id;
        $target = $view_types->id(view_type::ENTRY);
        $t->display('view->load type_id for "' . view_api::TN_RENAMED . '"', $target, $result);

        // check the view for the original user remains unchanged
        $dsp_reloaded = new view($t->usr1);
        $dsp_reloaded->load_by_name(view_api::TN_RENAMED, view::class);
        $result = $dsp_reloaded->description;
        $target = 'Just added for testing the user sandbox';
        $t->display('view->load comment for "' . view_api::TN_RENAMED . '"', $target, $result);
        $result = $dsp_reloaded->type_id;
        $target = $view_types->id(view_type::WORD_DEFAULT);
        $t->display('view->load type_id for "' . view_api::TN_RENAMED . '"', $target, $result);

        // check if undo all specific changes removes the user view
        $dsp_usr2 = new view($t->usr2);
        $dsp_usr2->load_by_name(view_api::TN_RENAMED, view::class);
        $dsp_usr2->description = 'Just added for testing the user sandbox';
        $dsp_usr2->type_id = $view_types->id(view_type::WORD_DEFAULT);
        $result = $dsp_usr2->save();
        $target = '';
        $t->display('view->save undo the user view fields beside the name for "' . view_api::TN_RENAMED . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

        // check if a user specific view changes have been saved
        $dsp_usr2_reloaded = new view($t->usr2);
        $dsp_usr2_reloaded->load_by_name(view_api::TN_RENAMED, view::class);
        $result = $dsp_usr2_reloaded->description;
        $target = 'Just added for testing the user sandbox';
        $t->display('view->load comment for "' . view_api::TN_RENAMED . '"', $target, $result);
        $result = $dsp_usr2_reloaded->type_id;
        $target = $view_types->id(view_type::WORD_DEFAULT);
        $t->display('view->load type_id for "' . view_api::TN_RENAMED . '"', $target, $result);

        // redo the user specific view changes
        // check if the user specific changes can be removed with one click

    }

    function create_test_views(test_cleanup $t): void
    {
        $t->header('Check if all base views are existing');

        foreach (view_api::TEST_VIEWS_AUTO_CREATE as $view_name) {
            $t->test_view($view_name);
        }

        // modify the special test cases
        global $usr;
        $dsp = new view($usr);
        $dsp->load_by_name(view_api::TN_EXCLUDED);
        $dsp->set_excluded(true);
        $dsp->save();
    }

}