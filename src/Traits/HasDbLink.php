<?php

namespace Rishadblack\OracleTableLinker\Traits;

use Illuminate\Support\Facades\DB;

trait HasDbLink
{
    // Static property to track the first call globally
    protected static $firstCall = true;

    // Store the original table name with the database link
    protected static $mainTable;

    /**
     * Initialize the mainTable property if it's not already set.
     */
    protected function initializeMainTable()
    {
        // Store the original table name if not already set
        if (empty(static::$mainTable)) {
            static::$mainTable = $this->getRawTableName(); // Set to the raw table name
        }
    }

    /**
     * Get the raw table name directly from the model's $table property.
     *
     * @return string
     */
    protected function getRawTableName(): string
    {
        return $this->table; // Return the raw table name directly
    }

    /**
     * Extract the alias from the raw table name.
     *
     * @return string
     */
    private function getTableAlias(): string
    {
        $rawTableName = $this->getRawTableName();
        $parts = explode('.', $rawTableName);
        $tableName = end($parts);
        $tableNameParts = explode('@', $tableName);
        return $tableNameParts[0]; // Return the alias part
    }

    /**
     * Determine whether the current operation is a "write" operation.
     *
     * @return bool
     */
    private function isWriteOperation(): bool
    {
        $writeOperations = ['save','insert', 'update', 'delete'];
        foreach (debug_backtrace() as $trace) {
            if (isset($trace['function']) && in_array($trace['function'], $writeOperations)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine the table name to use for queries.
     *
     * @return string|\Illuminate\Database\Query\Expression
     */
    public function getTable()
    {
        $this->initializeMainTable(); // Ensure mainTable is initialized

        $rawTableName = $this->getRawTableName(); // Retrieve the raw table name (with or without DB link)
        $hasDbLink = strpos($rawTableName, '@') !== false;

        // For write operations, always return the raw table name without alias
        if ($this->isWriteOperation()) {
            return DB::raw(static::$mainTable); // Return the original mainTable for write operations
        }

        // For read operations (SELECT), use the alias behavior
        if ($hasDbLink) {
            if (static::$firstCall) {
                static::$firstCall = false; // Mark that the first call has been made
                return DB::raw($rawTableName . ' ' . $this->getTableAlias()); // Return the raw table name with alias on the first call
            }

            return $this->getTableAlias(); // Return only the alias on subsequent calls
        }

        // If no database link, return the table name as-is
        return $rawTableName;
    }
}
