<?php

/*

    phrase_group_word_link.php - only for fast selection of the phrase group assigned to one word
    --------------------------

    replication of the words linked to a phrase group saved in the word_ids field

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

class phrase_group_word_link
{
    // object specific database and JSON object field names
    const FLD_ID = 'phrase_group_word_link_id';

    // all database field names excluding the id
    const FLD_NAMES = array(
        phrase_group::FLD_ID,
        word::FLD_ID
    );

    // database fields
    public int $id;        // the primary database id of the numeric value, which is the same for the standard and the user specific value
    public int $grp_id;    // the phrase group id and not the object to reduce the memory usage
    public int $wrd_id;    // the word id and not the object to reduce the memory usage

    function __construct()
    {
        $this->id = 0;
        $this->grp_id = 0;
        $this->wrd_id = 0;
    }

    function row_mapper(array $db_row): bool
    {
        $result = false;
        if ($db_row != null) {
            $this->id = $db_row[self::FLD_ID];
            $this->grp_id = $db_row[phrase_group::FLD_ID];
            $this->wrd_id = $db_row[word::FLD_ID];
            $result = true;
        }
        return $result;
    }

    /**
     * create an SQL statement to retrieve a single phrase group word link by the id
     *
     * @param sql_db $db_con the db connection object as a function parameter for unit testing
     * @return sql_par the SQL statement, the name of the SQL statement and the parameter list
     */
    function load_sql(sql_db $db_con): sql_par
    {
        $qp = new sql_par();
        $qp->name = self::class . '_by_';
        $db_con->set_type(DB_TYPE_PHRASE_GROUP_WORD_LINK);

        if ($this->id > 0) {
            $qp->name .= 'id';
            $db_con->add_par(sql_db::PAR_INT, $this->id);
        } else {
            log_err('The phrase group word id must be set ' .
                'to load a ' . self::class, self::class . '->load_sql');

        }
        $db_con->set_fields(self::FLD_NAMES);
        $db_con->set_name($qp->name);
        $qp->sql = $db_con->select();
        $qp->par = $db_con->get_par();

        return $qp;
    }

    /**
     * load the word to phrase group link from the database
     */
    function load(): bool
    {
        global $db_con;
        $qp = $this->load_sql($db_con);
        return $this->row_mapper($db_con->get1($qp));
    }

}
