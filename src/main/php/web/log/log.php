<?php

/*

    /web/log/log.php - base log object to create the html code to display a change of system log entry
    ----------------

    This file is part of the frontend of zukunft.com - calc with words

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

namespace html\log;

include_once WEB_SANDBOX_PATH . 'db_object.php';
include_once API_PATH . 'api.php';
include_once API_PATH . 'controller.php';

use api\api;
use DateTimeInterface;
use html\sandbox\db_object as db_object_dsp;
use controller\controller;
use DateTime;
use Exception;

class log extends db_object_dsp
{

    /*
     * object vars
     */

    private DateTime $time;
    private int $user_id;
    private string $text;
    private int $status;


    /*
     * set and get
     */

    /**
     * set the vars of this log html object bases on the api json array
     * @param array $json_array an api json message
     * @return void
     */
    function set_from_json_array(array $json_array): void
    {
        parent::set_from_json_array($json_array);
        // TODO use empty date instead?
        $sys_log_timestamp = new DateTime();
        if (array_key_exists(controller::API_FLD_TIME, $json_array)) {
            try {
                $sys_log_timestamp = new DateTime($json_array[controller::API_FLD_TIME]);
            } catch (Exception $e) {
                // TODO avoid loops if date writing in log_err fails ?
                log_err('Error converting system log timestamp ' . $json_array[controller::API_FLD_TIME]
                    . ' because ' . $e->getMessage());
            }
        } else {
            log_err('Mandatory time missing in API JSON ' . json_encode($json_array));
        }
        $this->set_time($sys_log_timestamp);
        if (array_key_exists(api::FLD_USER_ID, $json_array)) {
            $this->set_user_id($json_array[api::FLD_USER_ID]);
        } else {
            $this->set_user_id(0);
        }
        if (array_key_exists(controller::API_FLD_TEXT, $json_array)) {
            $this->set_text($json_array[controller::API_FLD_TEXT]);
        } else {
            $this->set_text('');
        }
        if (array_key_exists(controller::API_FLD_STATUS, $json_array)) {
            $this->set_status($json_array[controller::API_FLD_STATUS]);
        } else {
            $this->set_status('');
        }
    }

    function set_time(DateTime $iso_time_str): void
    {
        $this->time = $iso_time_str;
    }

    function time(): DateTime
    {
        return $this->time;
    }

    function set_user_id(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    function user_id(): int
    {
        return $this->user_id;
    }

    function set_text(string $text): void
    {
        $this->text = $text;
    }

    function text(): string
    {
        return $this->text;
    }

    function set_status(int $status): void
    {
        $this->status = $status;
    }

    function status(): int
    {
        return $this->status;
    }


    /*
     * interface
     */

    /**
     * @return array the json message array to send the updated data to the backend
     * an array is used (instead of a string) to enable combinations of api_array() calls
     */
    function api_array(): array
    {
        $vars = parent::api_array();
        $vars[controller::API_FLD_TIME] = $this->time()->format(DateTimeInterface::ATOM);
        $vars[api::FLD_USER_ID] = $this->user_id();
        $vars[controller::API_FLD_TEXT] = $this->text();
        $vars[controller::API_FLD_STATUS] = $this->status();
        return $vars;
    }

}
