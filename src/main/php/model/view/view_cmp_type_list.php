<?php

/*

    model/view/view_component_type_list.php - to link coded functionality to a view component
    ---------------------------------------

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

    Copyright (c) 1995-2023 zukunft.com AG, Zurich
    Heang Lor <heang@zukunft.com>

    http://zukunft.com
  
*/

namespace cfg;

include_once DB_PATH . 'sql_db.php';
include_once MODEL_VIEW_PATH . 'view_cmp_type.php';
include_once MODEL_HELPER_PATH . 'type_list.php';
include_once MODEL_HELPER_PATH . 'type_object.php';

use model\sql_db;
use model\view_cmp_type;

global $view_component_types;

class view_cmp_type_list extends type_list
{

    /**
     * overwrite the general user type list load function to keep the link to the table type capsuled
     * @param sql_db $db_con the database connection that can be either the real database connection or a simulation used for testing
     * @return bool true if load was successful
     */
    function load(sql_db $db_con, string $db_type = sql_db::TBL_VIEW_COMPONENT_TYPE): bool
    {
        return parent::load($db_con, $db_type);
    }

    /**
     * adding the view component types used for unit tests to the dummy list
     */
    function load_dummy(): void {
        parent::load_dummy();
        $type = new type_object(view_cmp_type::TEXT, view_cmp_type::TEXT, '', 2);
        $this->add($type);
        $type = new type_object(view_cmp_type::PHRASE_NAME, view_cmp_type::PHRASE_NAME, '', 8);
        $this->add($type);
        $type = new type_object(view_cmp_type::FORM_TITLE, view_cmp_type::FORM_TITLE, '', 17);
        $this->add($type);
        $type = new type_object(view_cmp_type::FORM_BACK, view_cmp_type::FORM_BACK, '', 18);
        $this->add($type);
        $type = new type_object(view_cmp_type::FORM_CONFIRM, view_cmp_type::FORM_CONFIRM, '', 19);
        $this->add($type);
        $type = new type_object(view_cmp_type::FORM_NAME, view_cmp_type::FORM_NAME, '', 20);
        $this->add($type);
        $type = new type_object(view_cmp_type::FORM_DESCRIPTION, view_cmp_type::FORM_DESCRIPTION, '', 21);
        $this->add($type);
        $type = new type_object(view_cmp_type::FORM_CANCEL, view_cmp_type::FORM_CANCEL, '', 22);
        $this->add($type);
        $type = new type_object(view_cmp_type::FORM_SAVE, view_cmp_type::FORM_SAVE, '', 23);
        $this->add($type);
    }

    /**
     * return the database id of the default view component type
     */
    function default_id(): int
    {
        return parent::id(view_cmp_type::TEXT);
    }

}
