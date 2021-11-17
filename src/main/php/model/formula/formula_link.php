<?php

/*

    formula_link.php - link a formula to a word
    ----------------

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

class formula_link extends user_sandbox_link
{

    // list of the formula link types that have a coded functionality
    const DEFAULT = "default";               // a simple link between a formula and a phrase
    const TIME_PERIOD = "time_period_based"; // for time based links

    // the database and JSON object field names used only for formula links
    const FLD_ID = 'formula_link_id';
    const FLD_TYPE = 'link_type_id';

    // all database field names excluding the id
    const FLD_NAMES = array(
        self::FLD_TYPE,
        self::FLD_EXCLUDED
    );

    // database fields additional to the user sandbox fields
    public ?int $order_nbr = null;    // to set the priority of the formula links
    public ?int $link_type_id = null; // define a special behavior for this link (maybe not needed at the moment)

    /**
     * formula_link constructor that set the parameters for the user_sandbox object
     */
    function __construct()
    {
        parent::__construct();
        $this->obj_type = user_sandbox::TYPE_LINK;
        $this->obj_name = DB_TYPE_FORMULA_LINK;
        $this->from_name = DB_TYPE_FORMULA;
        $this->to_name = DB_TYPE_PHRASE;
        $this->reset();
    }

    function reset()
    {
        parent::reset();

        $this->reset_objects();

        $this->order_nbr = null;
        $this->link_type_id = null;
    }

    /**
     * reset the in memory fields used e.g. if some ids are updated
     */
    private function reset_objects()
    {
        $this->fob = new formula();
        $this->tob = new phrase();
    }

    function row_mapper($db_row, $map_usr_fields = false)
    {
        if ($db_row != null) {
            if ($db_row[formula_link::FLD_ID] > 0) {
                $this->id = $db_row[formula_link::FLD_ID];
                $this->owner_id = $db_row[self::FLD_USER];
                $this->fob->id = $db_row[formula::FLD_ID];
                $this->tob->id = $db_row[phrase::FLD_ID];
                $this->link_type_id = $db_row[formula_link::FLD_TYPE];
                $this->excluded = $db_row[self::FLD_EXCLUDED];
                if ($map_usr_fields) {
                    $this->usr_cfg_id = $db_row[sql_db::USER_PREFIX . $this->obj_name . sql_db::FLD_EXT_ID];
                }
            } else {
                $this->id = 0;
            }
        } else {
            $this->id = 0;
        }
    }

    /*
     * internal check function
     */

    /**
     * @return bool true if the user is valid
     */
    private function is_usr_set(): bool
    {
        $result = false;
        if ($this->usr != null) {
            if ($this->usr->id > 0) {
                $result = true;
            }
        }
        return $result;
    }

    /*
     * get functions
     */

    /**
     * @return int the formula id and null if the formula is not set
     */
    function formula_id(): int
    {
        $result = 0;
        if ($this->fob != null) {
            if ($this->fob->id > 0) {
                $result = $this->fob->id;
            }
        }
        return $result;
    }

    /**
     * @return int the phrase id and null if the phrase is not set
     */
    function phrase_id(): int
    {
        $result = 0;
        if ($this->tob != null) {
            if ($this->tob->id > 0) {
                $result = $this->tob->id;
            }
        }
        return $result;
    }

    /*
     * load functions
     */

    /**
     * @return string the query name extension to make the query name unique and parameter specific
     */
    private function load_sql_name_extension(): string
    {
        $result = '';
        if ($this->id != 0) {
            $result .= 'id';
        } elseif ($this->is_unique()) {
            $result .= 'link_ids';
        } else {
            log_err("Either the database ID (" . $this->id . ") or the link ids must be set to load a word.", "formula_link->load");
        }
        return $result;
    }

    /**
     * create an SQL statement to retrieve the parameters of the standard formula link from the database
     *
     * @param sql_db $db_con the db connection object as a function parameter for unit testing
     * @param bool $get_name to create the SQL statement name for the predefined SQL within the same function to avoid duplicating if in case of more than on where type
     * @return string the SQL statement base on the parameters set in $this
     */
    function load_standard_sql(sql_db $db_con, bool $get_name = false): string
    {
        $sql_name = self::class . '_standard_by_' . $this->load_sql_name_extension();
        $db_con->set_type(DB_TYPE_FORMULA_LINK);
        $db_con->set_usr($this->usr->id);
        $db_con->set_link_fields(formula::FLD_ID, phrase::FLD_ID);
        $db_con->set_fields(array(sql_db::FLD_USER_ID, formula_link::FLD_TYPE, self::FLD_EXCLUDED));
        $db_con->set_where_link($this->id, $this->formula_id(), $this->phrase_id());
        $sql = $db_con->select();

        if ($get_name) {
            $result = $sql_name;
        } else {
            $result = $sql;
        }
        return $result;
    }

    /**
     * load the standard formula link to check if the user has done some personal changes
     * e.g. switched off a formula assignment
     * @return bool true if the loading of the standard formula link been successful
     */
    function load_standard(): bool
    {

        global $db_con;
        $result = false;

        if ($this->is_unique()) {
            $sql = $this->load_standard_sql($db_con);

            if ($db_con->get_where() <> '') {
                $db_frm = $db_con->get1($sql);
                $this->row_mapper($db_frm);
                $result = $this->load_owner();
            }
        }
        return $result;
    }

    /**
     * create an SQL statement to retrieve the parameters of a formula link from the database
     *
     * @param sql_db $db_con the db connection object as a function parameter for unit testing
     * @param bool $get_name to create the SQL statement name for the predefined SQL within the same function to avoid duplicating if in case of more than on where type
     * @return string the SQL statement base on the parameters set in $this
     */
    function load_sql(sql_db $db_con, bool $get_name = false): string
    {
        $sql_name = self::class . '_by_' . $this->load_sql_name_extension();
        $db_con->set_type(DB_TYPE_FORMULA_LINK);
        $db_con->set_usr($this->usr->id);
        $db_con->set_link_fields(formula::FLD_ID, phrase::FLD_ID);
        $db_con->set_usr_num_fields(array(formula_link::FLD_TYPE, self::FLD_EXCLUDED));
        $db_con->set_where_link($this->id, $this->formula_id(), $this->phrase_id());
        $sql = $db_con->select();

        if ($get_name) {
            $result = $sql_name;
        } else {
            $result = $sql;
        }
        return $result;
    }

    /**
     * load the missing formula link parameters from the database
     * the formula link can be either identified by the id
     * or by the IDs of the formula and the assigned phrase
     * @return bool true if the loading of the formula link been successful
     */
    function load(): bool
    {
        global $db_con;
        $result = false;

        // check the all minimal input parameters are set
        if (!$this->is_usr_set()) {
            log_err("The user id must be set to load a formula link.", "formula_link->load");
        } else {

            if ($this->is_unique()) {
                $sql = $this->load_sql($db_con);

                if ($db_con->get_where() <> '') {
                    $db_row = $db_con->get1($sql);
                    $this->row_mapper($db_row, true);
                    if ($this->id > 0) {
                        log_debug('formula_link->load (' . $this->id . ')');
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * to load the formula and the phase object
     * if the link object is loaded by an external query like in user_display to show the sandbox
     * @return bool true if the loading of the linked objects has been successful
     */
    function load_objects(): bool
    {
        $result = true;
        if ($this->formula_id() > 0) {
            $frm = new formula;
            $frm->id = $this->formula_id();
            $frm->usr = $this->usr;
            if ($frm->load()) {
                $this->fob = $frm;
            } else {
                $result = false;
            }
        }
        if ($result) {
            if ($this->phrase_id() <> 0) {
                $phr = new phrase;
                $phr->id = $this->phrase_id();
                $phr->usr = $this->usr;
                if ($phr->load()) {
                    $this->tob = $phr;
                } else {
                    $result = false;
                }
            }
        }
        $this->link_type_name();
        return $result;
    }

    /*
     * display functions
     */

    /**
     * @return string the unique id of the formula link
     */
    function dsp_id(): string
    {
        $result = '';

        if ($this->fob != null) {
            if ($this->fob->name <> '') {
                $result .= $this->fob->name . ' '; // e.g. Company details
                $result .= ' (' . $this->fob->id . ')';
            }
        }
        if ($this->tob != null) {
            if ($this->tob->name <> '') {
                if ($result != '') {
                    $result .= ' ';
                }
                $result .= $this->tob->name;     // e.g. cash flow statement
                $result .= ' (' . $this->tob->id . ')';
            }
        }

        if ($this->id > 0) {
            $result .= ' -> ' . $this->id;
        }
        if (isset($this->usr)) {
            $result .= ' for user ' . $this->usr->id . ' (' . $this->usr->name . ')';
        }
        return $result;
    }

    /**
     * @return string the html code to display the link name
     */
    function name(): string
    {
        $result = '';

        if ($this->fob != null) {
            $result = $this->fob->name();
        }
        if ($this->tob != null) {
            $result = ' to ' . $this->tob->name();
        }

        return $result;
    }

    /**
     * @return string the name of the formula link e.g. to describe to the user what can be done with undo
     */
    function link_type_name(): string
    {
        if ($this->link_type_id > 0) {
            return cl_name(db_cl::FORMULA_LINK_TYPE, $this->link_type_id);
        } else {
            return '';
        }
    }

    /**
     * @return string return the html code to display the link name
     */
    function name_linked(string $back = ''): string
    {
        $result = '';

        $this->load_objects();
        if (isset($this->fob) and isset($this->tob)) {
            $result = $this->fob->name_linked($back) . ' to ' . $this->tob->dsp_link();
        } else {
            $result .= log_err("The formula or the linked word cannot be loaded.", "formula_link->name");
        }

        return $result;
    }

    /*
     * save functions
     */

    /**
     * @return bool true if no one has used this formula
     */
    function not_used(): bool
    {
        log_debug('formula_link->not_used (' . $this->id . ')');

        // to review: maybe replace by a database foreign key check
        return $this->not_changed();
    }

    /**
     * @return string the SQL statement to check if no one else has changed the formula link
     */
    function not_changed_sql(bool $get_name = false): string
    {
        $sql_name = self::class . '_not_changed';
        $sql = "SELECT user_id 
                FROM user_formula_links 
               WHERE formula_link_id = " . $this->id;
        if ($this->owner_id > 0) {
            $sql .= " AND user_id <> " . $this->owner_id;
            $sql_name .= self::class . '_by_owner';
        }
        $sql .= " AND (excluded <> 1 OR excluded is NULL);";

        if ($get_name) {
            $result = $sql_name;
        } else {
            $result = $sql;
        }
        return $result;
    }

    /**
     * @return bool true if no other user has modified the formula link
     */
    function not_changed(): bool
    {
        log_debug('formula_link->not_changed (' . $this->id . ') by someone else than the owner (' . $this->owner_id . ')');

        global $db_con;
        $result = true;
        $sql = $this->not_changed_sql();
        $db_con->usr_id = $this->usr->id;
        $db_row = $db_con->get1($sql);
        if ($db_row != null) {
            if ($db_row[self::FLD_USER] > 0) {
                $result = false;
            }
        }
        log_debug('formula_link->not_changed for ' . $this->id . ' is ' . zu_dsp_bool($result));
        return $result;
    }

    /**
     * @return bool true if the user is the owner and no one else has changed the formula_link
     * because if another user has changed the formula_link and the original value is changed, maybe the user formula_link also needs to be updated
     */
    function can_change(): bool
    {
        $can_change = false;
        if ($this->owner_id == $this->usr->id or $this->owner_id <= 0) {
            $can_change = true;
        }
        log_debug('formula_link->can_change -> (' . zu_dsp_bool($can_change) . ')');
        return $can_change;
    }

    /**
     * @return bool true if a record for a user specific configuration already exists in the database
     */
    function has_usr_cfg(): bool
    {
        $has_cfg = false;
        if ($this->usr_cfg_id > 0) {
            $has_cfg = true;
        }
        return $has_cfg;
    }

    /**
     * create an SQL statement to retrieve the user specific formula link from the database
     *
     * @param sql_db $db_con the db connection object as a function parameter for unit testing
     * @param bool $get_name to create the SQL statement name for the predefined SQL within the same function to avoid duplicating if in case of more than on where type
     * @return string the SQL statement base on the parameters set in $this
     */
    function load_user_sql(sql_db $db_con, bool $get_name = false): string
    {
        $sql_name = self::class . '_user_sandbox';
        $db_con->set_type(DB_TYPE_FORMULA_LINK, true);
        $db_con->set_fields(array(formula_link::FLD_TYPE, self::FLD_EXCLUDED));
        $db_con->set_usr($this->usr->id);
        $db_con->set_where($this->id);
        $sql = $db_con->select();

        if ($get_name) {
            $result = $sql_name;
        } else {
            $result = $sql;
        }
        return $result;
    }

    /**
     * create a database record to save user specific settings for this formula_link
     * @return bool true if adding the new formula link has been successful
     */
    function add_usr_cfg(): bool
    {
        global $db_con;
        $result = true;

        if (!$this->has_usr_cfg()) {
            // check again if there ist not yet a record
            $sql = $this->load_user_sql($db_con);
            $db_row = $db_con->get1($sql);
            if ($db_row != null) {
                $this->usr_cfg_id = $db_row[formula_link::FLD_ID];
            }
            // create an entry in the user sandbox
            $db_con->set_type(DB_TYPE_USER_PREFIX . DB_TYPE_FORMULA_LINK);
            $log_id = $db_con->insert(array(formula_link::FLD_ID, user_sandbox::FLD_USER), array($this->id, $this->usr->id));
            if ($log_id <= 0) {
                log_err('Insert of user_formula_link failed.');
                $result = false;
            } else {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * check if the database record for the user specific settings can be removed
     * @return bool true  if the checking and the potential removing has been successful,
     *                       which does not mean, that the user sandbox database row has actually been removed
     *              false if the deleting has cause an internal error
     */
    function del_usr_cfg_if_not_needed(): bool
    {
        log_debug('formula_link->del_usr_cfg_if_not_needed for "' . $this->dsp_id() . ' und user ' . $this->usr->name);

        global $db_con;
        $result = true;

        // check again if there ist not yet a record
        $sql = $this->load_user_sql($db_con);
        $db_con->usr_id = $this->usr->id;
        $db_row = $db_con->get1($sql);
        if ($db_row) {
            if ($db_row[formula_link::FLD_ID] > 0) {
                // check if all fields are null
                if (!$this->is_usr_cfg_used($db_row, self::FLD_NAMES)) {
                    // actually delete the entry in the user sandbox
                    $result = $this->del_usr_cfg_exe($db_con);
                }
            }
        }
        return $result;
    }


    /**
     * set the main log entry parameters for updating one display word link field
     * e.g. that the user can see "moved formula list to position 3 in word view"
     * @return user_log the change log object with the presets for formula links
     */
    function log_upd_field(): user_log
    {
        $log = new user_log;
        $log->usr = $this->usr;
        $log->action = 'update';
        if ($this->can_change()) {
            $log->table = 'formula_links';
        } else {
            $log->table = 'user_formula_links';
        }

        return $log;
    }

    /**
     * set the update parameters for the formula type
     * @return string the message shown to the user why the action has failed or an empty string if everything is fine
     */
    function save_field_type(sql_db $db_con, $db_rec, $std_rec): string
    {
        $result = '';
        if ($db_rec->link_type_id <> $this->link_type_id) {
            $log = $this->log_upd_field();
            $log->old_value = $db_rec->link_type_name();
            $log->old_id = $db_rec->link_type_id;
            $log->new_value = $this->link_type_name();
            $log->new_id = $this->link_type_id;
            $log->std_value = $std_rec->link_type_name();
            $log->std_id = $std_rec->link_type_id;
            $log->row_id = $this->id;
            $log->field = formula_link::FLD_TYPE;
            $result .= $this->save_field_do($db_con, $log);
        }
        return $result;
    }

    /**
     * save all updated formula_link fields excluding the name, because already done when adding a formula_link
     * @return string the message shown to the user why the action has failed or an empty string if everything is fine
     */
    function save_fields(sql_db $db_con, $db_rec, $std_rec): string
    {
        // link type not used at the moment
        $result = $this->save_field_type($db_con, $db_rec, $std_rec);
        $result .= $this->save_field_excluded($db_con, $db_rec, $std_rec);
        log_debug('formula_link->save_fields all fields for "' . $this->fob->name . '" to "' . $this->tob->name . '" has been saved');
        return $result;
    }

    /**
     * update a formula_link in the database or create a user formula_link
     * @return string the message shown to the user why the action has failed or an empty string if everything is fine
     */
    function save(): string
    {

        global $db_con;
        $result = '';

        // check if the required parameters are set
        if (isset($this->fob) and isset($this->tob)) {
            log_debug('formula_link->save "' . $this->fob->name . '" to "' . $this->tob->name . '" (id ' . $this->id . ') for user ' . $this->usr->name);
        } elseif ($this->id > 0) {
            log_debug('formula_link->save id ' . $this->id . ' for user ' . $this->usr->name);
        } else {
            log_err("Either the formula and the word or the id must be set to link a formula to a word.", "formula_link->save");
        }

        // load the objects if needed
        $this->load_objects();

        // build the database object because the is anyway needed
        $db_con->set_usr($this->usr->id);
        $db_con->set_type(DB_TYPE_FORMULA_LINK);

        // check if a new value is supposed to be added
        if ($this->id <= 0) {
            log_debug('formula_link->save check if a new formula_link for "' . $this->fob->name . '" and "' . $this->tob->name . '" needs to be created');
            // check if a formula_link with the same formula and word is already in the database
            $db_chk = new formula_link;
            $db_chk->fob = $this->fob;
            $db_chk->tob = $this->tob;
            $db_chk->usr = $this->usr;
            $db_chk->load_standard();
            if ($db_chk->id > 0) {
                $this->id = $db_chk->id;
            }
        }

        if ($this->id <= 0) {
            log_debug('formula_link->save new link from "' . $this->fob->name . '" to "' . $this->tob->name . '"');
            $result .= $this->add();
        } else {
            log_debug('formula_link->save update "' . $this->id . '"');
            // read the database values to be able to check if something has been changed; done first,
            // because it needs to be done for user and general formulas
            $db_rec = new formula_link;
            $db_rec->id = $this->id;
            $db_rec->usr = $this->usr;
            $db_rec->load();
            $db_rec->load_objects();
            $db_con->set_type(DB_TYPE_FORMULA_LINK);
            log_debug("formula_link->save -> database formula loaded (" . $db_rec->id . ")");
            $std_rec = new formula_link;
            $std_rec->id = $this->id;
            $std_rec->usr = $this->usr; // must also be set to allow to take the ownership
            $std_rec->load_standard();
            log_debug("formula_link->save -> standard formula settings loaded (" . $std_rec->id . ")");

            // for a correct user formula link detection (function can_change) set the owner even if the formula link has not been loaded before the save
            if ($this->owner_id <= 0) {
                $this->owner_id = $std_rec->owner_id;
            }

            // it should not be possible to change the formula or the word, but nevertheless check
            // instead of changing the formula or the word, a new link should be created and the old deleted
            if ($db_rec->fob != null) {
                if ($db_rec->fob->id <> $this->fob->id
                    or $db_rec->tob->id <> $this->tob->id) {
                    log_debug("formula_link->save -> update link settings for id " . $this->id . ": change formula " . $db_rec->formula_id() . " to " . $this->formula_id() . " and " . $db_rec->phrase_id() . " to " . $this->phrase_id());
                    $result .= log_info('The formula link "' . $db_rec->fob->name . '" with "' . $db_rec->tob->name . '" (id ' . $db_rec->formula_id() . ',' . $db_rec->phrase_id() . ') " cannot be changed to "' . $this->fob->name . '" with "' . $this->tob->name . '" (id ' . $this->fob->id . ',' . $this->tob->id . '). Instead the program should have created a new link.', "formula_link->save");
                }
            }

            // check if the id parameters are supposed to be changed
            $this->load_objects();
            if ($result == '') {
                $result = $this->save_id_if_updated($db_con, $db_rec, $std_rec);
            }

            // if a problem has appeared up to here, don't try to save the values
            // the problem is shown to the user by the calling interactive script
            if ($result == '') {
                $result = $this->save_fields($db_con, $db_rec, $std_rec);
            }
        }

        if ($result != '') {
            log_err($result);
        }

        return $result;
    }

}