<?php

/**
 * 将数据表中的结果集展示成一个下拉列表.
 * @param array $data 结果集数组.
 * @param string $value_field 值字段名
 * @param string $name_field 文案字段名
 * @param string $name 下拉列表的name属性值
 * @return string
 */
function arr2select(array $data, $value_field, $name_field, $name, $selected_value = '') {
    $html = '<select name="' . $name . '">';
    $html .= '<option value="">请选择</option>';
    foreach ($data as $value) {
        if ($selected_value == $value[$value_field]) {
            $html .= '<option value="' . $value[$value_field] . '" selected="selected">' . $value[$name_field] . '</option>';
        } else {

            $html .= '<option value="' . $value[$value_field] . '">' . $value[$name_field] . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}
