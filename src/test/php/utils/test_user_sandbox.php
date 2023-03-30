<?php

/*

  test_word.php - TESTing of the word class
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

use cfg\phrase_type;
use model\source;
use model\word;
use test\testing;
use const test\TS_IPCC_AR6_SYNTHESIS;
use const test\TS_IPCC_AR6_SYNTHESIS_URL;
use const test\TW_MIO;

function run_sandbox_test(testing $t): void
{

    global $phrase_types;

    $t->header('Test the user sandbox class (classes/sandbox.php)');

    $t->subheader('Test the is_same and is_similar function');

    // a word is not the same as the same word that represents a formula
    $wrd1 = new word($t->usr1);
    $wrd1->type_id = $phrase_types->id(phrase_type::FORMULA_LINK);
    $wrd1->set_name(TW_MIO);
    $wrd2 = new word($t->usr1);
    $wrd2->type_id = $phrase_types->default_id();
    $wrd2->set_name(TW_MIO);
    $target = false;
    $result = $wrd1->is_same($wrd2);
    $t->dsp("a word is not the same as the same word that represents a formula", $target, $result);

    // ... but it is similar
    $target = true;
    $result = $wrd1->is_similar_named($wrd2);
    $t->dsp("... but it is similar", $target, $result);

    $t->subheader('Test the saving function');

    // create a new source (_sandbox->save case 1)
    $src = new source($t->usr1);
    $src->set_name(TS_IPCC_AR6_SYNTHESIS);
    $result = $src->save();
    $target = '';
    $t->dsp('_sandbox->save create a new source', $target, $result);

    // remember the id
    $src_id = 0;
    if ($result == '') {
        $src_id = $src->id();
    }

    // check if the source has been saved (check _sandbox->save case 1)
    $src = new source($t->usr1);
    if ($src->load_by_id($src_id)) {
        $result = $src->name();
    }
    $target = TS_IPCC_AR6_SYNTHESIS;
    $t->dsp('_sandbox->save check created source', $target, $result);

    // update the source url by name (_sandbox->save case 2)
    $src = new source($t->usr1);
    $src->set_name(TS_IPCC_AR6_SYNTHESIS);
    $src->url = TS_IPCC_AR6_SYNTHESIS_URL;
    $result = $src->save();
    $target = '';
    $t->dsp('_sandbox->save update the source url by name', $target, $result);

    // remember the id
    $src_id = 0;
    if ($result == '') {
        $src_id = $src->id();
    }

    // check if the source url has been updates (check _sandbox->save case 2)
    $src = new source($t->usr1);
    if ($src->load_by_id($src_id)) {
        $result = $src->url;
    }
    $target = TS_IPCC_AR6_SYNTHESIS_URL;
    $t->dsp('_sandbox->save check if the source url has been updates', $target, $result);

}

