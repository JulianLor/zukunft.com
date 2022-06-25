<?php

/*

    api\phrase.php - the minimal phrase object for the frontend API
    --------------

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

    Copyright (c) 1995-2022 zukunft.com AG, Zurich
    Heang Lor <heang@zukunft.com>

    http://zukunft.com

*/

namespace api;

class phrase_api extends user_sandbox_named_api
{

    // the mouse over tooltip for the word
    public ?string $description = null;

    function __construct(int $id = 0, string $name = '')
    {
        parent::__construct($id, $name);
    }

    /**
     * @returns true if th phrase is a triple (a combination of two words
     */
    public function is_triple(): bool
    {
        if ($this->id < 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @returns string the html code to display the phrase with reference links
     */
    function name_linked(): string
    {
        return $this->name;
    }

}