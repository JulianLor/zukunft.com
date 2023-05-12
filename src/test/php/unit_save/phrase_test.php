<?php

/*

  phrase_test.php - PHRASE class unit TESTs
  ---------------
  

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

namespace test;

use api\phrase_api;
use api\word_api;
use model\library;
use model\phrase;
use model\triple;
use model\verb;
use model\word;
use test\test_cleanup;
use const test\TEST_WORD;
use const test\TIMEOUT_LIMIT_PAGE;
use const test\TIMEOUT_LIMIT_PAGE_SEMI;
use const test\TP_ABB;
use const test\TP_FOLLOW;
use const test\TP_TAXES;
use const test\TW_2013;
use const test\TW_2014;
use const test\TW_ABB;
use const test\TW_CF;
use const test\TW_TAX;
use const test\TW_VESTAS;

function create_test_phrases(test_cleanup $t): void
{
    $t->header('Check if all base phrases are correct');

    $t->test_triple(word_api::TN_ZH, verb::IS_A, word_api::TN_CANTON, phrase_api::TN_ZH_CANTON);
    $t->test_triple(word_api::TN_ZH, verb::IS_A, word_api::TN_CITY, phrase_api::TN_ZH_CITY, phrase_api::TN_ZH_CITY);
    $t->test_triple(word_api::TN_ZH, verb::IS_A, word_api::TN_COMPANY, phrase_api::TN_ZH_COMPANY, phrase_api::TN_ZH_COMPANY);

    $t->test_triple(TW_ABB, verb::IS_A, TEST_WORD, TP_ABB);
    $t->test_triple(TW_VESTAS, verb::IS_A, TEST_WORD, TW_VESTAS, TW_VESTAS);
    $t->test_triple(TW_2014, verb::FOLLOW, TW_2013, TP_FOLLOW);
    // TODO check direction
    $t->test_triple(TW_TAX, verb::IS_PART_OF, TW_CF, TP_TAXES);

    $t->header('Check if all base phrases are correct');
    $t->test_phrase(phrase_api::TN_ZH_COMPANY);
}

function create_base_times(test_cleanup $t): void
{
    $t->header('Check if base time words are correct');

    zu_test_time_setup($t);
}

function run_phrase_test(test_cleanup $t): void
{

    global $usr;
    global $verbs;
    $lib = new library();

    $t->header('Test the phrase class (src/main/php/model/phrase/phrase.php)');

    // load the main test word and verb
    $wrd_company = $t->test_word(word_api::TN_COMPANY);
    $is_id = $verbs->id(verb::IS_A);

    // prepare the Insurance Zurich
    $wrd_zh = $t->load_word(word_api::TN_ZH);
    $lnk_company = new triple($usr);
    $lnk_company->load_by_link($wrd_zh->id(), $is_id, $wrd_company->id());

    // remember the id for later use
    $zh_company_id = $lnk_company->id();


    // test the phrase display functions (word side)
    $phr = new phrase($usr);
    $phr->set_id($wrd_company->id());
    $phr->set_user($usr);
    $phr->load_by_obj_par();
    $result = $phr->name();
    $target = word_api::TN_COMPANY;
    $t->display('phrase->load word by id ' . $wrd_company->id(), $target, $result);

    $result = str_replace("  ", " ", str_replace("\n", "", $phr->dsp_tbl()));
    $target = ' <td><a href="/http/view.php?words=' . $wrd_company->id() . '" title="System Test Word Group e.g. Company">' . word_api::TN_COMPANY . '</a></td> ';
    $result = str_replace("<", "&lt;", str_replace(">", "&gt;", $result));
    $target = str_replace("<", "&lt;", str_replace(">", "&gt;", $target));
    $result = $lib->trim_all_spaces($result);
    $target = $lib->trim_all_spaces($target);
    // to overwrite any special char
    $diff = $lib->str_diff($result, $target);
    if ($diff != '') {
        log_err('Unexpected diff ' . $diff);
        $target = $result;
    }
    $t->display('phrase->dsp_tbl word for ' . TEST_WORD, $target, $result);

    // test the phrase display functions (triple side)
    $phr = new phrase($usr);
    $phr->set_id_from_obj($zh_company_id, triple::class);
    $phr->load_by_obj_par();
    $result = $phr->name();
    $target = phrase_api::TN_ZH_COMPANY;
    $t->display('phrase->load triple by id ' . $zh_company_id, $target, $result);

    $result = str_replace("  ", " ", str_replace("\n", "", $phr->dsp_tbl()));
    $target = ' <td> <a href="/http/view.php?link=' . $lnk_company->id() . '" title="' . phrase_api::TN_ZH_COMPANY . '">' . phrase_api::TN_ZH_COMPANY . '</a></td> ';
    $result = str_replace("<", "&lt;", str_replace(">", "&gt;", $result));
    $target = str_replace("<", "&lt;", str_replace(">", "&gt;", $target));
    $result = $lib->trim_all_spaces($result);
    $target = $lib->trim_all_spaces($target);
    // to overwrite any special char
    $diff = $lib->str_diff($result, $target);
    if ($diff != '') {
        log_err('Unexpected diff ' . $diff);
        $target = $result;
    }
    $t->display('phrase->dsp_tbl triple for ' . $zh_company_id, $target, $result);

    // test the phrase selector
    $form_name = 'test_phrase_selector';
    $pos = 1;
    $back = $wrd_company->id();
    $phr = new phrase($usr);
    $phr->set_id($zh_company_id * -1);
    $phr->load_by_obj_par();
    $result = $phr->dsp_selector(Null, $form_name, $pos, '', $back);
    $target = phrase_api::TN_ZH_COMPANY;
    $t->dsp_contains(', phrase->dsp_selector ' . $result . ' with ' . phrase_api::TN_ZH_COMPANY . ' selected contains ' . phrase_api::TN_ZH_COMPANY . '', $target, $result, TIMEOUT_LIMIT_PAGE);

    // test the phrase selector of type company
    $wrd_ABB = new word($usr);
    $wrd_ABB->load_by_name(TW_ABB, word::class);
    $phr = $wrd_ABB->phrase();
    $wrd_company = new word($usr);
    $wrd_company->load_by_name(TEST_WORD, word::class);
    $result = $phr->dsp_selector($wrd_company, $form_name, $pos, '', $back);
    $target = TW_ABB;
    $t->dsp_contains(', phrase->dsp_selector of type ' . TEST_WORD . ': ' . $result . ' with ABB selected contains ' . phrase_api::TN_ZH_COMPANY . '', $target, $result, TIMEOUT_LIMIT_PAGE_SEMI);

    // test getting the parent for phrase Vestas
    $phr = $t->load_phrase(TW_VESTAS);
    $is_phr = $phr->is_mainly();
    if ($is_phr != null) {
        $result = $is_phr->name();
    }
    $target = TEST_WORD;
    $t->display('phrase->is_mainly for ' . $phr->name(), $target, $result);

}