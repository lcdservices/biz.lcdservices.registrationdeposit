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
