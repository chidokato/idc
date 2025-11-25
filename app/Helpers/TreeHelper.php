<?php

namespace App\Helpers;

class TreeHelper
{
    /**
     * Build select options from a tree structure
     *
     * @param \Illuminate\Support\Collection $items
     * @param int $parentId
     * @param string $prefix
     * @param int|null $selectedId
     * @param string $idField
     * @param string $parentField
     * @param string $nameField
     * @return string
     */
    public static function buildOptions(
        $items,
        $parentId = 0,
        $prefix = '',
        $selectedId = null,
        $idField = 'id',
        $parentField = 'parent',
        $nameField = 'name'
    ) {
        $html = '';

        foreach ($items as $item) {
            if ($item->$parentField == $parentId) {

                $selected = $selectedId == $item->$idField ? 'selected' : '';

                $html .= "<option value='{$item->$idField}' {$selected}>{$prefix}{$item->$nameField}</option>";

                $html .= self::buildOptions(
                    $items,
                    $item->$idField,
                    $prefix . '— ',
                    $selectedId,
                    $idField,
                    $parentField,
                    $nameField
                );
            }
        }

        return $html;
    }
}
