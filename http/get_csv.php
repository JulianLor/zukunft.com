<?php 

/*

  get_csv.php - get data from zukunft.com in the csv format
  -----------


zukunft.com - calc with words

copyright 1995-2018 by zukunft.com AG, Zurich

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if (isset($_GET['debug'])) { $debug = $_GET['debug']; } else { $debug = 0; }
include_once '../lib/zu_lib.php'; if ($debug > 0) { echo 'libs loaded<br>'; }

// open database
$link = zu_start("get_csv", "", $debug);

  // load the session user parameters
  $usr = New user;
  $result .= $usr->get($debug-1);

  // check if the user is permitted (e.g. to exclude google from doing stupid stuff)
  if ($usr->id > 0) {

    // sample "Nestlé%2Ccountryweight"
    $words = $_GET['words'];
    zu_debug("get_csv(".$words.")", $debug);
    $word_names = explode(",",$words);
    
    $wrd_lst = New word_list;
    $wrd_lst->usr = $usr;
    foreach ($word_names AS $wrd_name) {
      $wrd_lst->add_name($wrd_name, $debug-1);
    }
    $wrd_lst->load($debug-1);

    // get time word
    $time_word_id = 0;
  /*  if (zut_has_time($word_names, $debug-1)) {
      $time_word_lst = zut_time_lst($word_names, $debug-1); 
      // shortcut, replace with a most_useful function
      $time_word_id = $time_word_lst[0]; 
      $word_names = zu_lst_not_in($word_names, $time_word_id, $debug-1);
    } else {
      $time_word_id = zut_get_max_time($word_names[0], $word_names, $debug-1); 
    } 
    zu_debug("-> time word (".$time_word_id.")", $debug); */
    zu_debug("get_csv -> other words (".implode(",",$word_names).")", $debug);
    
    // get formula
    $formula_name = zut_get_formula ($word_names, $debug);
    if ($formula_name <> '') {
      $frm = New formula;
      $frm->usr = $usr;
      $frm->name = $formula_name;
      $frm->load($debug-1);
      //$word_names = zu_lst_not_in($word_names, $formula_name, $debug-1);
      $word_names = array_diff($word_names, array($formula_name));
      zu_debug("get_csv -> word names used (".implode(",",$word_names).")", $debug);
      $formula_id = $frm->id;
      $formula_text = $frm->ref_text;
      zu_debug("get_csv -> formula used (".$formula_text.")", $debug);
    }

    $word_lst = array_keys(zut_names_to_lst($word_names, $debug-1));
    zu_debug("get_csv -> words used (".implode(",",$word_lst).")", $debug);
    
    if ($formula_text <> '') {
      $in_result = zuf_2num($formula_id,$formula_text, $word_lst, $time_word_id, $usr->id, $debug-1);
      $value_lst = $in_result[0];
      if (is_array($value_lst)) {
        $result .= $formula_name.',name'."\r\n<br>";
        foreach ($value_lst AS $value_row) {
          $result .= ''.$value_row[0].','.$value_row[1]."\r\n<br>";
        }
      } else {
        $result .= $formula_name.' '.$value_lst."\r\n<br>";
      }
    }
  }
  
  echo $result;

// Closing connection
zu_end_api($link, $debug);
?>
