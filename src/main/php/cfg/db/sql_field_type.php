<?php

/*

    /model/dp/sql_field_type.php - enum of the sql field types used
    ----------------------------

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

namespace cfg\db;

enum sql_field_type: string
{

    // prime table index fields
    case KEY_INT = 'intKey'; // a 64-bit integer prime index with auto increase by 1
    case KEY_INT_NO_AUTO = 'intKeyNoAuto'; // a 64-bit integer prime index without auto increase
    case KEY_512 = '512bitKey'; // a 512-bit prime index without auto increase
    case KEY_TEXT = 'textKey'; // a long string prime index without auto increase

    // data field types used
    case NAME = 'name'; // a unique text up to 255 char long to identify a database row
    case CODE_ID = 'code_id'; // a unique text to select single database rows by the program
    case TEXT = 'text'; // a text with variable length that can be used for a combined index without auto increase
    case KEY_PART_TEXT = 'textKeyPart'; // a text with variable length that is part of a combined index
    case KEY_PART_512 = '512bitKeyPart'; // a 512-bit text for a combined index without auto increase
    case KEY_PART_INT_SMALL = 'smallIntKeyPart'; // a small integer that is part of a combined index
    case KEY_PART_INT = 'bigintKeyPart'; // an integer that is part of a combined index
    case REF_512 = '512bitRef'; // a 512-bit foreign key
    case INT = 'bigint'; // the standard integer type
    case INT_SMALL = 'smallint'; // the integer type for a very limited number of entries
    case BOOL = 'bool'; // the one bit true/false type
    case TIME = 'time'; // for the iso timestamp format
    case GEO = 'geo'; // for a geolocation
    case NUMERIC_FLOAT = 'float'; // a float value with double precision

    public function pg_type(): string
    {
        return match($this) {
            self::KEY_INT => 'BIGSERIAL',
            self::KEY_512, self::KEY_PART_512, self::REF_512 => 'char(112)',
            self::NAME => 'varchar(255)',
            self::CODE_ID => 'varchar(100)',
            self::TEXT, self::KEY_TEXT, self::KEY_PART_TEXT => 'text',
            self::INT, self::KEY_INT_NO_AUTO, self::KEY_PART_INT => 'bigint',
            self::INT_SMALL, self::BOOL, self::KEY_PART_INT_SMALL => 'smallint',
            self::NUMERIC_FLOAT => 'double precision',
            self::TIME => 'timestamp',
            self::GEO => 'point',
            default => 'postgres type ' . $this->value .' missing',
        };
    }

    public function mysql_type(): string
    {
        return match($this) {
            self::KEY_INT, self::INT, self::KEY_INT_NO_AUTO, self::KEY_PART_INT => 'bigint',
            self::KEY_512, self::KEY_PART_512, self::REF_512 => 'char(112)',
            self::KEY_TEXT, self::KEY_PART_TEXT => 'char(255)',
            self::NAME => 'varchar(255)',
            self::CODE_ID => 'varchar(100)',
            self::TEXT => 'text',
            self::INT_SMALL, self::BOOL, self::KEY_PART_INT_SMALL => 'smallint',
            self::NUMERIC_FLOAT => 'double',
            self::TIME => 'timestamp',
            self::GEO => 'point',
            default => 'MySQL type ' . $this->value .' missing',
        };
    }

    public function is_key(): bool
    {
        return match($this) {
            self::KEY_INT, self::KEY_INT_NO_AUTO, self::KEY_512, self::KEY_TEXT => true,
            default => false,
        };
    }

    public function is_key_part(): bool
    {
        return match($this) {
            self::KEY_PART_512, self::KEY_PART_INT, self::KEY_PART_INT_SMALL, self::KEY_PART_TEXT => true,
            default => false,
        };
    }

}