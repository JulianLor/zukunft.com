<?php

/*

    controller.php - the base class for API controller
    --------------

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

namespace controller;

use api\list_api;
use api\type_lists_api;
use api\user_sandbox_api;
use api_message;
use source;
use type_lists;

class controller
{

    // the parameter names used in the url
    const URL_API_PATH = 'api/';
    const URL_VAR_DEBUG = 'debug';
    const URL_VAR_WORD = 'words';
    const URL_VAR_ID = 'id';

    // used for the change log
    const URL_VAR_WORD_ID = 'word_id';
    const URL_VAR_WORD_FLD = 'word_field';

    // path parameters
    const PATH_API_REDIRECT = '/../../'; // get from the __DIR__ to the php root path
    const PATH_MAIN_LIB = 'src/main/php/zu_lib.php'; // the main php library the contains all other paths

    /**
     * response to a get request
     *
     * @param string $api_obj the object as a json string that should be returned
     * @param string $msg the message as a json string that should be returned
     * @return void
     */
    private function get_response(string $api_obj, string $msg): void
    {
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET");

        // return the api json or the error message
        if ($msg == '') {

            // set response code - 200 OK
            http_response_code(200);

            // return e.g. the word object
            echo $api_obj;

        } else {

            // set response code - 400 Bad Request
            http_response_code(400);

            // tell the user no products found
            echo json_encode(
                array("message" => $msg)
            );
        }
    }

    /**
     * response to post, get, put and delete requests
     *
     * @param string $api_obj the object as a json string that should be returned
     * @param string $msg the message as a json string that should be returned
     * @return void
     */
    private function curl_response(string $api_obj, string $msg): void
    {
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST,GET,PUT,DELETE");

        $method = $_SERVER['REQUEST_METHOD'];
        $request_text = file_get_contents('php://input');
        $request_json = json_decode($request_text, true);

        switch ($method) {
            case 'PUT':
                // set response code - 200 OK
                http_response_code(200);

                $request_body = $this->check_api_msg($request_json);

                echo json_encode(
                    array("result" => $this->put($request_body))
                );
                break;
            case 'GET':
                // return the api json or the error message
                if ($msg == '') {

                    // set response code - 200 OK
                    http_response_code(200);

                    // return e.g. the word object
                    echo $api_obj;

                } else {

                    // set response code - 400 Bad Request
                    http_response_code(400);

                    // tell the user no products found
                    echo json_encode(
                        array("message" => $msg)
                    );
                }
                break;
            case 'POST':
                // set response code - 200 OK
                http_response_code(200);
                echo json_encode(
                    array("result" => $this->post($request_json))
                );
                break;
            case 'DELETE':
                // set response code - 200 OK
                http_response_code(200);
                echo json_encode(
                    array("result" => $this->delete($request_json))
                );
                break;
            default:
                // set response code - 400 Bad Request
                http_response_code(400);
                break;
        }
    }

    public function not_permitted(string $msg): void
    {
        http_response_code(401);
        $this->curl_response('', $msg);
    }

    /**
     * encode an user sandbox object for the frontend api
     * and response to a get request
     *
     * @param user_sandbox_api $api_obj the object that should be encoded
     * @param string $msg if filled the message that should be shown to the user instead of the object
     * @return void
     */
    function get(user_sandbox_api $api_obj, string $msg): void
    {
        // return the api json or the error message
        if ($msg == '') {
            $this->get_response(json_encode($api_obj), $msg);
        } else {
            // tell the user e.g. that no products found
            $this->get_response('', $msg);
        }
    }

    function get_list(list_api $api_obj, string $msg): void
    {
        // return the api json or the error message
        if ($msg == '') {
            $this->get_response(json_encode($api_obj), $msg);
        } else {
            // tell the user e.g. that no products found
            $this->get_response('', $msg);
        }
    }

    function get_api_msg(api_message $api_obj, string $msg): void
    {
        // return the api json or the error message
        if ($msg == '') {
            $this->get_response(json_encode($api_obj), $msg);
        } else {
            // tell the user e.g. that no products found
            $this->get_response('', $msg);
        }
    }

    function get_types(type_lists_api $api_obj, string $msg): void
    {
        // return the api json or the error message
        if ($msg == '') {
            $this->get_response(json_encode($api_obj), $msg);
        } else {
            // tell the user e.g. that no products found
            $this->get_response('', $msg);
        }
    }

    function get_export(object $api_obj, string $msg): void
    {
        // return the api json or the error message
        if ($msg == '') {
            $this->get_response(json_encode($api_obj), $msg);
        } else {
            // tell the user e.g. that no products found
            $this->get_response('', $msg);
        }
    }

    public function check_api_msg(array $api_msg): array
    {
        $msg_ok = true;
        $body = array();
        // TODO check transfer time
        // TODO check if version matches
        if ($msg_ok) {
            if (array_key_exists('body', $api_msg)) {
                $body = $api_msg['body'];
            } else {
                $msg_ok = false;
            }
        }
        if ($msg_ok) {
            return $body;
        } else {
            return array();
        }
    }

    /**
     * encode a user sandbox object for the frontend api
     * and response to curl requests
     *
     * @param api_message $api_msg the object that should be encoded
     * @param string $msg if filled the message that should be shown to the user instead of the object
     * @return void
     */
    function curl(api_message $api_msg, string $msg): void
    {
        // return the api json or the error message
        if ($msg == '') {
            $this->curl_response(json_encode($api_msg), $msg);
        } else {
            // tell the user e.g. that no products found
            $this->curl_response('', $msg);
        }
    }

    function put(array $request): string
    {
        global $usr;
        // TODO switch between the objects
        $src = new source($usr);
        $result = $src->add_from_api_msg($request)->get_last_message();
        if ($result == '') {
            $result = $src->id();
        }
        return $result;
    }

    function post(array $request): string
    {
        return 'post';
    }

    function delete(array $request): string
    {
        return 'delete';
    }

}
