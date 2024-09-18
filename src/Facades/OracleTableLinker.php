<?php

namespace Rishadblack\OracleTableLinker\Facades;

use Illuminate\Support\Facades\Facade;

class OracleTableLinker extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'oracle-table-linker';
    }
}
