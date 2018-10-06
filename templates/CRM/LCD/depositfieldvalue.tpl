{* FILE: templates/CRM/LCD/customoptionvalue.tpl to add custom field for custom data set*}

  <div id="DIVlabel">{$form.min_deposit_text.label}</div>
  <div id="DIVinput">{$form.min_deposit_text.html}</div>

{literal}
<script type="text/javascript">
 cj("<tr class='crm-custom_option-form-block-min_deposit_text'><td class='label'><div class='labeldiv'></div></td><td class='input'><div class='labelinput'></td></tr>").insertAfter('div#price-block .crm-price-field-form-block-price');
 
 var $label = cj('tr.crm-custom_option-form-block-min_deposit_text').find('.labeldiv'); 
 var $input = cj('tr.crm-custom_option-form-block-min_deposit_text').find('.labelinput'); 
 cj('div#DIVlabel').insertAfter($label);
 cj('div#DIVinput').insertAfter($input);
 </script>
{/literal}  