<form action={'/shop/statistics'|ezurl} method="post" name="Statistics">

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Product statistics [%count]'|i18n( 'design/admin/shop/orderstatistics',, hash( '%count', $statistic_result[0].product_list|count  ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{section show=$statistic_result}
<table class="list" cellspacing="0">
<tr>
	<th class="wide">{'Product'|i18n( 'design/admin/shop/orderstatistics' )}</th>
	<th class="tight">{'Quantity'|i18n( 'design/admin/shop/orderstatistics' )}</th>
	<th class="tight">{'Total (ex. VAT)'|i18n( 'design/admin/shop/orderstatistics' )}</th>
	<th class="tight">{'Total (inc. VAT)'|i18n( 'design/admin/shop/orderstatistics' )}</th>
</tr>
{section var=Products loop=$statistic_result[0].product_list sequence=array( bglight, bgdark )}
<tr class="{$Products.sequence}">
	<td>{node_view_gui view=line content_node=$Products.product.main_node}</td>
    <td>{$Products.sum_count}</td>
	<td>{$Products.sum_ex_vat|l10n(currency)}</td>
	<td>{$Products.sum_inc_vat|l10n(currency)}</td>
</tr>
{/section}
<tr>
	<td><b>{'SUM:'|i18n( 'design/admin/shop/orderstatistics' )}</b></td>
    <td>&nbsp;</td>
	<td><b>{$statistic_result[0].total_sum_ex_vat|l10n(currency)}</b></td>
	<td><b>{$statistic_result[0].total_sum_inc_vat|l10n(currency)}</b></td>
</tr>
</table>
{/section}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">

<select name="Year">
    <option value="0" {section show=eq($year,0)}selected="selected"{/section}>[{'All years'|i18n( 'design/admin/shop/orderstatistics' )}]</option>
    {section var=YearValue loop=$year_list}
        <option value="{$YearValue}" {section show=eq($YearValue,$year)}selected="selected"{/section}>{$YearValue}</option>
    {/section}
</select>

<select name="Month">
    <option value="0" {section show=eq($month,0)}selected="selected"{/section}>[{'All months'|i18n( 'design/admin/shop/orderstatistics' )}]</option>
    {section var=MonthValue loop=$month_list}
        <option value="{$MonthValue}" {section show=eq($MonthValue,$month)}selected="selected"{/section}>{$MonthValue}</option>
    {/section}
</select>

<input class="button" type="submit" name="View" value="{'Show'|i18n( 'design/admin/shop/orderstatistics' )}" />

</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>

</form>
