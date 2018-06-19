<?php

/**
 * @file
 * Add a table of notes from related contacts.
 *
 * Copyright (C) 2013-15, AGH Strategies, LLC <info@aghstrategies.com>
 * Licensed under the GNU Affero Public License 3.0 (see LICENSE.txt)
 */

require_once 'registrationdeposit.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function registrationdeposit_civicrm_config(&$config) {
  _registrationdeposit_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function registrationdeposit_civicrm_xmlMenu(&$files) {
  _registrationdeposit_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function registrationdeposit_civicrm_install() {
  return _registrationdeposit_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function registrationdeposit_civicrm_uninstall() {
  return _registrationdeposit_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function registrationdeposit_civicrm_enable() {
  return _registrationdeposit_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function registrationdeposit_civicrm_disable() {
  return _registrationdeposit_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function registrationdeposit_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _registrationdeposit_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function registrationdeposit_civicrm_managed(&$entities) {
  return _registrationdeposit_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function registrationdeposit_civicrm_buildForm($formName, &$form) {
  if( $formName == 'CRM_Price_Form_Field' && ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE) ) {
    $numoption = CRM_Price_Form_Field::NUM_OPTION;
    for ($i = 1; $i <= $numoption; $i++) {     
      $form->add('text', "max_deposit[$i]", ts('Maximum deposit'));
    }
    if($form->getAction() == CRM_Core_Action::ADD){
    }
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/LCD/customoptionvalue.tpl"
    ));   
  }
  if( $formName == 'CRM_Price_Form_Option' && ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE)) {
    $form->add('text', 'max_deposit', ts('Maximum deposit'));
    if($form->getAction() == CRM_Core_Action::ADD){
    }
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/LCD/customoption.tpl"
    ));
  }
}
