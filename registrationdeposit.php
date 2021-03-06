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
      $fields['min_deposit_text'] = array(
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
    $form->add('text', "min_deposit_text", ts('Minimum deposit'));
    
    $numoption = CRM_Price_Form_Field::NUM_OPTION;
    for ($i = 1; $i <= $numoption; $i++) {     
      $form->add('text', "min_deposit[$i]", ts('Minimum deposit'));
    }
    if($form->getAction() == CRM_Core_Action::ADD){
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => "CRM/LCD/depositoptionvalue.tpl"
      )); 
    }
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/LCD/depositfieldvalue.tpl"
    ));
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
    $min_deposit = 0;
    $fee_blocks = $form->_values['fee'];
    foreach( $fee_blocks as $feeID=>$fee_block) {
      $html_type = CRM_Utils_Array::value('html_type', $fee_block);
      
      $feeOption = $fee_block['options'];
      foreach( $feeOption as $fee){
        $min_deposit += CRM_Utils_Array::value('min_deposit', $fee);
      }
    }
    if($min_deposit > 0 ) {
      $paymentprocessorID = $form->getVar('_paymentProcessorID');
      $form->assign('defaultPaymentprocessorID', $paymentprocessorID);
      $form->add('text', 'min_amount', ts('Deposit Amount'));
      CRM_Core_Region::instance('price-set-1')->add(array(
        'template' => "CRM/LCD/registerdeposit.tpl"
      ));
    }
  }
  if ($formName == 'CRM_Event_Form_Registration_AdditionalParticipant') {
    $params = $form->getVar('_params');
    $payment_processor_id = 0;
    foreach($params as $key=>$value){
      if(isset($value['payment_processor_id'])){
        $payment_processor_id = CRM_Utils_Array::value('payment_processor_id', $value);
      }
    }
    $min_deposit = 0;
    $fee_blocks = $form->_values['fee'];
    foreach( $fee_blocks as $fee_block) {
      $feeOption = $fee_block['options'];
      foreach( $feeOption as $fee){
         $min_deposit += CRM_Utils_Array::value('min_deposit', $fee);
      }
    }
    if($payment_processor_id > 0 && $min_deposit > 0){
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

    foreach ($params as $key => $value){
      if ($valMinAmt = CRM_Utils_Array::value('min_amount', $value)) {
        $min_amount += $valMinAmt;
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
  if ($formName == 'CRM_Event_Form_Participant') {
    $form->add('text', 'min_amount', ts('Deposit Amount'));
    CRM_Core_Region::instance('price-set-1')->add(array(
      'template' => "CRM/LCD/participantdeposit.tpl"
    ));
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
  /*Civi::log()->debug('registrationdeposit_civicrm_validateForm', [
    '$formName' => $formName,
    '$fields' => $fields,
    //'$form' => $form,
    //'$errors' => $errors,
  ]);*/

  // Form validation for Price Form Field
  if ($formName == 'CRM_Price_Form_Field' && $form->getAction() == CRM_Core_Action::ADD) {
    if( isset($fields['price']) && !empty($fields['price']) ){
      $min_deposit = $fields['min_deposit_text'];
      $amount = $fields['price'];
      if($min_deposit > $amount){
        $error_message= ts("Cannot create price field because minimum deposit is greater than amount.");
        $form->setElementError('min_deposit_text', $error_message);
      }
    }
    else {
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
  if ($formName == 'CRM_Event_Form_Registration_Register' ||
    $formName == 'CRM_Event_Form_Registration_AdditionalParticipant'
  ) {
    if ($priceSetId = CRM_Utils_Array::value('priceSetId', $fields)) {
      $min_total_amount = 0;

      try {
        $priceFields = civicrm_api3('PriceField', 'get', ['price_set_id' => $priceSetId]);
        //Civi::log()->debug('validateForm', ['$priceFields' => $priceFields]);

        foreach ($priceFields['values'] as $key => $priceField) {
          $type = CRM_Utils_Array::value('html_type', $priceField);

          if($type == 'Text'){
            $priceFieldID = 'price_' . $key;
            $quantity = CRM_Utils_Array::value($priceFieldID, $fields);
            $fieldOptions = civicrm_api3('price_field_value', 'get', [
              'price_field_id' => $key,
              'sequential' => 1,
            ]);

            if (!empty($priceOptionSet = $fieldOptions['values'][0])) {
              $min_deposit_amount = CRM_Utils_Array::value('min_deposit', $priceOptionSet);

              //use minimum deposit if set; else use full amount;
              if ($min_deposit_amount) {
                $totalAmount = $quantity * $min_deposit_amount;
                $min_total_amount += $totalAmount;
              }
            }
          }
          else{
            $priceFieldID = 'price_' . $key;
            if (!empty($priceoptionID = CRM_Utils_Array::value($priceFieldID, $fields))) {
              $fieldOptions = civicrm_api3('price_field_value', 'getsingle', [
                'id' => $priceoptionID,
                'price_field_id' => $key,
                'sequential' => 1,
              ]);
              //Civi::log()->debug('validateForm', ['$fieldOptions' => $fieldOptions]);

              if (!empty($fieldOptions)) {
                $min_deposit_amount = CRM_Utils_Array::value('min_deposit', $fieldOptions);

                //use minimum deposit if set; else use full amount;
                if (is_numeric($min_deposit_amount)) {
                  $min_total_amount += $min_deposit_amount;
                }
                else {
                  $count = CRM_Utils_Array::value('count', $fieldOptions, 1);
                  $min_total_amount += $count * $fieldOptions['amount'];
                }
              }
            }
          } 
        }
      }
      catch (CiviCRM_API3_Exception $e) {}

      $amount_entered = CRM_Utils_Array::value('min_amount', $fields);
      $config = CRM_Core_Config::singleton();
      $currencySymbol = CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_Currency', $config->defaultCurrency, 'symbol', 'name');

      //Civi::log()->debug('', array('$min_total_amount' => $min_total_amount, 'amount_entered' => $amount_entered));
      if (!empty($amount_entered) && $min_total_amount > $amount_entered) {
        $error_message = ts("The deposit amount must be equal to or more than the total minimum deposit of %1%2 for your selections.", [
          '1' => $currencySymbol,
          '2' => $min_total_amount
        ]);
        $form->setElementError('min_amount', $error_message);
      }
    }
  }
}
/**
 * Implements hook_civicrm_postProcess().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function registrationdeposit_civicrm_postProcess($formName, &$form) {
  //Civi::log()->debug('registrationdeposit_civicrm_postProcess', array('$formName' => $formName));

  if ($formName == 'CRM_Price_Form_Field') {
    $params = $form->getVar('_submitValues');

    if($form->getAction() == CRM_Core_Action::ADD){
      $id = $form->getVar('_sid');
      $field_params = array(
        'price_set_id' => $id,
      );
      $custom_field = civicrm_api3('PriceField', 'get', $field_params);
      foreach($custom_field['values'] as $key => $value){
        $priceFieldID = $key;
      }
    }
    else if($form->getAction() == CRM_Core_Action::UPDATE) {
      $priceFieldID = $form->getVar('_fid');
    }
    
    $fieldOptions = civicrm_api3('price_field_value', 'get', array(
      'price_field_id' => $priceFieldID,
      'sequential' => 1,
    ));

    if (isset($params['price']) && !empty($params['price'])){
      $min_deposit = $params['min_deposit_text'];
      if(isset($fieldOptions['values']) ){
        foreach ($fieldOptions['values'] as $key => $value) {
          $fieldValue = new CRM_Price_DAO_PriceFieldValue();
          $fieldValue->min_deposit = $min_deposit;
          $fieldValue->id = $value['id'];
          $fieldValue->save();
        }
      }
    }
    else {
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
  
  if ($formName == 'CRM_Event_Form_Registration_Confirm') {
    $params = $form->getVar('_params');
    $contribution_id = CRM_Utils_Array::value('contributionID', $params);

    //return _registrationdeposit_civicrm_updatePartialPayment($contribution_id, $params);
  }

  if ($formName == 'CRM_Event_Form_Participant') {
    $params = $form->getVar('_params');
    //Civi::log()->debug('registrationdeposit_civicrm_postProcess', array('$params' => $params));

    $participantID = $form->getVar('_id');
    $contributionId = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_ParticipantPayment',
      $participantID, 'contribution_id', 'participant_id'
    );

    return _registrationdeposit_civicrm_updatePartialPayment($contributionId, $params);
  }
}
/**
 * Implements _registrationdeposit_civicrm_updatePartialPayment().
 *
 * to alter the payment status and amount for contribution
 * with partial payment
 */
function _registrationdeposit_civicrm_updatePartialPayment($contribID, $params){
  //Civi::log()->debug('_registrationdeposit_civicrm_updatePartialPayment', array('$contribID' => $contribID, '$params' => $params));

  //short circuit if we're deleting or no min_amount to process
  if (empty($contribID) || empty($params['min_amount'])) {
    return;
  }

  $payment = civicrm_api3('Payment', 'get', array('contribution_id' => $contribID));
  //Civi::log()->debug('_registrationdeposit_civicrm_updatePartialPayment', array('$payment' => $payment));

  if (!empty($payment['values'])) {
    //Change contribution status
    $status = CRM_Core_PseudoConstant::getKey('CRM_Contribute_DAO_Contribution',
      'contribution_status_id', 'Partially paid');
    $total_amount = CRM_Utils_Array::value('amount', $params);
    $min_amount = CRM_Utils_Array::value('min_amount', $params);

    if ($min_amount && $total_amount > $min_amount) {
      $contribParams = array(
        'total_amount' => $total_amount,
        'partial_payment_total' => $total_amount,
        'partial_amount_to_pay' => $min_amount,
        'contribution_status_id' => $status,
      );
      $contribution = new CRM_Contribute_BAO_Contribution();
      $contribution->copyValues($contribParams);
      $contribution->id = $contribID;
      $result = $contribution->save();
    }
    else {
      $min_amount = $total_amount;
    }

    //update financial trxn with partial payment to is_payment=0
    $paymentID = CRM_Utils_Array::value('id', $payment);
    $finTrxnParams = $payment['values'][$paymentID];
    $finTrxnParams['net_amount'] = $min_amount;
    $finTrxnParams['total_amount'] = $min_amount;
    //Civi::log()->debug('_registrationdeposit_civicrm_updatePartialPayment', array('$finTrxnParams' => $finTrxnParams));
    
    try {
      $updatedPayment = civicrm_api3('FinancialTrxn', 'create', $finTrxnParams);
    }
    catch (CiviCRM_API3_Exception $e) {
      Civi::log()->debug('_registrationdeposit_civicrm_updatePartialPayment $e', array('$e' => $e));
    }
  }
}
/**
 * Implements hook_civicrm_alterPaymentProcessorParams().
 *
 * @param $paymentObj
 * @param $rawParams
 */
function registrationdeposit_civicrm_alterPaymentProcessorParams($paymentObj,  &$rawParams, &$cookedParams) {
  /*Civi::log()->debug('registrationdeposit_civicrm_alterPaymentProcessorParams', [
    '$paymentObj' => $paymentObj,
    '$rawParams' => $rawParams,
    '$cookedParams' => $cookedParams,
  ]);*/

  if (!empty($rawParams['min_amount'])) {
    //PayPal Standard
    if (is_a($paymentObj, 'CRM_Core_Payment_PayPalImpl')) {
      $cookedParams['amount'] = $rawParams['min_amount'];

    }
    //Authorize.net
    elseif (is_a($paymentObj, 'CRM_Core_Payment_AuthorizeNet')) {
      $cookedParams['x_amount'] = $rawParams['min_amount'];
    }
  }
}
