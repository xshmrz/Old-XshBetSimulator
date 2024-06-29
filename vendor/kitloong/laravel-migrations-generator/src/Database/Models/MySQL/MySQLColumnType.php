<?php

namespace KitLoong\MigrationsGenerator\Database\Models\MySQL;

use KitLoong\MigrationsGenerator\Database\DatabaseColumnType;
use KitLoong\MigrationsGenerator\Enum\Migrations\Method\ColumnType;

class MySQLColumnType extends DatabaseColumnType
{
    /**
     * @var array<string, \KitLoong\MigrationsGenerator\Enum\Migrations\Method\ColumnType>
     */
    protected static array $map = [
        'bigint'             => ColumnType::BIG_INTEGER,
        'binary'             => ColumnType::BINARY,
        'bit'                => ColumnType::BOOLEAN,
        'blob'               => ColumnType::BINARY,
        'char'               => ColumnType::CHAR,
        'date'               => ColumnType::DATE,
        'datetime'           => ColumnType::DATETIME,
        'decimal'            => ColumnType::DECIMAL,
        'double'             => ColumnType::DOUBLE,
        'enum'               => ColumnType::ENUM,
        'float'              => ColumnType::FLOAT,
        'geography'          => ColumnType::GEOGRAPHY,
        'geometry'           => ColumnType::GEOMETRY,
        'int'                => ColumnType::INTEGER,
        'integer'            => ColumnType::INTEGER,
        'json'               => ColumnType::JSON,
        'longblob'           => ColumnType::BINARY,
        'longtext'           => ColumnType::LONG_TEXT,
        'mediumblob'         => ColumnType::BINARY,
        'mediumint'          => ColumnType::MEDIUM_INTEGER,
        'mediumtext'         => ColumnType::MEDIUM_TEXT,
        'numeric'            => ColumnType::DECIMAL,
        'real'               => ColumnType::FLOAT,
        'set'                => ColumnType::SET,
        'smallint'           => ColumnType::SMALL_INTEGER,
        'string'             => ColumnType::STRING,
        'text'               => ColumnType::TEXT,
        'time'               => ColumnType::TIME,
        'timestamp'          => ColumnType::TIMESTAMP,
        'tinyblob'           => ColumnType::BINARY,
        'tinyint'            => ColumnType::TINY_INTEGER,
        'tinytext'           => ColumnType::TINY_TEXT,
        'varbinary'          => ColumnType::BINARY,
        'varchar'            => ColumnType::STRING,
        'year'               => ColumnType::YEAR,

        // For MariaDB
        'uuid'               => ColumnType::UUID,

        // Removed from Laravel v11
        'geomcollection'     => ColumnType::GEOMETRY_COLLECTION,
        'linestring'         => ColumnType::LINE_STRING,
        'multilinestring'    => ColumnType::MULTI_LINE_STRING,
        'point'              => ColumnType::POINT,
        'multipoint'         => ColumnType::MULTI_POINT,
        'polygon'            => ColumnType::POLYGON,
        'multipolygon'       => ColumnType::MULTI_POLYGON,

        // For MariaDB
        'geometrycollection' => ColumnType::GEOMETRY_COLLECTION,
    ];

    /**
     * @inheritDoc
     */
    public static function toColumnType(string $dbType): ColumnType
    {
        return self::mapToColumnType(self::$map, $dbType);
    }
}
