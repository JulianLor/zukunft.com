<?php

/*

    api/ref/ref.php - the reference object for the frontend API
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

namespace api;

class ref_api extends sandbox_api
{

    /*
     * const for the api
     */

    const API_NAME = 'reference';


    /*
     * const for system testing
     */

    // persevered reference names for unit and integration tests
    const TN_READ = 'wikidata';
    const TN_ADD = 'System Test Reference Name';
    const TK_READ = 'Mathematical_constant';

    // must be the same as in /resource/api/source/source_put.json
    const TK_ADD_API = 'System Test Reference API added';
    const TD_ADD_API = 'System Test Reference Description API';
    const TU_ADD_API = 'https://api.zukunft.com/';

    // reference group for testing
    // TODO activate
    const RESERVED_REFERENCES = array(
        self::TN_READ
    );


    /*
     * object vars
     */

    public ?phrase_api $phr;
    public ?string $external_key;
    public ?int $type_id;
    public ?int $source_id;
    public ?string $url;
    public ?string $description;


    /*
     * set and get
     */

    function set_type_id(?int $type_id): void
    {
        $this->type_id = $type_id;
    }
}
