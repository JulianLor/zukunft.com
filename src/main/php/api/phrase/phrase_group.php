<?php

/*

    api\phrase_group.php - the minimal phrase group object used for the back- to frontend api
    --------------------


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

namespace api;

use html\phrase_group_dsp;
use html\phrase_list_dsp;

class phrase_group_api extends user_sandbox_named_api
{

    // list of word_min and triple_min objects
    private array $lst;

    // memory vs speed optimize vars
    private array $id_lst;
    private bool $lst_dirty;
    private string $name_linked;
    private bool $name_dirty;

    /*
     * construct and map
     */

    function __construct(int $id = 0, array $phr_lst = array(), string $name = '')
    {
        parent::__construct($id, $name);
        $this->lst = [];

        $this->id_lst = array();
        $this->lst_dirty = false;
        $this->name_linked = '';
        $this->name_dirty = true;


        // fill the phrase group based on the parameters included in new call
        $phr_id = 1; // if now id is given, create a dummy id for testing
        if (count($phr_lst) > 0) {
            foreach ($phr_lst as $phr_str) {
                $phr = new phrase_api($phr_id, $phr_str);
                $this->add($phr);
                $phr_id++;
            }
        }
    }

    /*
     * set and get
     */

    function set_lst($lst): void
    {
        $this->lst = $lst;
        $this->set_dirty();
    }

    function reset_lst(): void
    {
        $this->lst = array();
        $this->set_dirty();
    }

    function set_dirty(): void
    {
        $this->lst_dirty = true;
        $this->name_dirty = true;
    }

    function unset_name_dirty(): void
    {
        $this->name_dirty = false;
    }

    /**
     * @returns array the protected list of phrases
     */
    function lst(): array
    {
        return $this->lst;
    }

    function name_dirty(): bool
    {
        return $this->name_dirty;
    }

    /**
     * @returns array with all unique phrase ids og this list
     */
    private function id_lst(): array
    {
        $result = array();
        if ($this->lst_dirty) {
            foreach ($this->lst as $phr) {
                if (!in_array($phr->id, $result)) {
                    $result[] = $phr->id;
                }
            }
            $this->lst_dirty = false;
        } else {
            $result = $this->id_lst;
        }
        return $result;
    }

    /**
     * add a phrase to the list
     * @returns bool true if the phrase has been added
     */
    function add(phrase_api $phr): bool
    {
        $result = false;
        if (!in_array($phr->id, $this->id_lst())) {
            $this->lst[] = $phr;
            $this->set_dirty();
            $result = true;
        }
        return $result;
    }

    /**
     * @returns phrase_list_api the list of phrases as an object
     */
    function phr_lst(): phrase_list_api
    {
        $result = new phrase_list_api();
        $result->set_lst($this->lst());
        return $result;
    }

    /*
     * info
     */

    function has_percent(): bool
    {
        return $this->phr_lst()->has_percent();
    }

    /*
     * casting objects
     */

    /**
     * @returns phrase_group_dsp the cast object with the HTML code generating functions
     */
    function dsp_obj(): phrase_group_dsp
    {
        $result = new phrase_group_dsp($this->id);
        $result->set_lst_dsp($this->lst());
        return $result;
    }

    /**
     * @returns phrase_list_dsp the list of phrases as an object
     */
    function phr_lst_dsp(): phrase_list_dsp
    {
        $result = new phrase_list_dsp();
        $result->set_lst($this->lst());
        return $result;
    }

    function load_phrases(): bool
    {
        return $this->load_phrases();
    }

}
