{* FILE: templates/CRM/LCD/customoptionvalue.tpl to add custom field for custom data set*}


{section name=rowLoop start=1 loop=12}
  {assign var=index value=$smarty.section.rowLoop.index}
  {assign var=min_deposit value=$form.min_deposit.$index.html}
  <div class="min_deposit{$index}">{$form.min_deposit.$index.html}</div>
{literal}
    <script type="text/javascript">
      cj("div.min_deposit{/literal}{$index}{literal}").insertAfter('#optionField tbody tr#optionField_{/literal}{$index}{literal} td:last');
    </script>
{/literal}
{/section}

{literal}
<script type="text/javascript">
 cj("<th class='min_deposit'>{/literal} {ts}Minimum Deposit{/ts}{literal}</th>").insertAfter('#optionField tbody tr th:last');
</script>
{/literal}  