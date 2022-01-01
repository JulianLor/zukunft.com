<?php

/*

    api/system/error_log_list.php - the simple export object to create a json for the frontend API
    -----------------------------

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

class system_error_log_list_api extends api_message
{

    // field names used for JSON creation
    public ?array $system_errors = null;      // a list of system error objects

    function __construct()
    {
        parent::__construct();
        $this->type = api_message::SYS_LOG;
        $this->system_errors = null;
    }

    /**
     * @return false|string the frontend API JSON string
     */
    function get_json(): string
    {
        return json_encode($this);
    }

    /**
     * display the error that are related to the user, so that he can track when they are closed
     * or display the error that are related to the user, so that he can track when they are closed
     * called also from user_display.php/dsp_errors
     */
    function get_html(user $usr, string $back): string
    {
        log_debug('system_error_log_list->display for user "' . $usr->name . '"');

        $result = ''; // reset the html code var

        if (count($this->system_errors) > 0) {
            // prepare to show the word link
            $log_dsp = $this->system_errors[0];
            if ($log_dsp->time <> '') {
                $result .= dsp_tbl_start();
                $row_nbr = 0;
                foreach ($this->system_errors as $log_dsp) {
                    $row_nbr++;
                    if ($row_nbr == 1) {
                        $result .= $this->headline_html();
                    }
                    $result .= $log_dsp->get_html($usr, $back);
                }
                $result .= dsp_tbl_end();
            }
        }

        log_debug('system_error_log_list->display -> done');
        return $result;
    }


    function get_html_page(user $usr, string $back): string
    {
        return parent::get_html_header('System log') . $this->get_html($usr, $back) . parent::get_html_footer();
    }

    /**
     * @return string the HTML code for the table headline
     * should be corresponding to system_error_log_dsp::get_html
     */
    private function headline_html(): string
    {
        $result = '<tr>';
        $result .= '<th> creation time     </th>';
        $result .= '<th> user              </th>';
        $result .= '<th> issue description </th>';
        $result .= '<th> trace             </th>';
        $result .= '<th> program part      </th>';
        $result .= '<th> owner             </th>';
        $result .= '<th> status            </th>';
        $result .= '</tr>';
        return $result;
    }

}