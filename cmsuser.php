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
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function cmsuser_civicrm_xmlMenu(&$files) {
  _cmsuser_civix_civicrm_xmlMenu($files);
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
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function cmsuser_civicrm_uninstall() {
  _cmsuser_civix_civicrm_uninstall();
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
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function cmsuser_civicrm_disable() {
  _cmsuser_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function cmsuser_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _cmsuser_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function cmsuser_civicrm_managed(&$entities) {
  _cmsuser_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function cmsuser_civicrm_caseTypes(&$caseTypes) {
  _cmsuser_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function cmsuser_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _cmsuser_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Function to ensure extension is used only in Drupal.
 *
 */
function checkCMS() {
  if (CRM_Core_Config::singleton()->userFramework != "Drupal") {
    CRM_Core_Error::fatal(ts("This extension currently only supports Drupal CMS."));
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