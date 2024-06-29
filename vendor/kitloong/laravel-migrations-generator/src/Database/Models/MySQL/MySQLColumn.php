<?php

namespace KitLoong\MigrationsGenerator\Database\Models\MySQL;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use KitLoong\MigrationsGenerator\Database\Models\DatabaseColumn;
use KitLoong\MigrationsGenerator\Enum\Migrations\Method\ColumnType;
use KitLoong\MigrationsGenerator\Repositories\MariaDBRepository;
use KitLoong\MigrationsGenerator\Repositories\MySQLRepository;
use PDO;

class MySQLColumn extends DatabaseColumn
{
    /**
     * @see https://mariadb.com/kb/en/library/string-literals/#escape-sequences
     */
    private const MARIADB_ESCAPE_SEQUENCES = [
        '\\0'  => "\0",
        "\\'"  => "'",
        '\\"'  => '"',
        '\\b'  => "\b",
        '\\n'  => "\n",
        '\\r'  => "\r",
        '\\t'  => "\t",
        '\\Z'  => "\x1a",
        '\\\\' => '\\',
        '\\%'  => '%',
        '\\_'  => '_',

        // Internally, MariaDB escapes single quotes using the standard syntax
        "''"   => "'",
    ];

    private MySQLRepository $mysqlRepository;

    private MariaDBRepository $mariaDBRepository;

    /**
     * @inheritDoc
     */
    public function __construct(string $table, array $column)
    {
        parent::__construct($table, $column);

        $this->default  = $this->escapeDefault($column['default']);
        $this->unsigned = str_contains($column['type'], 'unsigned');

        if ($this->isMaria()) {
            $this->default = $this->getMariaDBColumnDefault($column['default']);
            $this->default = $this->escapeDefault($this->default);
        }

        $this->mysqlRepository   = app(MySQLRepository::class);
        $this->mariaDBRepository = app(MariaDBRepository::class);

        $this->setTypeToIncrements(true);
        $this->setTypeToUnsigned();

        switch ($this->type) {
            case ColumnType::UNSIGNED_TINY_INTEGER:
            case ColumnType::TINY_INTEGER:
                if ($this->isBoolean()) {
                    $this->type = ColumnType::BOOLEAN;
                }

                break;

            case ColumnType::ENUM:
                $this->presetValues = $this->getEnumPresetValues($column['type']);
                break;

            case ColumnType::SET:
                $this->presetValues = $this->getSetPresetValues($column['type']);
                break;

            case ColumnType::SOFT_DELETES:
            case ColumnType::SOFT_DELETES_TZ:
            case ColumnType::TIMESTAMP:
            case ColumnType::TIMESTAMP_TZ:
                $this->onUpdateCurrentTimestamp = $this->hasOnUpdateCurrentTimestamp();
                break;

            case ColumnType::GEOGRAPHY:
            case ColumnType::GEOMETRY:
            case ColumnType::GEOMETRY_COLLECTION:
            case ColumnType::LINE_STRING:
            case ColumnType::MULTI_LINE_STRING:
            case ColumnType::POINT:
            case ColumnType::MULTI_POINT:
            case ColumnType::POLYGON:
            case ColumnType::MULTI_POLYGON:
                $this->setRealSpatialColumn();
                break;

            default:
        }

        $this->setVirtualDefinition();
        $this->setStoredDefinition();

        if (!$this->isMaria()) {
            return;
        }

        // Extra logic for MariaDB
        switch ($this->type) {
            case ColumnType::LONG_TEXT:
                if ($this->isJson()) {
                    $this->type = ColumnType::JSON;
                }

                break;

            default:
        }
    }

    /**
     * @inheritDoc
     */
    protected function getColumnType(string $type): ColumnType
    {
        return MySQLColumnType::toColumnType($type);
    }

    /**
     * @inheritDoc
     */
    protected function escapeDefault(?string $default): ?string
    {
        $default = parent::escapeDefault($default);

        if ($default === null) {
            return null;
        }

        return addcslashes($default, '\\');
    }

    /**
     * Determine if the connected database is a MariaDB database.
     */
    private function isMaria(): bool
    {
        return str_contains(DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), 'MariaDB');
    }

    /**
     * Check if the column is "tinyint(1)".
     */
    private function isBoolean(): bool
    {
        if ($this->autoincrement) {
            return false;
        }

        $showColumn = $this->mysqlRepository->showColumn($this->tableName, $this->name);

        if ($showColumn === null) {
            return false;
        }

        return Str::startsWith($showColumn->getType(), 'tinyint(1)');
    }

    /**
     * Get the preset values if the column is "enum".
     *
     * @return string[]
     */
    private function getEnumPresetValues(string $fullDefinition): array
    {
        $value = substr(
            $fullDefinition,
            strlen("enum('"),
            -strlen("')"),
        );
        return explode("','", $value);
    }

    /**
     * Get the preset values if the column is "set".
     *
     * @return string[]
     */
    private function getSetPresetValues(string $fullDefinition): array
    {
        $value = substr(
            $fullDefinition,
            strlen("set('"),
            -strlen("')"),
        );
        return explode("','", $value);
    }

    /**
     * Check if the column uses "on update CURRENT_TIMESTAMP".
     */
    private function hasOnUpdateCurrentTimestamp(): bool
    {
        return $this->mysqlRepository->isOnUpdateCurrentTimestamp($this->tableName, $this->name);
    }

    /**
     * MariaDB return `longText` instead of `json` column.
     * Check the check constraint of this column to check if type is `json`.
     * Return true if check constraint contains `json_valid` keyword.
     */
    private function isJson(): bool
    {
        $checkConstraint = $this->mariaDBRepository->getCheckConstraintForJson($this->tableName, $this->name);
        return $checkConstraint !== null;
    }

    /**
     * Set virtual definition if the column is virtual.
     */
    private function setVirtualDefinition(): void
    {
        $virtualDefinition = $this->mysqlRepository->getVirtualDefinition($this->tableName, $this->name);

        if ($virtualDefinition === null) {
            return;
        }

        // The definition of MySQL8 returned `concat(string,_utf8mb4\' \',string_255)`.
        // Replace `\'` to `'` here to avoid double escape.
        $this->virtualDefinition = str_replace("\'", "'", $virtualDefinition);
    }

    /**
     * Set stored definition if the column is stored.
     */
    private function setStoredDefinition(): void
    {
        $storedDefinition = $this->mysqlRepository->getStoredDefinition($this->tableName, $this->name);

        if ($storedDefinition === null) {
            return;
        }

        // The definition of MySQL8 returned `concat(string,_utf8mb4\' \',string_255)`.
        // Replace `\'` to `'` here to avoid double escape.
        $this->storedDefinition = str_replace("\'", "'", $storedDefinition);
    }

    /**
     * Set to geometry or geography.
     */
    private function setRealSpatialColumn(): void
    {
        if (!$this->atLeastLaravel11()) {
            return;
        }

        switch ($this->type) {
            case ColumnType::GEOMETRY_COLLECTION:
                $this->spatialSubType = 'geometryCollection';
                break;

            case ColumnType::LINE_STRING:
                $this->spatialSubType = 'lineString';
                break;

            case ColumnType::MULTI_LINE_STRING:
                $this->spatialSubType = 'multiLineString';
                break;

            case ColumnType::POINT:
                $this->spatialSubType = 'point';
                break;

            case ColumnType::MULTI_POINT:
                $this->spatialSubType = 'multiPoint';
                break;

            case ColumnType::POLYGON:
                $this->spatialSubType = 'polygon';
                break;

            case ColumnType::MULTI_POLYGON:
                $this->spatialSubType = 'multiPolygon';
                break;

            default:
        }

        $this->type = ColumnType::GEOMETRY;

        $this->spatialSrID = $this->mysqlRepository->getSrID($this->tableName, $this->name);

        if ($this->spatialSrID === null) {
            return;
        }

        $this->type = ColumnType::GEOGRAPHY;
    }

    /**
     * Set the column type to "unsigned*" if the column is unsigned.
     */
    private function setTypeToUnsigned(): void
    {
        if (
            !in_array($this->type, [
                ColumnType::BIG_INTEGER,
                ColumnType::INTEGER,
                ColumnType::MEDIUM_INTEGER,
                ColumnType::SMALL_INTEGER,
                ColumnType::TINY_INTEGER,
            ])
            || !$this->unsigned
        ) {
            return;
        }

        $this->type = ColumnType::from('unsigned' . ucfirst($this->type->value));
    }

    /**
     * Return Mysql column default values for MariaDB 10.2.7+ servers.
     *
     * - Since MariaDb 10.2.7 column defaults stored in information_schema are now quoted
     *   to distinguish them from expressions (see MDEV-10134).
     * - CURRENT_TIMESTAMP, CURRENT_TIME, CURRENT_DATE are stored in information_schema
     *   as current_timestamp(), currdate(), currtime()
     * - Quoted 'NULL' is not enforced by Maria, it is technically possible to have
     *   null in some circumstances (see https://jira.mariadb.org/browse/MDEV-14053)
     * - \' is always stored as '' in information_schema (normalized)
     *
     * @link https://mariadb.com/kb/en/library/information-schema-columns-table/
     * @link https://jira.mariadb.org/browse/MDEV-13132
     * @param string|null $columnDefault default value as stored in information_schema for MariaDB >= 10.2.7
     */
    private function getMariaDBColumnDefault(?string $columnDefault): ?string
    {
        if ($columnDefault === 'NULL' || $columnDefault === null) {
            return null;
        }

        if (preg_match('/^\'(.*)\'$/', $columnDefault, $matches) === 1) {
            return strtr($matches[1], self::MARIADB_ESCAPE_SEQUENCES);
        }

        return match ($columnDefault) {
            'current_timestamp()' => 'CURRENT_TIMESTAMP',
            'curdate()' => 'CURRENT_DATE',
            'curtime()' => 'CURRENT_TIME',
            default => $columnDefault,
        };
    }
}
