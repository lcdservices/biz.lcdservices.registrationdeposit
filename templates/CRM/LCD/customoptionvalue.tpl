{* FILE: templates/CRM/LCD/customoptionvalue.tpl to add custom field for custom data set*}


{section name=rowLoop start=1 loop=12}
  {assign var=index value=$smarty.section.rowLoop.index}
  {assign var=max_deposit value=$form.max_deposit.$index.html}
  <div class="max_deposit{$index}">{$form.max_deposit.$index.html}</div>
{literal}
    <script type="text/javascript">
      cj("div.max_deposit{/literal}{$index}{literal}").insertAfter('#optionField tbody tr#optionField_{/literal}{$index}{literal} td:last');
    </script>
{/literal}
{/section}

{literal}
<script type="text/javascript">
 cj("<th class='max_deposit'>{/literal} {ts}Maximum Deposit{/ts}{literal}</th>").insertAfter('#optionField tbody tr th:last');
</script>
{/literal}  