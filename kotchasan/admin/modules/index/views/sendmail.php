<?php
/*
 * @filesource index/views/sendmail.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Sendmail;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\Login;
use \Kotchasan\Template;

/**
 * ฟอร์มส่งอีเมล์จากแอดมิน
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * แสดงผล
   */
  public function render($login)
  {
    // send email form
    $form = Html::create('form', array(
        'id' => 'write_frm',
        'class' => 'setup_frm',
        'action' => 'index.php/index/model/sendmail/save',
        'onsubmit' => 'doFormSubmit',
        'token' => true,
        'ajax' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Details of').' '.Language::get('Email')
    ));
    // reciever
    $reciever = self::$request->get('to')->topic();
    $fieldset->add('text', array(
      'id' => 'reciever',
      'itemClass' => 'item',
      'labelClass' => 'g-input icon-email-sent',
      'label' => Language::get('Reciever'),
      'comment' => Language::get('Recipient&#39;s Email Address Many can be found Each separated by, (comma).'),
      'autofocus',
      'value' => $reciever
    ));
    // email_from
    $datas = array($login['email'] => $login['email']);
    if (Login::isAdmin()) {
      $datas[self::$cfg->noreply_email] = self::$cfg->noreply_email;
      foreach (\Index\Sendmail\Model::findAdmin(self::$request) as $item) {
        $datas[$item] = $item;
      }
    }
    $fieldset->add('select', array(
      'id' => 'from',
      'itemClass' => 'item',
      'labelClass' => 'g-input icon-email',
      'label' => Language::get('Sender'),
      'options' => $datas
    ));
    // subject
    $fieldset->add('text', array(
      'id' => 'subject',
      'itemClass' => 'item',
      'labelClass' => 'g-input icon-edit',
      'label' => Language::get('Subject'),
      'comment' => ''.Language::get('Please fill in').' '.Language::get('Subject')
    ));
    // detail
    $fieldset->add('ckeditor', array(
      'id' => 'detail',
      'itemClass' => 'item',
      'height' => 300,
      'language' => Language::name(),
      'toolbar' => 'Email',
      'label' => Language::get('Detail'),
      'value' => Template::load('', '', 'mailtemplate')
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