{* FILE: templates/CRM/LCD/registerdeposit.tpl to add custom field for custom data set*}
 <div id="deposit-block">
{if $priceSet}
  <div id="amount_information">
    <fieldset class="deposit-group deposit_info-group">
      <legend>
        Deposit
      </legend>
       <div class="crm-section deposit_amount-section">
        <div id="min_amount_value" class="crm-section">
          <div class="label" id="minpricelabel">{$form.min_amount.label}</div>
          <div class="content calc-value" id="minpricevalue">{$form.min_amount.html}</div>
        </div>
      </div>
    </fieldset>
  </div>
{/if}
</div>
  
<script type="text/javascript">
{literal}
  cj('#deposit-block').insertAfter('.payment_options-group');
  var paymentID = cj('input[name="payment_processor_id"]').attr('value');
  if(paymentID > 0){
    cj('#deposit-block').show();
  }
  else{
    cj('#deposit-block').hide();
  }
  cj('input[name="payment_processor_id"]').on('change.paymentBlock', function() {
    var paymentID = cj(this).attr('value');
    if(paymentID > 0){
      cj('#deposit-block').show();
    }
    else{
      cj('#deposit-block').hide();
    }
  });
{/literal}
</script>

<script type="text/javascript">
{literal}

{/literal}
{foreach key=key item=item from=$minDepositData}
{literal}
var minimum_deposit = '{/literal}{$item}{literal}';
var prcieID = '{/literal}{$key}{literal}';
  cj("#priceset [price]").each(function () {
    var elementType =  cj(this).attr('type');
    if( this.tagName == 'SELECT' ){
      cj("#priceset select option").each(function(){
        var optionID =  cj(this).attr('value');
        if(optionID == prcieID){
          cj(this).attr('minimum_deposit', minimum_deposit);
        }
      });
    }
    else{
      var optionID = cj(this).attr('value');
      if(optionID == prcieID){
        cj(this).attr('minimum_deposit', minimum_deposit);
      }
    }
  });
 {/literal} 
{/foreach}
{literal}
    
var thousandMarker = '{/literal}{$config->monetaryThousandSeparator}{literal}';
var separator      = '{/literal}{$config->monetaryDecimalPoint}{literal}';
var symbol         = '{/literal}{$currencySymbol}{literal}';
var optionSep      = ',';

cj("#priceset [price]").each(function () {
  var totalFee = 0;
    var elementType =  cj(this).attr('type');
    if ( this.tagName == 'SELECT' ) {
      elementType = 'select-one';
    }

    switch(elementType) {
      case 'checkbox':
        //event driven calculation of element.
        cj(this).click(function(){
          calculateCheckboxLineItemMinValue(this);
          var total = calculateTotalAmount();
          cj('input[name = "TotalAmount"]').val( total );
          displayMin(calculateMinTotalFee());
        });
        calculateCheckboxLineItemMinValue(this);
      break;

    case 'radio':
      //event driven calculation of element.
      cj(this).click( function(){
        calculateRadioLineItemMinValue(this);
        var total = calculateTotalAmount();
        cj('input[name = "TotalAmount"]').val( total );
        displayMin(calculateMinTotalFee());
      });
      calculateRadioLineItemMinValue(this);
      break;
  case 'select-one':
    calculateSelectLineItemValue(this);
    //event driven calculation of element.
    cj(this).change( function() {
      calculateSelectLineItemValue(this);
      var total = calculateTotalAmount();
      cj('input[name = "TotalAmount"]').val( total );
      displayMin(calculateMinTotalFee());
    });


    break;

  }
  displayMin(calculateMinTotalFee());
});

/**
 * Calculate the value of the line item for a radio value.
 */
function calculateCheckboxLineItemMinValue(priceElement) {
  var minimum_deposit = cj(priceElement).attr('minimum_deposit');
  if(cj.isNumeric(minimum_deposit)){
    price = parseFloat(0);
    var lineTotal = parseFloat(minimum_deposit);
    if (cj(priceElement).prop('checked')) {
      price = parseFloat(minimum_deposit);
    }    
  }
  else{
    price = parseFloat(0);
  }
  cj(priceElement).data('line_raw_min_total', price);
}

/**
 * Calculate the value of the line item for a radio value.
 */
function calculateRadioLineItemMinValue(priceElement) {
  var minimum_deposit = cj(priceElement).attr('minimum_deposit');
  if(cj.isNumeric(minimum_deposit)){
    var lineTotal = parseFloat(minimum_deposit);
    cj(priceElement).data('line_raw_min_total', lineTotal);
    var radionGroupName = cj(priceElement).attr("name");
    // Reset all unchecked options to having a data value of 0.
    cj('input[name=' + radionGroupName + ']:radio:unchecked').each(
      function () {
        cj(this).data('line_raw_min_total', 0);
      }
    );
  }
  else{
    cj(this).data('line_raw_min_total', 0);
  }
}

/**
 * Calculate the value of the line item for a select value.
 */
function calculateSelectLineItemValue(priceElement) {
  var minimum_deposit = cj('option:selected', priceElement).attr('minimum_deposit');
  var price = parseFloat('0');
  var option = cj(priceElement).val();
  if (option) {
    price = parseFloat(minimum_deposit);
  }
  cj(priceElement).data('line_raw_min_total', price);
}

/**
 * Calculate the total fee for the visible priceset.
 */
function calculateMinTotalFee() {
  var totalFee = 0;
  cj("#priceset [price]").each(function () {
    totalFee = totalFee + cj(this).data('line_raw_min_total');
  });

  return totalFee;
}

/**
 * Display calculated amount.
 */
function displayMin(totalfee) {
  // totalfee is monetary, round it to 2 decimal points so it can
  // go as a float - CRM-13491
  totalfee = Math.round(totalfee*100)/100;
  var totalEventFee  = formatMoney( totalfee, 2, separator, thousandMarker);

  cj('input[name = "min_deposit_amount"]').val(totalfee);
  cj('#pricevalue').data('raw-total', totalfee).trigger('change');

  (totalfee < 0) ? cj('table#pricelabel').addClass('disabled') : cj('table#pricelabel').removeClass('disabled');
  if (typeof skipPaymentMethod == 'function') {
    // Advice to anyone who, like me, feels hatred towards this if construct ... if you remove the if you
    // get an error on participant 2 of a event that requires approval & permits multiple registrants.
    skipPaymentMethod();
  }
}

//money formatting/localization
function formatMoney (amount, c, d, t) {
var n = amount,
    c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "," : d,
    t = t == undefined ? "." : t, s = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

/**
 * Calculate the total fee for the visible priceset.
 */
function calculateTotalAmount() {
  var totalFee = 0;
  cj("#priceset [price]").each(function () {
    totalFee = totalFee + cj(this).data('line_raw_total');
  });
  return totalFee;
}

{/literal}
</script>
