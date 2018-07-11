{* FILE: templates/CRM/LCD/registerdeposit.tpl to add custom field for custom data set*}
 <div id="deposit-block" class="crm-section section-depositAmount">
{if $priceSet}
    <div class="label" id="minpricelabel">{$form.min_amount.label}</div>
    <div class="content calc-value" id="minpricevalue">{$form.min_amount.html}</div>
{/if}
</div>
  
<script type="text/javascript">
{literal}
  cj('#deposit-block').insertAfter('#pricesetTotal');
  cj('#priceset .crm-section').css({'min-height': '50px'});
{/literal}
</script>
