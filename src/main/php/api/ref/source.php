<?php

/*

    api/ref/source.php - the source object for the frontend API
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
    along with zukunft.com. If not, see <http://www.gnu.org/licenses/agpl.html>.

    To contact the authors write to:
    Timon Zielonka <timon@zukunft.com>

    Copyright (c) 1995-2022 zukunft.com AG, Zurich
    Heang Lor <heang@zukunft.com>

    http://zukunft.com

*/

namespace api;

class source_api extends user_sandbox_named_with_type_api
{

    /*
     * const for system testing
     */

    // persevered source names for unit and integration tests (TN means TEST NAME)
    const TN_READ = 'wikidata';
    const TN_ADD = 'System Test Source';
    const TN_RENAMED = 'System Test Source Renamed';

    // parameters used for unit and integration tests
    const TEST_URL = 'https://www.zukunft.com/';
    const TEST_URL_CHANGED = 'https://api.zukunft.com/';
    const TEST_DESCRIPTION = 'System Test Source Description';
    const TEST_DESCRIPTION_CHANGED = 'System Test Source Description Changed';

    // source group for creating the test sources and remove them after the test
    const RESERVED_SOURCES = array(
        self::TN_READ, // the source for all data imported from wikidata that does not yet have a source defined in wikidata
        self::TN_ADD,
        self::TN_RENAMED
    );

}