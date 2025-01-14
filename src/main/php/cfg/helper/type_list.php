<?php

/*

    model/helper/type_list.php - the superclass for word, formula and view type lists
    --------------------------


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
include_once DB_PATH . 'sql_par.php';
include_once MODEL_HELPER_PATH . 'library.php';
include_once MODEL_VERB_PATH . 'verb.php';
include_once API_SYSTEM_PATH . 'type_list.php';
include_once WEB_USER_PATH . 'user_type_list.php';

use api\system\type_list as type_list_api;
use cfg\db\sql;
use cfg\db\sql_db;
use cfg\db\sql_par;
use html\user\user_type_list as type_list_dsp;
use model\db_cl;

class type_list
{

    /*
     * database link
     */

    // database and export JSON object field names
    const FLD_NAME = 'sys_log_function_name';

    // error return codes
    const CODE_ID_NOT_FOUND = -1;

    // persevered type name and code id for unit and integration tests
    const TEST_NAME = 'System Test Type Name';
    const TEST_TYPE = 'System Test Type Code ID';


    /*
     * object vars
     */

    private array $lst = [];  // a list of type objects
    private array $hash = []; // hash list with the code id for fast selection


    /*
     * construct and map
     */

    function reset(): void
    {
        $this->set_lst(array());
    }


    /*
     * cast
     */

    /**
     * @return type_list_api the object type list frontend api object
     */
    function api_obj(): object
    {
        return new type_list_api($this->lst);
    }

    /**
     * @returns string the api json message for the object as a string
     */
    function api_json(): string
    {
        return $this->api_obj()->get_json();
    }

    /**
     * @return type_list_dsp the word frontend api object
     */
    function dsp_obj(): object
    {
        return new type_list_dsp($this->lst);
    }


    /*
     * set and get
     */

    /**
     * @returns true if the list has been replaced
     */
    function set_lst(array $lst): bool
    {
        $this->lst = $lst;
        $this->get_hash($lst);
        return true;
    }

    /**
     * @returns array the protected list of preloaded types
     */
    function lst(): array
    {
        return $this->lst;
    }

    /**
     * @returns array the hash list of preloaded types
     */
    function hash(): array
    {
        return $this->hash;
    }


    /*
     * interface set and get
     */

    function add(type_object|ref|view $item): void
    {
        $this->lst[$item->id()] = $item;
        $this->hash[$item->code_id] = $item->id();
    }

    /*
     * database (dao) functions
     */

    /**
     * set the common part of the sql parameters to load all rows of one 'type of database type'
     *
     * a type is the link between one object and some predefined behavior
     * a.g. a word like 'meter' has the type 'measure' which implies that
     * the result of meter divided by meter is a relative value which is e.g. in percent
     *
     * a 'database type' is a group of type used for the same objects
     * e.g. a db_type is phrase_type or view type
     *
     * @param sql $sc with the target db_type set
     * @param string $class the class of the related object e.g. phrase_type or formula_type
     * @param string $query_name the name extension to make the query name unique
     * @param string $order_field set if the type list should e.g. be sorted by the name instead of the id
     * @return sql_par the sql statement with the parameters and the name
     */
    function load_sql(
        sql    $sc,
        string $class,
        string $query_name = 'all',
        string $order_field = ''): sql_par
    {
        $lib = new library();
        $db_type = $lib->class_to_name($class);
        $sc->set_class($db_type);
        $qp = new sql_par($db_type);
        $qp->name = $db_type . '_' . $query_name;
        $sc->set_name($qp->name);
        //TODO check if $db_con->set_usr($this->user()->id()); is needed
        $sc->set_fields(array(sandbox_named::FLD_DESCRIPTION, sql_db::FLD_CODE_ID));
        if ($order_field == '') {
            $order_field = $sc->get_id_field_name($db_type);
        }
        $sc->set_order($order_field);

        return $qp;
    }

    /**
     * the sql parameters to load all rows of one 'type of database type'
     *
     * a type is the link between one object and some predefined behavior
     * a.g. a word like 'meter' has the type 'measure' which implies that
     * the result of meter divided by meter is a relative value which is e.g. in percent
     *
     * a 'database type' is a group of type used for the same objects
     * e.g. a db_type is phrase_type or view type
     *
     * @param sql $sc with the target db_type set
     * @param string $db_type the class of the related object e.g. phrase_type or formula_type
     * @return sql_par the sql statement with the parameters and the name
     */
    function load_sql_all(sql $sc, string $db_type): sql_par
    {
        $qp = $this->load_sql($sc, $db_type);
        $sc->set_page(sql_db::ROW_MAX, 0);
        $qp->sql = $sc->sql();
        $qp->par = $sc->get_par();

        return $qp;
    }

    /**
     * force to reload the type names and translations from the database
     * @param sql_db $db_con the database connection that can be either the real database connection or a simulation used for testing
     * @param string $db_type the database name e.g. the table name without s
     * @return array the list of types
     */
    private function load_list(sql_db $db_con, string $db_type): array
    {
        $this->lst = [];
        $qp = $this->load_sql_all($db_con->sql_creator(), $db_type);
        $db_lst = $db_con->get($qp);
        if ($db_lst != null) {
            foreach ($db_lst as $db_row) {
                $type_id = $db_row[$db_con->get_id_field_name($db_type)];
                $type_code_id = strval($db_row[sql_db::FLD_CODE_ID]);
                // database field name exceptions
                $type_name = '';
                if ($db_type == db_cl::LOG_ACTION) {
                    $type_name = strval($db_row[type_object::FLD_ACTION]);
                } elseif ($db_type == db_cl::LOG_TABLE) {
                    $type_name = strval($db_row[type_object::FLD_TABLE]);
                } elseif ($db_type == sql_db::VT_TABLE_FIELD) {
                    $type_name = strval($db_row[type_object::FLD_FIELD]);
                } elseif ($db_type == sql_db::TBL_LANGUAGE) {
                    $type_name = strval($db_row[language::FLD_NAME]);
                } elseif ($db_type == sql_db::TBL_LANGUAGE_FORM) {
                    $type_name = strval($db_row[language_form::FLD_NAME]);
                } else {
                    $type_name = strval($db_row[sql_db::FLD_TYPE_NAME]);
                }
                $type_comment = strval($db_row[sandbox_named::FLD_DESCRIPTION]);
                $type_obj = new type_object($type_code_id, $type_name, $type_comment, $type_id);
                $this->add($type_obj);
            }
        }
        return $this->lst;
    }

    /**
     * recreate the hash table to get the database id for a code_id
     * @param array $type_list the list of the code_id indexed by the database id
     * @return array with the database ids indexed by the code_id
     */
    function get_hash(array $type_list): array
    {
        $this->hash = [];
        if ($type_list != null) {
            foreach ($type_list as $key => $type) {
                $this->hash[$type->code_id] = $key;
            }
        }
        return $this->hash;
    }

    /**
     * reload a type list from the database e.g. because a translation has changed and fill the hash table
     * @param string $db_type the database table type name to select either word, formula, view, ...
     * @param sql_db $db_con the database connection that can be either the real database connection or a simulation used for testing
     * @return bool true if load was successful
     */
    function load(sql_db $db_con, string $db_type): bool
    {
        $result = false;
        $this->lst = $this->load_list($db_con, $db_type);
        $this->hash = $this->get_hash($this->lst);
        if (count($this->hash) > 0) {
            $result = true;
        }
        return $result;
    }

    /**
     * return the database row id based on the code_id
     *
     * @param string $code_id
     * @return int the database id for the given code_id
     */
    function id(string $code_id): int
    {
        $lib = new library();
        $result = 0;
        if ($code_id != '' and $code_id != null) {
            if (array_key_exists($code_id, $this->hash)) {
                $result = $this->hash[$code_id];
            } else {
                $result = self::CODE_ID_NOT_FOUND;
                log_debug('Type id not found for "' . $code_id . '" in ' . $lib->dsp_array_keys($this->hash));
            }
        } else {
            log_debug('Type code id not not set');
        }
        return $result;
    }

    /**
     * return user specific type name based on the database row id
     *
     * @param int|null $id
     * @return string
     */
    function name(?int $id): string
    {
        $result = '';
        if ($id != null) {
            $type = $this->get($id);
            if ($type != null) {
                $result = $type->name;
            } else {
                log_debug('Type id ' . $id . ' not found');
            }
        }
        return $result;
    }

    /**
     * pick a type from the preloaded object list
     * @param int $id the database id of the expected type
     * @return type_object|null the type object
     */
    function get(int $id): ?type_object
    {
        $result = null;
        if ($id > 0) {
            if (array_key_exists($id, $this->lst)) {
                $result = $this->lst[$id];
            } else {
                log_err('Type with is ' . $id . ' not found in ' . $this->dsp_id());
            }
        } else {
            log_debug('Type id not set');
        }
        return $result;
    }

    /**
     * TODO to rename to get and rename get to get_by_id
     */
    function get_by_code_id(string $code_id): type_object
    {
        return $this->get($this->id($code_id));
    }

    function code_id(int $id): string
    {
        $result = '';
        $type = $this->get($id);
        if ($type != null) {
            $result = $type->code_id;
        } else {
            log_err('Type code id not found for ' . $id . ' in ' . $this->dsp_id());
        }
        return $result;
    }

    function count(): int
    {
        return count($this->lst());
    }

    /**
     * @return bool true if the list is empty (and a foreach loop will fail)
     */
    function is_empty(): bool
    {
        $result = false;
        if (empty($this->lst)) {
            $result = true;
        }
        return $result;
    }


    /*
     * unit test support functions
     */

    /**
     * create dummy type list for the unit tests without database connection
     */
    function load_dummy(): void
    {
        $this->lst = array();
        $this->hash = array();
        $type = new type_object(type_list::TEST_TYPE, type_list::TEST_NAME, '', 1);
        $this->add($type);
    }

    /**
     * @param array $code_id_list with the code ids that should be converted
     * @return array with the component ids
     */
    function view_id_list(array $code_id_list): array
    {
        global $view_types;

        $result = [];
        foreach ($code_id_list as $code_id) {
            $result[] = $view_types->id($code_id);
        }
        return $result;
    }

    /**
     * @param array $code_id_list with the code ids that should be converted
     * @return array with the component ids
     */
    function component_id_list(array $code_id_list): array
    {
        global $component_types;

        $result = [];
        foreach ($code_id_list as $code_id) {
            $result[] = $component_types->id($code_id);
        }
        return $result;
    }


    /*
     * debug
     */

    /**
     * @return string the verb list with the internal database ids for debugging
     */
    function dsp_id(): string
    {
        $names = '';
        $ids = '';
        if (!$this->is_empty()) {
            foreach ($this->lst as $key => $type) {
                if ($names != '') {
                    $names .= ', ';
                }
                $names .= '"' . $type->name() . '"';

                if ($ids != '') {
                    $ids .= ', ';
                }
                $ids .= $key;
            }
        }
        return $names . ' (' . $ids . ')';
    }

}