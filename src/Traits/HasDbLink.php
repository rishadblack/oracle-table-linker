<?php

namespace Rishadblack\OracleTableLinker\Traits;

use Illuminate\Support\Facades\DB;

trait HasDbLink
{
    // Static property to track the first call globally
    protected static $firstCall = true;

    // Store the original table name with the database link
    protected static $mainTable;

    // Static property to indicate if the current operation is a write operation
    protected static $isWrite = false;

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
     * Determine whether the table has a DB link (i.e., contains '@').
     *
     * @return bool
     */
    private function hasDbLink(): bool
    {
        return strpos($this->getRawTableName(), '@') !== false;
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
        $hasDbLink = $this->hasDbLink();

        // For write operations (INSERT, UPDATE, DELETE), always return the raw table name with DB link if present
        if (static::$isWrite) {
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

    /**
     * Ensure raw table name is used for delete operations.
     *
     * @return bool|null
     */
    public function delete()
    {
        $this->initializeMainTable(); // Make sure table is initialized before deletion
        static::$isWrite = true; // Mark this as a write operation
        $result = parent::delete(); // Perform the actual delete operation
        static::$isWrite = false; // Reset the write flag after the operation
        static::$firstCall = true; // Reset firstCall to ensure correct table name recalculation

        return $result;
    }

    /**
     * Hook into the save operation to handle inserts or updates.
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->initializeMainTable(); // Make sure table is initialized before saving
        static::$isWrite = true; // Mark this as a write operation
        $result = parent::save($options); // Perform the actual save operation
        static::$isWrite = false; // Reset the write flag after the operation
        static::$firstCall = true; // Reset firstCall for future operations

        return $result;
    }

    /**
     * Hook into the insert operation.
     *
     * @param array $values
     * @return bool
     */
    public static function insert(array $values)
    {
        static::$isWrite = true; // Mark this as a write operation
        $result = parent::insert($values); // Perform the actual insert operation
        static::$isWrite = false; // Reset the write flag after the operation

        return $result;
    }

    /**
     * Hook into the update operation.
     *
     * @param array $attributes
     * @param array $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        $this->initializeMainTable(); // Make sure table is initialized before updating
        static::$isWrite = true; // Mark this as a write operation
        $result = parent::update($attributes, $options); // Perform the actual update operation
        static::$isWrite = false; // Reset the write flag after the operation
        static::$firstCall = true; // Reset firstCall for future operations

        return $result;
    }
}
