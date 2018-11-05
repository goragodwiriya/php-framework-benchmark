<?php
// admin/accesskey.php
if (MAIN_INIT == 'admin' && $isMember) {
    $title = $lng['LNG_ACCESSKEY_TITLE'];
    $content[] = '<div class=breadcrumbs><ul><li><span class=icon-help>{LNG_HELP}</span></li></ul></div>';
    $content[] = '<section>';
    $content[] = '<header><h1 class=icon-keyboard>'.$title.'</h1></header>';
    $content[] = '<table class="summary fullwidth">';
    $content[] = '<caption>{LNG_ACCESSKEY_COMMENT}</caption>';
    $content[] = '<colgroup></colgroup>';
    $content[] = '<colgroup></colgroup>';
    $content[] = '<thead>';
    $content[] = '<tr><th scope=col>{LNG_ACCESSKEY}</th><th scope=col>{LNG_DESCRIPTION}</th></tr>';
    $content[] = '</thead>';
    $content[] = '<tbody>';
    $content[] = '<tr><td>0</td><td>{LNG_ACCESSKEY_TITLE}</td></tr>';
    $content[] = '<tr class=bg2><td>1 - 9</td><td>{LNG_MENUS}</td></tr>';
    $content[] = '<tr><td>a</td><td>{LNG_AUTHOR_PAGE}</td></tr>';
    $content[] = '<tr class=bg2><td>c</td><td>{LNG_CANCLE}</td></tr>';
    $content[] = '<tr><td>b</td><td>{LNG_SCROLL_TO_BOTTOM}</td></tr>';
    $content[] = '<tr class=bg2><td>d</td><td>{LNG_CHANGE_DISPLAY}</td></tr>';
    $content[] = '<tr><td>f</td><td>{LNG_FORGOT_TITLE}</td></tr>';
    $content[] = '<tr class=bg2><td>h</td><td>{LNG_HOME}</td></tr>';
    $content[] = '<tr><td>l</td><td>{LNG_LOGOUT} ({LNG_ADMIN_LOGIN})</td></tr>';
    $content[] = '<tr class=bg2><td>n</td><td>{LNG_CHANGE_LANGUAGE}</td></tr>';
    $content[] = '<tr><td>p</td><td>{LNG_PREVIEW}</td></tr>';
    $content[] = '<tr class=bg2><td>s</td><td>{LNG_SKIP_TO_CONTENT}</td></tr>';
    $content[] = '<tr><td>t</td><td>{LNG_SCROLL_TO_TOP}</td></tr>';
    $content[] = '<tr class=bg2><td>w</td><td>{LNG_MEMBER_EDIT_TITLE}</td></tr>';
    $content[] = '</tbody>';
    $content[] = '</table>';
    $content[] = '</section>';
}
