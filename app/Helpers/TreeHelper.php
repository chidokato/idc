<?php

namespace App\Helpers;

class TreeHelper
{
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



class TreeHelper_disabled
{
    public static function buildDepartmentOptions($items, $parent = 0, $prefix = '', $selectedId = null)
    {
        $html = '';

        foreach ($items as $item) {
            if ($item->parent == $parent) {

                // kiểm tra nếu có con → disable
                $disabled = $item->children()->exists() ? 'disabled' : '';

                // chọn option nếu đúng user đang dùng
                $selected = ($selectedId == $item->id) ? 'selected' : '';

                $html .= "<option value='{$item->id}' {$disabled} {$selected}>{$prefix}{$item->name}</option>";

                // đệ quy xuống cấp dưới
                $html .= self::buildDepartmentOptions($items, $item->id, $prefix . '-- ', $selectedId);
            }
        }

        return $html;
    }
}