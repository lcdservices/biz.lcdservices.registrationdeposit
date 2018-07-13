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
 * Implements hook_civicrm_entityTypes().
 */
function registrationdeposit_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Price_DAO_PriceFieldValue']['fields_callback'][]
    = function ($class, &$fields) {
      $fields['min_deposit'] = array(
        'name' => 'min_deposit',
        'type' => CRM_Utils_Type::T_MONEY,
        'title' => ts('Minimum deposit'),
        'description' => 'Minimum deposit for option amount',
        'precision' => [
          18,
          9
        ],
        'table_name' => 'civicrm_price_field_value',
        'entity' => 'PriceFieldValue',
        'bao' => 'CRM_Price_BAO_PriceFieldValue',
        'localizable' => 0,
        'html' => [
          'type' => 'Text',
        ],
      );
    };
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function registrationdeposit_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Price_Form_Field' &&
    ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE)
  ) {
    $numoption = CRM_Price_Form_Field::NUM_OPTION;
    for ($i = 1; $i <= $numoption; $i++) {     
      $form->add('text', "min_deposit[$i]", ts('Minimum deposit'));
    }

    if($form->getAction() == CRM_Core_Action::ADD){
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => "CRM/LCD/depositoptionvalue.tpl"
      )); 
    }
  }

  if ($formName == 'CRM_Price_Form_Option' &&
    ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE)
  ) {
    $form->add('text', 'min_deposit', ts('Minimum deposit'));
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/LCD/depositoption.tpl"
    ));
  }

  if ($formName == 'CRM_Event_Form_Registration_Register') {
    $form->add('text', 'min_amount', ts('Deposit Amount'));
    CRM_Core_Region::instance('price-set-1')->add(array(
      'template' => "CRM/LCD/registerdeposit.tpl"
    ));
  }
  if ($formName == 'CRM_Event_Form_Registration_AdditionalParticipant') {
    $params = $form->getVar('_params');
    $payment_processor_id = 0;
    foreach($params as $key=>$value){
      if(isset($value['payment_processor_id'])){
        $payment_processor_id = CRM_Utils_Array::value('payment_processor_id', $value);
      }
    }
    if($payment_processor_id > 0){
      $form->add('text', 'min_amount', ts('Deposit Amount'));
      CRM_Core_Region::instance('price-set-1')->add(array(
        'template' => "CRM/LCD/participantregisterdeposit.tpl"
      ));
    }    
  }

  if ($formName == 'CRM_Event_Form_Registration_Confirm' ||
    $formName == 'CRM_Event_Form_Registration_ThankYou'
  ) {
    $params = $form->getVar('_params');
    $min_amount = 0;
    
    foreach($params as $key=>$value){
      if(isset($value['min_amount'])){
        $min_amount += CRM_Utils_Array::value('min_amount', $value);
      }
    }

    if($min_amount > 0){
      $amountformat = CRM_Utils_Money::format($min_amount);
      $form->assign('min_amount', $amountformat);
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => "CRM/LCD/confirm.tpl"
      ));
    }
  }
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @param string $formName
 * @param array $fields
 * @param array $files
 * @param CRM_Core_Form $form
 * @param array $errors
 */
function registrationdeposit_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  //Civi::log()->debug('registrationdeposit_civicrm_validateForm', array('fields' => $fields));

  // Form validation for Price Form Field
  if ($formName == 'CRM_Price_Form_Field' && $form->getAction() == CRM_Core_Action::ADD) {
    $numoption = CRM_Price_Form_Field::NUM_OPTION;
      
    for ($i = 1; $i <= $numoption; $i++) { 
      $min_deposit = CRM_Utils_Array::value($i, $fields['min_deposit']);
      $option_amount = CRM_Utils_Array::value($i, $fields['option_amount']);
      $min_depositID = "option_amount[$i]";
      if($min_deposit > $option_amount){
        $error_message= ts("Cannot create price option because minimum deposit is greater than amount.", array(
          '1' => $min_depositID,
        ));
        $form->setElementError($min_depositID, $error_message);
      }
    }        
  }

  // Form validation for Price fields for a option Data Set
  if ($formName == 'CRM_Price_Form_Option' &&
    ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE)
  ) {
    $option_amount = CRM_Utils_Array::value('amount', $fields);
    $min_deposit = CRM_Utils_Array::value('min_deposit', $fields);
    
    if($min_deposit > $option_amount){
      $error_message= ts("Cannot create price option because minimum deposit is greater than amount.", array(
        '1' => $option_amount,
      ));
      $form->setElementError('amount', $error_message);
    }
  }
  
  // Form validation for Registration fields for price option
  if ($formName == 'CRM_Event_Form_Registration_Register' || $formName == 'CRM_Event_Form_Registration_AdditionalParticipant') {
    if ($priceSetId = CRM_Utils_Array::value('priceSetId', $fields)) {
      try {
        $min_total_amount = 0;
        $field_params = [
          'price_set_id' => $priceSetId,
        ];
        $priceFields = civicrm_api3('PriceField', 'get', $field_params);

        foreach ($priceFields['values'] as $key => $value) {
          $priceFieldID = 'price_' . $key;
          if (!empty($priceoptionID = CRM_Utils_Array::value($priceFieldID, $fields))) {
            $fieldOptions = civicrm_api3('price_field_value', 'get', [
              'id' => $priceoptionID,
              'price_field_id' => $key,
              'sequential' => 1,
            ]);

            if (!empty($priceOptionSet = $fieldOptions['values'][0])) {
              //Civi::log()->debug('', array('priceOptionSet' => $priceOptionSet));
              $min_deposit_amount = CRM_Utils_Array::value('min_deposit', $priceOptionSet);

              //use minimum deposit if set; else use full amount;
              if (is_numeric($min_deposit_amount)) {
                $min_total_amount += $min_deposit_amount;
              }
              else {
                $count = CRM_Utils_Array::value('count', $priceOptionSet, 1);
                $min_total_amount += $count * $priceOptionSet['amount'];
              }
            }
          }
        }
      }
      catch (CiviCRM_API3_Exception $e) {}

      $payment_processor_id = CRM_Utils_Array::value('payment_processor_id', $fields);
      $amount_entered = CRM_Utils_Array::value('min_amount', $fields);
      $config = CRM_Core_Config::singleton();
      $currencySymbol = CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_Currency', $config->defaultCurrency, 'symbol', 'name');

      //Civi::log()->debug('', array('min_total_maount' => $min_total_amount, 'amount_entered' => $amount_entered));
      if ( !empty($amount_entered) && $min_total_amount > $amount_entered) {
        $error_message = ts("The deposit amount must be equal to or more than the total minimum deposit of %1%2 for your selections.", [
          '1' => $currencySymbol,
          '2' => $min_total_amount
        ]);
        $form->setElementError('min_amount', $error_message);
      }
    }
  }
  return;
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function registrationdeposit_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Price_Form_Field' &&
    ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE)
  ) {
    $params = $form->getVar('_submitValues');    
    $id = $form->getVar('_sid');
    $field_params = array(
      'price_set_id' => $id,
    );
    $custom_field = civicrm_api3('PriceField', 'get', $field_params);
    foreach($custom_field['values'] as $key=>$value){
      $priceFieldID = $key;
    }

    $fieldOptions = civicrm_api3('price_field_value', 'get', array(
      'price_field_id' => $priceFieldID,
      'sequential' => 1,
    ));

    if(isset($fieldOptions['values']) ){
      foreach ($fieldOptions['values'] as $key => $value) {
        $option_label = $value['label'];
        $option_key = array_search($option_label, $params['option_label']);
        $option_deposit = CRM_Utils_Array::value($option_key, $params['min_deposit'], FALSE);
        $fieldValue = new CRM_Price_DAO_PriceFieldValue();
        $fieldValue->min_deposit = $option_deposit;
        $fieldValue->id = $value['id'];
        $fieldValue->save();
      }
    }          
  }
}
