<?php
/*
 * @filesource Widgets/Contact/Views/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Contact\Views;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\Login;

/**
 * ฟอร์มส่งอีเมล์ถึงแอดมิน
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\View
{

  /**
   * ฟอร์มส่งจดหมายหาแอดมิน
   *
   * @param array $emails รายชื่อผู้รับ
   * @return string
   */
  public static function render($emails)
  {
    // send email form
    $form = Html::create('form', array(
        'id' => 'write_frm',
        'class' => 'setup_frm',
        'action' => 'xhr.php/Widgets/Contact/Models/Index/send',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true
    ));
    $fieldset = $form->add('fieldset');
    // reciever
    $reciever = self::$request->get('to')->topic();
    $fieldset->add('select', array(
      'id' => 'mail_reciever',
      'itemClass' => 'item',
      'labelClass' => 'g-input icon-email-sent',
      'label' => Language::get('Reciever'),
      'options' => $emails
    ));
    // sender
    $login = Login::isAdmin();
    $fieldset->add('text', array(
      'id' => 'mail_sender',
      'itemClass' => 'item',
      'labelClass' => 'g-input icon-email',
      'label' => Language::get('Sender'),
      'value' => $login ? $login['email'] : '',
      'placeholder' => ''.Language::get('Please fill in').' '.Language::get('Sender')
    ));
    // subject
    $fieldset->add('text', array(
      'id' => 'mail_subject',
      'itemClass' => 'item',
      'labelClass' => 'g-input icon-edit',
      'label' => Language::get('Subject'),
      'placeholder' => ''.Language::get('Please fill in').' '.Language::get('Subject')
    ));
    // detail
    $fieldset->add('textarea', array(
      'id' => 'mail_detail',
      'itemClass' => 'item',
      'labelClass' => 'g-input icon-file',
      'label' => Language::get('Detail'),
      'rows' => 10,
      'placeholder' => ''.Language::get('Please fill in').' '.Language::get('Detail')
    ));
    // antispam
    $fieldset->add('antispam', array(
      'id' => 'mail_antispam',
      'itemClass' => 'item',
      'labelClass' => 'g-input',
      'placeholder' => Language::get('Please enter the characters you see in the box'),
      'maxlength' => 4
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => Language::get('Send message')
    ));
    return $form->render();
  }
}