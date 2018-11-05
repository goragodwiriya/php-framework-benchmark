<?php
// admin/countrywrite.php
if (MAIN_INIT == 'admin' && $isAdmin) {
    // ค่าที่ส่งมา
    $id = gcms::getVars($_GET, 'id', 0);
    if ($id > 0) {
        $country = $db->getRec(DB_COUNTRY, $id);
    } else {
        $country = array();
    }
    if ($id > 0 && !$country) {
        $title = $lng['LNG_DATA_NOT_FOUND'];
        $content[] = '<aside class=error>'.$title.'</aside>';
    } else {
        // title
        $title = $lng[$id == 0 ? 'LNG_ADD' : 'LNG_EDIT'].' '.$lng['LNG_COUNTRY'];
        $a = array();
        $a[] = '<span class=icon-settings>{LNG_SITE_SETTINGS}</span>';
        $a[] = '<a href="{URLQUERY?module=country}">{LNG_COUNTRY_LIST}</a>';
        $a[] = $id == 0 ? '{LNG_ADD}' : '{LNG_EDIT}';
        // แสดงผล
        $content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
        $content[] = '<section>';
        $content[] = '<header><h1 class=icon-world>'.$title.'</h1></header>';
        $content[] = '<form id=setup_frm class=setup_frm method=post action=index.php>';
        $content[] = '<fieldset>';
        $content[] = '<legend><span>'.$a[2].'</span></legend>';
        // iso
        $content[] = '<div class=item>';
        $content[] = '<label for=write_iso>{LNG_COUNTRY_ISO}</label>';
        $content[] = '<span class="g-input icon-edit"><input type=text name=write_iso id=write_iso value="'.$country['iso'].'" maxlength=2 title="{LNG_PLEASE_FILL}" autofocus></span>';
        $content[] = '<div class=comment id=result_write_iso>{LNG_COUNTRY_ISO_COMMENT}</div>';
        $content[] = '</div>';
        // key
        $content[] = '<div class=item>';
        $content[] = '<label for=write_name>{LNG_COUNTRY_NAME}</label>';
        $content[] = '<span class="g-input icon-edit"><input type=text name=write_name id=write_name value="'.$country['printable_name'].'" maxlength=80 title="{LNG_PLEASE_FILL}"></span>';
        $content[] = '<div class=comment id=result_write_name>{LNG_COUNTRY_NAME_COMMENT}</div>';
        $content[] = '</div>';
        // zone
        $content[] = '<div class=item>';
        $content[] = '<label for=write_zone>{LNG_COUNTRY_ZONE}</label>';
        $content[] = '<div class="table collapse">';
        $content[] = '<div class=td>';
        $content[] = '<span class="g-input icon-world"><select name=write_zone id=write_zone title="{LNG_PLEASE_SELECT}">';
        $content[] = '<option value=0>{LNG_PLEASE_SELECT}</option>';
        if (isset($lng['COUNTRIES_ZONE'])) {
            foreach ($lng['COUNTRIES_ZONE'] AS $i => $item) {
                $sel = $i == $country['zone'] ? 'selected' : '';
                $content[] = '<option value='.$i.' '.$sel.'>'.$item.'</option>';
            }
        }
        $content[] = '</select></span>';
        $content[] = '</div>';
        $content[] = '<div class=td>&nbsp;<a href="{URLQUERY?module=zone&src=countrywrite}" class=icon-edit title="{LNG_EDIT}"></a></div>';
        $content[] = '</div>';
        $content[] = '<div class=comment id=result_write_zone>{LNG_COUNTRY_ZONE_COMMENT}</div>';
        $content[] = '</div>';
        $content[] = '</fieldset>';
        // submit
        $content[] = '<fieldset class=submit>';
        $content[] = '<input type=submit class="button large save" value="{LNG_SAVE}">';
        $content[] = '<input type=hidden name=write_id value='.(int)$country['id'].'>';
        $content[] = '</fieldset>';
        $content[] = '</form>';
        $content[] = '</section>';
        $content[] = '<script>';
        $content[] = '$G(window).Ready(function(){';
        $content[] = 'new GForm("setup_frm", "countrywrite_save.php").onsubmit(doFormSubmit);';
        $content[] = '});';
        $content[] = '</script>';
        // หน้าปัจจุบัน
        $url_query['module'] = 'countrywrite';
    }
} else {
    $title = $lng['LNG_DATA_NOT_FOUND'];
    $content[] = '<aside class=error>'.$title.'</aside>';
}
