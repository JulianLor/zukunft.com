<?php

/*

  value_exp.php - the simple export object for a value
  -------------
  
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

namespace model\export;

class value_exp extends exp_obj
{

    // field names used for JSON creation
    public ?array $words = null;
    public ?array $triples = null;
    public ?string $time = '';
    public ?string $number = '';
    public ?string $share = '';
    public ?string $protection = '';
    public ?string $source = '';

    function reset()
    {
        $this->words = [];
        $this->triples = [];
        $this->time = '';
        $this->number = '';
        $this->share = '';
        $this->protection = '';
        $this->source = '';
    }

}
