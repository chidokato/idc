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


class TreeHelperLv2Only
{
    public static function buildOptions(
        $items,
        $selectedId = null,
        $idField = 'id',
        $parentField = 'parent',
        $nameField = 'name'
    ) {
        $html = '';

        // LV1
        foreach ($items as $lv1) {
            if ($lv1->$parentField == 0) {

                $selected = $selectedId == $lv1->$idField ? 'selected' : '';
                $html .= "<option value='{$lv1->$idField}' {$selected}>{$lv1->$nameField}</option>";

                // LV2
                foreach ($items as $lv2) {
                    if ($lv2->$parentField == $lv1->$idField) {

                        $selected = $selectedId == $lv2->$idField ? 'selected' : '';
                        $html .= "<option value='{$lv2->$idField}' {$selected}>— {$lv2->$nameField}</option>";

                        // ❌ KHÔNG render LV3
                    }
                }
            }
        }

        return $html;
    }
}
