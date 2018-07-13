{* FILE: templates/CRM/LCD/registerdeposit.tpl to add custom field for custom data set*}
 <div id="deposit-block" class="crm-section section-depositAmount">
{if $priceSet}
    <div class="label" id="minpricelabel">{$form.min_amount.label}</div>
    <div class="content calc-value" id="minpricevalue">{$form.min_amount.html}</div>
{/if}
</div>

{literal}
<script type="text/javascript">
  CRM.$(function($) {
    $('#deposit-block').insertAfter('#pricesetTotal');
    $('#priceset .crm-section').css({'min-height': '50px'});
    var paymentID = $('input[name="payment_processor_id"]:checked').attr('value');
    if(paymentID > 0){
      $('#deposit-block').show();
    }
    else{
      $('#deposit-block').hide();
      $('input[name="min_amount"]').val('');
    }
    $('[name=payment_processor_id]').on('change.paymentBlock', function() {
      paymentProcessorID = $(this).val();
      if(paymentProcessorID > 0){
        $('#deposit-block').show();
      }
      else{
        $('#deposit-block').hide();
        $('input[name="min_amount"]').val('');
      }
    });
  });
</script>
{/literal}