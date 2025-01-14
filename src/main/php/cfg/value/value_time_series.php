<?php

/*

    model/value/value_time_series.php - the header object for time series values
    ---------------------------------

    TODO add function that decides if the user values should saved in a complete new time series or if overwrites should be saved

    To save values that have a timestamp more efficient in a separate table


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

namespace cfg\value;

use cfg\db\sql;
use cfg\db\sql_db;
use cfg\db\sql_table_type;
use cfg\db\sql_par;
use cfg\group\group;
use cfg\library;
use cfg\sandbox;
use cfg\sandbox_value;
use cfg\source;
use cfg\user;
use cfg\user_message;
use DateTime;

class value_time_series extends sandbox_value
{

    /*
     * database link
     */

    // object specific database and JSON object field names
    const FLD_ID = 'value_time_series_id';
    const FLD_LAST_UPDATE = 'last_update';

    // all database field names excluding the id and excluding the user specific fields
    const FLD_NAMES = array(
        user::FLD_ID,
        group::FLD_ID
    );

    // list of the user specific numeric database field names
    const FLD_NAMES_NUM_USR = array(
        source::FLD_ID,
        sandbox::FLD_EXCLUDED,
        sandbox::FLD_PROTECT
    );

    // list of field names that are only on the user sandbox row
    // e.g. the standard value does not need the share type, because it is by definition public (even if share types within a group of users needs to be defined, the value for the user group are also user sandbox table)
    const FLD_NAMES_USR_ONLY = array(
        sandbox::FLD_SHARE
    );

    /*
     * object vars
     */

    // related objects used also for database mapping
    public group $grp;  // phrases (word or triple) group object for this value
    public ?source $source;    // the source object

    /*
     * construct and map
     */

    /**
     * set the user sandbox type for a value time series object and set the user, which is needed in all cases
     * @param user $usr the user who requested to see this value
     */
    function __construct(user $usr)
    {
        parent::__construct($usr);
        $this->obj_type = sandbox::TYPE_VALUE;
        $this->obj_name = sql_db::TBL_VALUE_TIME_SERIES;

        $this->rename_can_switch = UI_CAN_CHANGE_VALUE;

        $this->reset($usr);
    }

    function reset(): void
    {
        parent::reset();

        $this->grp = new group($this->user());
        $this->source = null;
    }

    /*
     * database load functions that reads the object from the database
     */

    /**
     * map the database fields to the object fields
     *
     * @param array|null $db_row with the data directly from the database
     * @param bool $load_std true if only the standard user sandbox object ist loaded
     * @param bool $allow_usr_protect false for using the standard protection settings for the default object used for all users
     * @param string $id_fld the name of the id field as defined in this child and given to the parent
     * @return bool true if the value time series is loaded and valid
     */
    function row_mapper_sandbox(
        ?array $db_row,
        bool   $load_std = false,
        bool   $allow_usr_protect = true,
        string $id_fld = self::FLD_ID): bool
    {
        $lib = new library();
        $result = parent::row_mapper_sandbox($db_row, $load_std, $allow_usr_protect, self::FLD_ID);
        if ($result) {
            $this->grp->set_id($db_row[group::FLD_ID]);
            if ($db_row[source::FLD_ID] > 0) {
                $this->source = new source($this->user());
                $this->source->set_id($db_row[source::FLD_ID]);
            }
            $this->set_last_update($lib->get_datetime($db_row[self::FLD_LAST_UPDATE], $this->dsp_id()));
        }
        return $result;
    }

    /**
     * create the SQL to load the default time series always by the id
     * @param sql $sc with the target db_type set
     * @param string $class the name of this class
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_standard_sql(sql $sc, string $class = self::class): sql_par
    {
        $sc->set_class(self::class);
        $sc->set_fields(array_merge(self::FLD_NAMES, self::FLD_NAMES_NUM_USR));

        return parent::load_standard_sql($sc, $class);
    }

    /**
     * create the common part of an SQL statement to retrieve the parameters of a time series from the database
     *
     * @param sql $sc with the target db_type set
     * @param string $query_name the name extension to make the query name unique
     * @param string $class the name of the child class from where the call has been triggered
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_sql(sql $sc, string $query_name, string $class = self::class): sql_par
    {
        $qp = parent::load_sql_obj_vars($sc, $class);
        $qp->name .= $query_name;

        $sc->set_class($class);
        $sc->set_name($qp->name);
        $sc->set_usr($this->user()->id());
        $sc->set_fields(self::FLD_NAMES);
        $sc->set_usr_num_fields(self::FLD_NAMES_NUM_USR);
        //$sc->set_usr_only_fields(self::FLD_NAMES_USR_ONLY);

        return $qp;
    }

    /**
     * load the standard value use by most users
     * @param sql_par|null $qp placeholder to align the function parameters with the parent
     * @param string $class the name of this class to be delivered to the parent function
     * @return bool true if a time series has been loaded
     */
    function load_standard(?sql_par $qp = null, string $class = self::class): bool
    {
        global $db_con;
        $qp = $this->load_standard_sql($db_con->sql_creator());
        return parent::load_standard($qp, $class);
    }

    /**
     * create the common part of an SQL statement to retrieve the parameters of a value time series
     *
     * @param sql $sc with the target db_type set
     * @param string $query_name the name extension to make the query name unique
     * @param string $class the name of the child class from where the call has been triggered
     * @param string $ext the query name extension e.g. to differentiate queries based on 1,2, or more phrases
     * @param sql_table_type $tbl_typ the table name extension e.g. to switch between standard and prime values
     * @param bool $usr_tbl true if a db row should be added to the user table
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_sql_multi(
        sql            $sc,
        string         $query_name,
        string         $class = self::class,
        string         $ext = '',
        sql_table_type $tbl_typ = sql_table_type::MOST,
        bool           $usr_tbl = false
    ): sql_par
    {
        $qp = parent::load_sql_multi($sc, $query_name, $class, $ext, $tbl_typ, $usr_tbl);

        // overwrite the standard id field name (value_id) with the main database id field for values "group_id"
        $sc->set_id_field($this->id_field());
        $sc->set_name($qp->name);
        $sc->set_fields(self::FLD_NAMES);
        $sc->set_usr($this->user()->id());
        $sc->set_usr_num_fields(self::FLD_NAMES_NUM_USR);
        //$sc->set_usr_only_fields(self::FLD_NAMES_USR_ONLY);

        return $qp;
    }

    /**
     * create an SQL statement to retrieve a time series by the phrase group from the database
     *
     * @param sql $sc with the target db_type set
     * @param group $grp the phrase group to which the time series should be loaded
     * @param string $class the name of the child class from where the call has been triggered
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_sql_by_grp(sql $sc, group $grp, string $class = self::class): sql_par
    {
        $qp = $this->load_sql($sc, group::FLD_ID);
        $sc->add_where(group::FLD_ID, $grp->id());
        $qp->sql = $sc->sql();
        $qp->par = $sc->get_par();

        return $qp;
    }

    /**
     * just set the class name for the user sandbox function
     * load a reference object by database id
     * TODO load the related time series data
     * @param int|string $id the id of the reference
     * @param string $class the reference class name
     * @return int the id of the object found and zero if nothing is found
     */
    function load_by_id(int|string $id, string $class = self::class): int
    {
        return parent::load_by_id($id, $class);
    }

    /**
     * load a row from the database selected by id
     * TODO load the related time series data
     * @param group $grp the phrase group to which the time series should be loaded
     * @return int the id of the object found and zero if nothing is found
     */
    function load_by_grp(group $grp): int
    {
        global $db_con;

        log_debug($grp->dsp_id());
        $qp = $this->load_sql_by_grp($db_con->sql_creator(), $grp);
        return $this->load($qp);
    }

    /**
     * add a new time series
     * @return user_message with status ok
     *                      or if something went wrong
     *                      the message that should be shown to the user
     *                      including suggested solutions
     */
    function add(): user_message
    {
        log_debug('->add');

        global $db_con;
        $result = new user_message();

        // log the insert attempt first
        $log = $this->log_add();
        if ($log->id() > 0) {
            $db_con->set_class(sql_db::TBL_VALUE_TIME_SERIES);
            $this->id = $db_con->insert_old(
                array(group::FLD_ID, user::FLD_ID, self::FLD_LAST_UPDATE),
                array($this->grp->id(), $this->user()->id(), sql::NOW));
            if ($this->id > 0) {
                // update the reference in the log
                if (!$log->add_ref($this->id)) {
                    $result->add_message('adding the value time series reference in the system log failed');
                }

                // update the phrase links for fast searching
                /*
                $upd_result = $this->upd_phr_links();
                if ($upd_result != '') {
                    $result->add_message('Adding the phrase links of the value time series failed because ' . $upd_result);
                    $this->id = 0;
                }
                */

                // create an empty db_rec element to force saving of all set fields
                //$db_vts = new value_time_series($this->user());
                //$db_vts->id = $this->id;
                // TODO add the data list saving
            }
        }

        return $result;
    }

    /*
     * information
     */

    /**
     * temp overwrite of the id_field function of sandbox_value class until this class is revied
     * @return string|array the field name(s) of the prime database index of the object
     */
    function id_field(): string|array
    {
        $lib = new library();
        return $lib->class_to_name($this::class) . sql_db::FLD_EXT_ID;
    }


    /*
     * write
     */

    /**
     * insert or update a time series in the database or save user specific time series numbers
     */
    function save(): string
    {
        log_debug('->save');

        global $db_con;
        $result = '';

        // build the database object because the is anyway needed
        $db_con->set_class(sql_db::TBL_VALUE_TIME_SERIES);
        $db_con->set_usr($this->user()->id());

        // check if a new time series is supposed to be added
        if ($this->id <= 0) {
            // check if a time series for the phrase group is already in the database
            $db_chk = new value_time_series($this->user());
            $db_chk->load_by_grp($this->grp);
            if ($db_chk->id() > 0) {
                $this->set_id($db_chk->id());
            }
        }

        if ($this->id <= 0) {
            $result .= $this->add()->get_last_message();
        } else {
            // update a value
            // TODO: if no one else has ever changed the value, change to default value, else create a user overwrite

            // read the database value to be able to check if something has been changed
            // done first, because it needs to be done for user and general values
            $db_rec = new value_time_series($this->user());
            $db_rec->load_by_id($this->id);
            $std_rec = new value_time_series($this->user()); // user must also be set to allow to take the ownership
            $std_rec->id = $this->id;
            $std_rec->load_standard();

            // for a correct user value detection (function can_change) set the owner even if the value has not been loaded before the save
            if ($this->owner_id <= 0) {
                $this->owner_id = $std_rec->owner_id;
            }

            // check if the id parameters are supposed to be changed
            $result = $this->save_id_if_updated($db_con, $db_rec, $std_rec);

            // if a problem has appeared up to here, don't try to save the values
            // the problem is shown to the user by the calling interactive script
            if ($result == '') {
                // if the user is the owner and no other user has adjusted the value, really delete the value in the database
                $result = $this->save_fields($db_con, $db_rec, $std_rec);
            }

        }

        if ($result != '') {
            log_err($result);
        }

        return $result;
    }

}