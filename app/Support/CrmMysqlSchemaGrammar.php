<?php

namespace App\Support;

use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Fluent;

class CrmMysqlSchemaGrammar extends MySqlGrammar
{
    /** Store application timestamps as explicit UTC values without MySQL 5.7's implicit TIMESTAMP defaults. */
    protected function typeTimestamp(Fluent $column): string
    {
        return $this->typeDateTime($column);
    }
}
