<?php

namespace Rishadblack\OracleTableLinker\Traits;

use Illuminate\Support\Facades\DB;

trait HasDbLink
{
    // Instance property to track the first call
    protected static $firstCall = true;

    /**
     * Get the raw table name directly from the model's $table property.
     * This is used to retrieve the full table name, including database link if applicable.
     *
     * @return string
     */
    protected function getRawTableName(): string
    {
        return $this->table;
    }

    /**
     * Extract the alias from the raw table name.
     * The alias is extracted from the portion before the '@' character in the table name.
     * This method assumes that the table name may include a database link.
     *
     * @return string
     */
    private function getTableAlias(): string
    {
        $rawTableName = $this->getRawTableName();
        // Split the table name by '.' to handle schema and database links
        $parts = explode('.', $rawTableName);
        $tableName = end($parts);
        // Split the table name by '@' to handle database links
        $tableNameParts = explode('@', $tableName);
        // Return the first part as the alias
        return $tableNameParts[0];
    }

    /**
     * Determine the table name to use for queries.
     * If the table name includes a database link, return it with an alias on the first call.
     * On subsequent calls, return only the alias.
     * If there is no database link, return the table name as-is.
     *
     * @return string|\Illuminate\Database\Query\Expression
     */
    public function getTable()
    {
        $rawTableName = $this->getRawTableName();
        // Check if the raw table name contains a database link
        $hasDbLink = strpos($rawTableName, '@') !== false;

        if ($hasDbLink) {
            if (self::$firstCall) {
                self::$firstCall = false; // Mark that the first call has been made
                // Return the raw table name with an alias using a raw query expression
                return DB::raw($rawTableName.' '.$this->getTableAlias());
            }
            // For subsequent calls, return only the alias
            return $this->getTableAlias();
        } else {
            // If there is no database link, return the table name as-is
            return $rawTableName;
        }
    }
}
