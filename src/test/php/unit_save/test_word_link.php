<?php

/*

  test_word_link.php - TESTing of the WORD LINK functions
  ------------------
  

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
    along with zukunft.com. If not, see <http://www.gnu.org/licenses/gpl.html>.

    To contact the authors write to:
    Timon Zielonka <timon@zukunft.com>

    Copyright (c) 1995-2021 zukunft.com AG, Zurich
    Heang Lor <heang@zukunft.com>

    http://zukunft.com

*/

// to create the base word links create_base_phrases is used

// --------------------------------------
// start testing the system functionality 
// --------------------------------------

function run_word_link_test(testing $t)
{

    $t->header('Test the word link class (classes/word_link.php)');

    // create the group test word
    $wrd_company = $t->test_word(word::TN_COMPANY);

    // check the triple usage for Zurich (City) and Zurich (Canton)
    $wrd_zh = $t->load_word(word::TN_ZH);
    $wrd_city = $t->load_word(word::TN_CITY);
    $wrd_canton = $t->load_word(word::TN_CANTON);

    // ... now test the Canton Zurich
    $lnk_canton = new word_link($t->usr1);
    $lnk_canton->from->id = $wrd_zh->id;
    $lnk_canton->verb->id = cl(db_cl::VERB, verb::IS_A);
    $lnk_canton->to->id = $wrd_canton->id;
    $lnk_canton->load();
    $target = word::TN_ZH . ' (' . word::TN_CANTON . ')';
    $result = $lnk_canton->name;
    $t->dsp('triple->load for Canton Zurich', $target, $result, TIMEOUT_LIMIT_DB);

    // ... now test the Canton Zurich using the name function
    $target = word::TN_ZH . ' (' . word::TN_CANTON . ')';
    $result = $lnk_canton->name();
    $t->dsp('triple->load for Canton Zurich using the function', $target, $result);

    // ... now test the Insurance Zurich
    $lnk_company = new word_link($t->usr1);
    $lnk_company->from->id = $wrd_zh->id;
    $lnk_company->verb->id = cl(db_cl::VERB, verb::IS_A);
    $lnk_company->to->id = $wrd_company->id;
    $lnk_company->load();
    $target = phrase::TN_ZH_COMPANY;
    $result = $lnk_company->name;
    $t->dsp('triple->load for ' . phrase::TN_ZH_COMPANY . '', $target, $result);

    // ... now test the Insurance Zurich using the name function
    $target = phrase::TN_ZH_COMPANY;
    $result = $lnk_company->name();
    $t->dsp('triple->load for ' . phrase::TN_ZH_COMPANY . ' using the function', $target, $result);

    // link the added word to the test word
    $wrd_added = $t->load_word(word::TN_RENAMED);
    $wrd = $t->load_word(TEST_WORD);
    $vrb = new verb;
    $vrb->id = cl(db_cl::VERB, verb::IS_A);
    $vrb->usr = $t->usr1;
    $vrb->load();
    $lnk = new word_link($t->usr1);
    $lnk->from->id = $wrd_added->id;
    $lnk->verb->id = $vrb->id;
    $lnk->to->id = $wrd->id;
    $result = $lnk->save();
    $target = '';
    $t->dsp('triple->save "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

    echo "... and also testing the user log link class (classes/user_log_link.php)<br>";

    // ... check the correct logging
    $log = new user_log_link;
    $log->table = 'word_links';
    $log->new_from_id = $wrd_added->id;
    $log->new_link_id = $vrb->id;
    $log->new_to_id = $wrd->id;
    $log->usr = $t->usr1;
    $result = $log->dsp_last(true);
    $target = 'zukunft.com system test linked ' . word::TN_RENAMED . ' to ' . TEST_WORD . '';
    $t->dsp('triple->save logged for "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '"', $target, $result);

    // ... check if the link is shown correctly

    $lnk = new word_link($t->usr1);
    $lnk->from->id = $wrd_added->id;
    $lnk->verb->id = $vrb->id;
    $lnk->to->id = $wrd->id;
    $lnk->load();
    $result = $lnk->name;
    $target = '' . word::TN_RENAMED . ' (' . TEST_WORD . ')';
    $t->dsp('triple->load', $target, $result);
    // ... check if the link is shown correctly also for the second user
    $lnk2 = new word_link($t->usr2);
    $lnk2->from->id = $wrd_added->id;
    $lnk2->verb->id = $vrb->id;
    $lnk2->to->id = $wrd->id;
    $lnk2->load();
    $result = $lnk2->name;
    $target = '' . word::TN_RENAMED . ' (' . TEST_WORD . ')';
    $t->dsp('triple->load for user "' . $t->usr2->name . '"', $target, $result);

    // ... check if the value update has been triggered

    // if second user removes the new link
    $lnk = new word_link($t->usr2);
    $lnk->from->id = $wrd_added->id;
    $lnk->verb->id = $vrb->id;
    $lnk->to->id = $wrd->id;
    $lnk->load();
    $msg = $lnk->del();
    $result = $msg->get_last_message();
    $target = '';
    $t->dsp('triple->del "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '" by user "' . $t->usr2->name . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

    // ... check if the removal of the link for the second user has been logged
    $log = new user_log_link;
    $log->table = 'word_links';
    $log->old_from_id = $wrd_added->id;
    $log->old_link_id = $vrb->id;
    $log->old_to_id = $wrd->id;
    $log->usr = $t->usr2;
    $result = $log->dsp_last(true);
    $target = 'zukunft.com system test partner unlinked ' . word::TN_RENAMED . ' from ' . TEST_WORD . '';
    $t->dsp('triple->del logged for "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '" and user "' . $t->usr2->name . '"', $target, $result);


    // ... check if the link is really not used any more for the second user
    $lnk2 = new word_link($t->usr2);
    $lnk2->from->id = $wrd_added->id;
    $lnk2->verb->id = $vrb->id;
    $lnk2->to->id = $wrd->id;
    $lnk2->load();
    $result = $lnk2->name();
    $target = '';
    $t->dsp('triple->load "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '" for user "' . $t->usr2->name . '" not any more', $target, $result, TIMEOUT_LIMIT_PAGE_SEMI);

    // ... check if the value update for the second user has been triggered

    // ... check all places where the word maybe used ...

    // ... check if the link is still used for the first user
    $lnk = new word_link($t->usr1);
    $lnk->from->id = $wrd_added->id;
    $lnk->verb->id = $vrb->id;
    $lnk->to->id = $wrd->id;
    $lnk->load();
    $result = $lnk->name;
    $target = '' . word::TN_RENAMED . ' (' . TEST_WORD . ')';
    $t->dsp('triple->load of "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '" is still used for user "' . $t->usr1->name . '"', $target, $result, TIMEOUT_LIMIT_PAGE_SEMI);

    // ... check if the values for the first user are still the same

    // if the first user also removes the link, both records should be deleted
    $lnk = new word_link($t->usr1);
    $lnk->from->id = $wrd_added->id;
    $lnk->verb->id = $vrb->id;
    $lnk->to->id = $wrd->id;
    $lnk->load();
    $msg = $lnk->del();
    $result = $msg->get_last_message();
    $target = '';
    $t->dsp('triple->del "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);

    // check the correct logging
    $log = new user_log_link;
    $log->table = 'word_links';
    $log->old_from_id = $wrd_added->id;
    $log->old_link_id = $vrb->id;
    $log->old_to_id = $wrd->id;
    $log->usr = $t->usr1;
    $result = $log->dsp_last(true);
    $target = 'zukunft.com system test unlinked ' . word::TN_RENAMED . ' from ' . TEST_WORD . '';
    $t->dsp('triple->del logged for "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '" and user "' . $t->usr1->name . '"', $target, $result);

    // check if the formula is not used any more for both users
    $lnk = new word_link($t->usr1);
    $lnk->from->id = $wrd_added->id;
    $lnk->verb->id = $vrb->id;
    $lnk->to->id = $wrd->id;
    $lnk->load();
    $result = $lnk->name;
    $target = '';
    $t->dsp('triple->load of "' . $wrd_added->name . '" ' . $vrb->name . ' "' . $wrd->name . '" for user "' . $t->usr1->name . '" not used any more', $target, $result);


    // ... and the values have been updated
    /*
    // insert the link again for the first user
    $frm =$t->load_formula(TF_ADD_RENAMED);
    $phr = New phrase;
    $phr->name = word::TEST_NAME_CHANGED;
    $phr->usr = $t->usr2;
    $phr->load();
    $result = $frm->link_phr($phr);
    $target = '1';
    $t->dsp('triple->link_phr "'.$phr->name.'" to "'.$frm->name.'"', $target, $result, TIMEOUT_LIMIT_DB_MULTI);
    */
    // ... if the second user changes the link

    // ... and the first user removes the link

    // ... the link should still be active for the second user

    // ... but not for the first user

    // ... and the owner should now be the second user

    // the code changes and tests for formula link should be moved the view_component_link

}