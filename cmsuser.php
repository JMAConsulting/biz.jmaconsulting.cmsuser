<?php

require_once 'cmsuser.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function cmsuser_civicrm_config(&$config) {
  _cmsuser_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function cmsuser_civicrm_install() {
  checkCMS();
  _cmsuser_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function cmsuser_civicrm_enable() {
  _cmsuser_civix_civicrm_enable();
}

/**
 * Function to ensure extension is used only in Drupal.
 *
 */
function checkCMS() {
  if (CRM_Core_Config::singleton()->userFramework == "Joomla") {
    CRM_Core_Error::fatal(ts("This extension currently does not support Joomla CMS."));
  }
}

/**
 * Implementation of hook_civicrm_pre
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_pre
 */
function cmsuser_civicrm_pre($op, $objectName, $id, &$params) {
  if ($op == "create" && $objectName == "Profile" && ($cid = CRM_Utils_Request::retrieve('crmId', 'Integer'))) {
    $cms = CRM_Core_Config::singleton()->userFramework;
    switch ($cms) {
      case "Drupal":
        // Set contact ID so that new contact is not created by CiviCRM.
        $params['contact_id'] = $cid;
        break;
      case "WordPress":
        // FIXME
      case "Joomla":
        // FIXME
        break;
    default:
      break;
    }
  }
}

/**
 * Implementation of hook_civicrm_pageRun
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_pageRun
 */
function cmsuser_civicrm_pageRun(&$page) {
  if ($page->getVar('_name') == "CRM_Contact_Page_View_Summary") {
    $cms = CRM_Core_Config::singleton()->userFramework;
    switch ($cms) {
      case "Drupal":
        $cid = CRM_Core_Session::singleton()->get('view.id');
        $uid = CRM_Core_BAO_UFMatch::getUFId($cid);
        if ($uid) {
          $userRecordUrl = CRM_Core_Config::singleton()->userSystem->getUserRecordUrl($cid);
          $user = user_load($uid);
          $username = $user->name;
          $page->assign('cmsUser', $username);
        }
        else {
          $userRecordUrl = CRM_Utils_System::url("admin/people/create", array('crmId' => $cid));
          $page->assign('cmsUser', "Create User");
        }
        $page->assign('cmsURL', $userRecordUrl);
        CRM_Core_Region::instance('page-body')->add(array(
          'template' => 'CMSUser.tpl',
        ));
        break;
      case "WordPress":
        $cid = CRM_Core_Session::singleton()->get('view.id');
        $uid = CRM_Core_BAO_UFMatch::getUFId($cid);
        if ($uid) {
          $userRecordUrl = CRM_Core_Config::singleton()->userSystem->getUserRecordUrl($cid);
          $user = get_userdata($uid);
          $username = $user->data->display_name;
          $page->assign('cmsUser', $username);
        }
        else {
          $userRecordUrl = CRM_Utils_System::url("civicrm/contact/view/useradd", array('cid' => $cid, 'reset' => 1, 'action' => 'add'));
          $page->assign('cmsUser', "Create User");
        }
        $page->assign('cmsURL', $userRecordUrl);
        CRM_Core_Region::instance('page-body')->add(array(
          'template' => 'CMSUser.tpl',
        ));
        break;
      case "Joomla":
        // FIXME
        break;
    default:
      break;
    }
  }
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function cmsuser_civicrm_buildForm($formName, &$form) {
  if ($formName == "CRM_Profile_Form_Dynamic" && ($cid = CRM_Utils_Request::retrieve('crmId', 'Integer'))) {
    $cms = CRM_Core_Config::singleton()->userFramework;
    switch ($cms) {
      case "Drupal":
        $result = civicrm_api3('Contact', 'get', array(
          'sequential' => 1,
          'return' => "first_name,last_name,email",
          'id' => $cid,
        ));
        if ($result['count'] > 0) {
          $defaults = array(
            'first_name' => $result['values'][0]['first_name'],
            'last_name' => $result['values'][0]['last_name'],
          );
          $form->setDefaults($defaults);
        }

        $url = CRM_Core_Config::singleton()->extensionsURL . DIRECTORY_SEPARATOR . basename(__DIR__) . "/templates/res/js/cms.js";
        drupal_add_js($url, 'external');
        break;
      case "WordPress":
        // FIXME
      case "Joomla":
        // FIXME
        break;
    default:
      break;
    }
  }
}
