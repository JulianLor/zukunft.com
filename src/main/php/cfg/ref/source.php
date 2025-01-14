<?php

/*

    model/ref/source.php - the source object to define the source for the values
    --------------------

    a source is always unidirectional
    in many cases a source is just a user base data source without any import
    the automatic import can be based on standard data format e.g. json, XML or HTML

    reference types are preloaded in the frontend whereas source are loaded on demand

    if the import gets more complex or the interface is bidirectional use a reference type
    references are concrete links between a phrase and an external object


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

include_once DB_PATH . 'sql_par_type.php';
include_once MODEL_SANDBOX_PATH . 'sandbox_typed.php';
include_once API_REF_PATH . 'source.php';
include_once SERVICE_EXPORT_PATH . 'sandbox_exp.php';
include_once SERVICE_EXPORT_PATH . 'source_exp.php';
include_once WEB_REF_PATH . 'source.php';

use api\ref\source as source_api;
use cfg\db\sql;
use cfg\db\sql_db;
use cfg\db\sql_field_default;
use cfg\db\sql_field_type;
use cfg\db\sql_par;
use cfg\export\sandbox_exp;
use cfg\export\source_exp;

class source extends sandbox_typed
{

    /*
     * database link
     */

    // object specific database and JSON object field names
    const FLD_ID = 'source_id';
    const FLD_NAME = 'source_name';
    const FLD_TYPE = 'source_type_id';
    const FLD_URL = 'url';

    // comments used for the database creation
    const TBL_COMMENT = 'for the original sources for the numeric, time and geo values';

    // list of fields that can be changed by the user
    const FLD_LST_CREATE_CHANGEABLE = array(
        [self::FLD_NAME, sql_field_type::NAME, sql_field_default::NOT_NULL, sql::INDEX, '', 'the unique name of the source used e.g. as the primary search key'],
        [self::FLD_DESCRIPTION, sql_field_type::TEXT, sql_field_default::NULL, '', '', 'the user specific description of the source for mouse over helps'],
        [self::FLD_TYPE, sql_field_type::INT, sql_field_default::NULL, sql::INDEX, '', 'link to the source type'],
        [self::FLD_URL, sql_field_type::TEXT, sql_field_default::NULL, '', '', 'the url of the source'],
        [sql_db::FLD_CODE_ID, sql_field_type::CODE_ID, sql_field_default::NULL, '', '', 'to select sources used by this program'],
    );

    // all database field names excluding the id used to identify if there are some user specific changes
    const FLD_NAMES = array(
        self::FLD_NAME,
        sql_db::FLD_CODE_ID
    );
    // list of the user specific database field names
    const FLD_NAMES_USR = array(
        self::FLD_URL,
        sandbox_named::FLD_DESCRIPTION
    );
    // list of the user specific numeric database field names
    const FLD_NAMES_NUM_USR = array(
        self::FLD_TYPE,
        sandbox::FLD_EXCLUDED
    );
    // all database field names excluding the id used to identify if there are some user specific changes
    const ALL_SANDBOX_FLD_NAMES = array(
        self::FLD_NAME,
        sandbox_named::FLD_DESCRIPTION,
        self::FLD_TYPE,
        sandbox::FLD_EXCLUDED,
        self::FLD_URL
    );


    /*
     * object vars
     */

    // database fields additional to the user sandbox fields
    public ?string $url = null;          // the internet link to the source
    public ?string $code_id = null;      // to select internal predefined sources


    /*
     * construct and map
     */

    // define the settings for this source object
    function __construct(user $usr)
    {
        parent::__construct($usr);
        $this->obj_name = sql_db::TBL_SOURCE;

        $this->rename_can_switch = UI_CAN_CHANGE_SOURCE_NAME;
    }

    function reset(): void
    {
        parent::reset();

        $this->url = '';
        $this->code_id = '';
    }

    /**
     * map the database object to this source class fields
     *
     * @param array|null $db_row with the data directly from the database
     * @param bool $load_std true if only the standard user sandbox object ist loaded
     * @param bool $allow_usr_protect false for using the standard protection settings for the default object used for all users
     * @param string $id_fld the name of the id field as defined in this child and given to the parent
     * @return bool true if the source is loaded and valid
     */
    function row_mapper_sandbox(
        ?array $db_row,
        bool   $load_std = false,
        bool   $allow_usr_protect = false,
        string $id_fld = self::FLD_ID,
        string $name_fld = self::FLD_NAME
    ): bool
    {
        $result = parent::row_mapper_sandbox($db_row, $load_std, $allow_usr_protect, $id_fld, $name_fld);
        if ($result) {
            $this->url = $db_row[self::FLD_URL];
            $this->type_id = $db_row[self::FLD_TYPE];
            $this->code_id = $db_row[sql_db::FLD_CODE_ID];
        }
        return $result;
    }


    /*
     * set and get
     */

    /**
     * set the most used object vars with one set statement
     * @param int $id mainly for test creation the database id of the source
     * @param string $name mainly for test creation the name of the source
     * @param string $type_code_id the code id of the predefined source type
     */
    function set(int $id = 0, string $name = '', string $type_code_id = ''): void
    {
        parent::set($id, $name);

        if ($type_code_id != '') {
            $this->set_type($type_code_id);
        }
    }

    /**
     * set the predefined type of this source
     *
     * @param string $type_code_id the code id that should be added to this source
     * @return void
     */
    function set_type(string $type_code_id): void
    {
        global $source_types;
        $this->type_id = $source_types->id($type_code_id);
    }


    /*
     * cast
     */

    /**
     * @return source_api the source frontend api object
     */
    function api_obj(): source_api
    {
        $api_obj = new source_api();
        if (!$this->is_excluded()) {
            parent::fill_api_obj($api_obj);
            $api_obj->url = $this->url;
        }
        return $api_obj;
    }

    /**
     * @returns string the api json message for the object as a string
     */
    function api_json(): string
    {
        return $this->api_obj()->get_json();
    }


    /*
     * load
     */

    /**
     * create the SQL to load the default source always by the id
     *
     * @param sql $sc with the target db_type set
     * @param string $class the name of the child class from where the call has been triggered
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_standard_sql(sql $sc, string $class = self::class): sql_par
    {
        $sc->set_class(source::class);
        $sc->set_fields(array_merge(
            self::FLD_NAMES,
            self::FLD_NAMES_USR,
            self::FLD_NAMES_NUM_USR,
            array(user::FLD_ID)
        ));

        return parent::load_standard_sql($sc, $class);
    }

    /**
     * load the source parameters for all users
     * @param sql_par|null $qp placeholder to align the function parameters with the parent
     * @param string $class the name of this class to be delivered to the parent function
     * @return bool true if the standard source has been loaded
     */
    function load_standard(?sql_par $qp = null, string $class = self::class): bool
    {
        global $db_con;
        $qp = $this->load_standard_sql($db_con->sql_creator());
        $result = parent::load_standard($qp, $class);

        if ($result) {
            $result = $this->load_owner();
        }
        return $result;
    }

    /**
     * create the common part of an SQL statement to retrieve the parameters of a source from the database
     *
     * @param sql $sc with the target db_type set
     * @param string $query_name the name extension to make the query name unique
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_sql(sql $sc, string $query_name): sql_par
    {
        $sc->set_class($this::class);
        return parent::load_sql_fields(
            $sc, $query_name,
            self::FLD_NAMES,
            self::FLD_NAMES_USR,
            self::FLD_NAMES_NUM_USR
        );
    }

    /**
     * create an SQL statement to retrieve a source by code id from the database
     *
     * @param sql $sc with the target db_type set
     * @param string $code_id the code id of the source
     * @param string $class the name of the child class from where the call has been triggered
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_sql_by_code_id(sql $sc, string $code_id, string $class): sql_par
    {
        $qp = $this->load_sql($sc, 'code_id', $class);
        $sc->add_where(sql_db::FLD_CODE_ID, $code_id);
        $qp->sql = $sc->sql();
        $qp->par = $sc->get_par();

        return $qp;
    }

    /**
     * load a source by code id
     * @param string $code_id the code id of the source
     * @param string $class the name of the child class from where the call has been triggered
     * @return int the id of the object found and zero if nothing is found
     */
    function load_by_code_id(string $code_id, string $class = self::class): int
    {
        global $db_con;

        log_debug($code_id);
        $qp = $this->load_sql_by_code_id($db_con->sql_creator(), $code_id, $class);
        return parent::load($qp);
    }

    /**
     * just set the class name for the user sandbox function
     * load a source object by database id
     * @param int $id the id of the source
     * @param string $class the source class name
     * @return int the id of the object found and zero if nothing is found
     */
    function load_by_id(int $id, string $class = self::class): int
    {
        return parent::load_by_id($id, $class);
    }

    /**
     * just set the class name for the user sandbox function
     * load a source object by name
     * @param string $name the name source
     * @param string $class the source class name
     * @return int the id of the object found and zero if nothing is found
     */
    function load_by_name(string $name, string $class = self::class): int
    {
        return parent::load_by_name($name, $class);
    }

    function name_field(): string
    {
        return self::FLD_NAME;
    }

    function all_sandbox_fields(): array
    {
        return self::ALL_SANDBOX_FLD_NAMES;
    }


    /**
     * @return string the source type name from the array preloaded from the database
     */
    function type_name(): string
    {
        global $source_types;

        $type_name = '';
        if ($this->type_id > 0) {
            $type_name = $source_types->name($this->type_id);
        }
        return $type_name;
    }


    /*
     * im- and export
     */

    /**
     * import a source from an object
     *
     * @param array $in_ex_json an array with the data of the json object
     * @param object|null $test_obj if not null the unit test object to get a dummy seq id
     * @return user_message the status of the import and if needed the error messages that should be shown to the user
     */
    function import_obj(array $in_ex_json, object $test_obj = null): user_message
    {
        global $source_types;

        log_debug();
        $result = parent::import_obj($in_ex_json, $test_obj);

        foreach ($in_ex_json as $key => $value) {
            if ($key == self::FLD_URL) {
                $this->url = $value;
            }
            if ($key == sandbox_exp::FLD_TYPE) {
                $this->type_id = $source_types->id($value);
            }
        }

        // save the source in the database
        if (!$test_obj) {
            if ($result->is_ok()) {
                $result->add_message($this->save());
            }
        }

        return $result;
    }

    /**
     * create an object for the export
     * @param bool $do_load to switch off the database load for unit tests
     * @return sandbox_exp the filled object used to create the json
     */
    function export_obj(bool $do_load = true): sandbox_exp
    {
        log_debug();
        $result = new source_exp();

        // add the source parameters
        $result->name = $this->name();
        if ($this->url <> '') {
            $result->url = $this->url;
        }
        if ($this->description <> '') {
            $result->description = $this->description;
        }
        if ($this->type_name() <> '') {
            $result->type = $this->type_name();
        }
        if ($this->code_id <> '') {
            $result->code_id = $this->code_id;
        }

        log_debug(json_encode($result));
        return $result;
    }

    /**
     * set the source object vars based on an api json array
     * similar to import_obj but using the database id instead of the names and code id
     * @param array $api_json the api array
     * @return user_message false if a value could not be set
     */
    function save_from_api_msg(array $api_json, bool $do_save = true): user_message
    {
        log_debug();
        $result = new user_message();

        foreach ($api_json as $key => $value) {

            if ($key == sandbox_exp::FLD_NAME) {
                $this->name = $value;
            }
            if ($key == self::FLD_URL) {
                $this->url = $value;
            }
            if ($key == sandbox_exp::FLD_DESCRIPTION) {
                $this->description = $value;
            }
            if ($key == sandbox_exp::FLD_TYPE_ID) {
                $this->type_id = $value;
            }
        }

        if ($result->is_ok() and $do_save) {
            $result->add_message($this->save());
        }

        return $result;
    }


    /*
     * save
     */

    /**
     * @return bool true if no one has used this source
     */
    function not_used(): bool
    {
        log_debug($this->id);

        // to review: maybe replace by a database foreign key check
        return $this->not_changed();
    }

    /**
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     *                 to check if the source has been changed
     */
    function not_changed_sql(sql_db $db_con): sql_par
    {
        $db_con->set_class(source::class);
        return $db_con->load_sql_not_changed($this->id, $this->owner_id);
    }

    /**
     * @return bool true if no other user has modified the source
     */
    function not_changed(): bool
    {
        log_debug($this->id . ' by someone else than the owner (' . $this->owner_id . ')');

        global $db_con;
        $result = true;

        if ($this->id == 0) {
            log_err('The id must be set to detect if the link has been changed');
        } else {
            $qp = $this->not_changed_sql($db_con);
            $db_row = $db_con->get1($qp);
            $change_user_id = $db_row[user::FLD_ID];
            if ($change_user_id > 0) {
                $result = false;
            }
        }
        log_debug('for ' . $this->id . ' is ' . zu_dsp_bool($result));
        return $result;
    }

    /**
     * create an SQL statement to retrieve the user changes of the current source
     *
     * @param sql $sc with the target db_type set
     * @param string $class the name of the child class from where the call has been triggered
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_sql_user_changes(sql $sc, string $class = self::class): sql_par
    {
        $sc->set_class(source::class, true);
        return parent::load_sql_user_changes($sc, $class);
    }

    /**
     * set the update parameters for the source url
     * @param sql_db $db_con the database connection that can be either the real database connection or a simulation used for testing
     * @param source $db_rec the database record before the saving
     * @param source $std_rec the database record defined as standard because it is used by most users
     * @return string if not empty the message that should be shown to the user
     */
    private function save_field_url(sql_db $db_con, source $db_rec, source $std_rec): string
    {
        $result = '';
        if ($db_rec->url <> $this->url) {
            $log = $this->log_upd();
            $log->old_value = $db_rec->url;
            $log->new_value = $this->url;
            $log->std_value = $std_rec->url;
            $log->row_id = $this->id;
            $log->set_field(self::FLD_URL);
            $result = $this->save_field_user($db_con, $log);
        }
        return $result;
    }

    /**
     * save all updated source fields excluding the name, because already done when adding a source
     * @param sql_db $db_con the database connection that can be either the real database connection or a simulation used for testing
     * @param source|sandbox $db_rec the database record before the saving
     * @param source|sandbox $std_rec the database record defined as standard because it is used by most users
     * @return string if not empty the message that should be shown to the user
     */
    function save_fields(sql_db $db_con, source|sandbox $db_rec, source|sandbox $std_rec): string
    {
        $result = parent::save_fields_typed($db_con, $db_rec, $std_rec);
        $result .= $this->save_field_url($db_con, $db_rec, $std_rec);
        log_debug('all fields for ' . $this->dsp_id() . ' has been saved');
        return $result;
    }

}
