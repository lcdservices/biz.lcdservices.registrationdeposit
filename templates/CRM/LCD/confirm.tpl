{* FILE: templates/CRM/LCD/confirm.tpl to add custom field for custom data set*}
<div class="crm-section no-label deposit_amount-section">
  <div class="content bold">Deposit Amount:$ {$min_amount}</div>
  <div class="clear"></div>
</div>


<script type="text/javascript">
  cj('.deposit_amount-section').insertAfter('div.total_amount-section');
</script>