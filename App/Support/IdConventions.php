<?php

namespace App\Support;

use Framework\DB\IDbConvention;

/**
 * Pomenovanie databázy v tomto projekte:
 *  - názov tabuľky = názov modelu v snake_case v jednotnom čísle (PovahovyRys -> povahovy_rys),
 *  - primárny kľúč = id_<tabuľka> (Dennik -> id_dennik),
 *  - cudzí kľúč má rovnaký názov ako primárny kľúč, na ktorý ukazuje (dennik.id_rasa -> rasa.id_rasa),
 *  - stĺpce sú v snake_case a vlastnosti modelu v camelCase (id_dennik <-> $idDennik).
 *
 * Vďaka tomu fungujú getOneRelated() a getAllRelated() bez ďalšieho nastavovania.
 */
class IdConventions implements IDbConvention
{
    public function getTableName(string $className): string
    {
        return $this->toSnakeCase($this->shortName($className));
    }

    public function getPkColumnName(string $className): string
    {
        return 'id_' . $this->toSnakeCase($this->shortName($className));
    }

    public function getFkColumn(string $className)
    {
        return $this->getPkColumnName($className);
    }

    public function toDbColumnName(string $propertyName): string
    {
        return $this->toSnakeCase($propertyName);
    }

    public function toPropertyName(string $columnName): string
    {
        return lcfirst(str_replace('_', '', ucwords($columnName, '_')));
    }

    private function shortName(string $className): string
    {
        $parts = explode('\\', $className);
        return end($parts);
    }

    private function toSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
