<?php

/*

  protection_type_list.php - a database based enum list for the data protection types
  ------------------------


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

global $protection_types;

class protection_type_list extends user_type_list
{
    // list of the ref types that have a coded functionality
    const DBL_NO = "no_protection";
    const DBL_USER = "user_protection";
    const DBL_ADMIN = "admin_protection";
    const DBL_NO_CHANGE = "no_change";

    /**
     * overwrite the general user type list load function to keep the link to the table type capsuled
     * @param sql_db $db_con the database connection that can be either the real database connection or a simulation used for testing
     * @return bool true if load was successful
     */
    function load(sql_db $db_con): bool
    {
        return parent::load_by_db(DB_TYPE_PROTECTION, $db_con);
    }

    /**
     * create dummy type list for the unit tests without database connection
     */
    function load_dummy()
    {
        $this->type_list = array();
        $this->type_hash = array();
        $type = new user_type();
        $type->name = protection_type_list::DBL_NO;
        $type->code_id = protection_type_list::DBL_NO;
        $this->type_list[2] = $type;
        $this->type_hash[protection_type_list::DBL_NO] = 2;
        $type = new user_type();
        $type->name = protection_type_list::DBL_ADMIN;
        $type->code_id = protection_type_list::DBL_ADMIN;
        $this->type_list[3] = $type;
        $this->type_hash[protection_type_list::DBL_ADMIN] = 3;

    }

    /**
     * return the database id of the default word type
     */
    function default_id(): int {
        return parent::id(protection_type_list::DBL_NO);
    }

}