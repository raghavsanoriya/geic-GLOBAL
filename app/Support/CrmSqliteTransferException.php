<?php

namespace App\Support;

use RuntimeException;

class CrmSqliteTransferException extends RuntimeException
{
    /** @param array{phase: string, table?: string, exception: string, code: string} $diagnostics */
    public function __construct(public readonly array $diagnostics)
    {
        parent::__construct('Verified database copy stopped. Target changes were rolled back; source records were not changed.');
    }
}
