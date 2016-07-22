<?php
/*
 * @filesource document/models/settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Settings;

use \Gcms\Login;
use \Kotchasan\Language;
use \Kotchasan\Validator;
use \Kotchasan\Url;

/**
 *  Model สำหรับอ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * บันทึกข้อมูล config ของโมดูล
   */
  public function save()
  {
    $ret = array();
    // referer, session, member
    if ($this->request->isReferer() && $this->request->initSession() && Login::isMember()) {
      if ($_SESSION['login']->email == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ $_POST
        $save = $this->request->filter($_POST);
        $files = $this->request->files();
        // save
        $model = new static;
        $index = $model->db()->first($model->tableWithPrefix('modules'), $save['id']);
        if ($index) {
          $config = @unserialize($index->config);
          unset($save['id']);
          $save['default_icon'] = $config['default_icon'];
          foreach ($this->request->files() as $item => $file) {
            if ($file['tmp_name'] != '') {
              $icon = DATA_FOLDER.'document/default-'.$index->id;
              // ตรวจสอบไฟล์อัปโหลด
              $info = Validator::isImage(array('jpg', 'gif', 'png'), $file);
              if (!$info) {
                $ret['ret_'.$item] = Language::get('The type of file is invalid');
                $input = !$input ? $item : $input;
              } elseif (@move_uploaded_file($file['tmp_name'], ROOT_PATH."$icon.$info[ext]")) {
                $ret['ret_'.$item] = '';
                $save['default_icon'] = "$icon.$info[ext]";
              } else {
                // ไม่สามารถอัปโหลดได้
                $ret['ret_'.$item] = Language::get('Can not upload files');
                $input = !$input ? $item : $input;
              }
            }
          }
          $save['new_date'] = $save['new_date'] * 86400;
          $save['can_view'][] = 1;
          $save['can_write'][] = 1;
          $save['can_config'][] = 1;
          $model->db()->update($model->tableWithPrefix('modules'), $index->id, array('config' => serialize($save)));
          // คืนค่า
          $ret['alert'] = Language::get('Saved successfully');
          $ret['location'] = Url::postBack('index.php', array('module' => 'document-settings', 'id' => $index->id));
        } else {
          // not found
          $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}