<form method="post" action={"setup/toolbarlist"|ezurl}>
<h1>{'Toolbar management'|i18n( 'design/standard/toolbar' )}</h1>

<div class="objectheader">
    <h2>{'SiteAccess'|i18n( 'design/standard/setup' )}</h2>
</div>
<div class="object">
    {section show=$current_siteaccess}
        <p>{'Current siteaccess'|i18n( 'design/standard/setup' )}: <strong>{$current_siteaccess}</strong></p>
    {/section}
    <div class="block">
        <label>{'Select siteaccess'|i18n( 'design/standard/setup' )}</label><div class="labelbreak"></div>
        <select name="CurrentSiteAccess">
            {section var=siteaccess loop=$siteaccess_list}
                {section show=eq( $current_siteaccess, $siteaccess )}
                    <option value="{$siteaccess}" selected="selected">{$siteaccess}</option>
                {section-else}
                <option value="{$siteaccess}">{$siteaccess}</option>
            {/section}
        {/section}
        </select>
        &nbsp;
        <input type="submit" value="{"Set"|i18n("design/standard/setup")}" name="SelectCurrentSiteAccessButton" />
    </div>
</div>

<h2>{'Available toolbars'|i18n( 'design/standard/toolbar' )}</h2>
<table class="list" width="100%" cellspacing="0" cellpadding="0" border="0">
{section var=toolbar loop=$toolbar_list sequence=array( bglight, bgdark )}
    <tr>
        <td class="{$toolbar.sequence}">
        <a href={concat( "setup/toolbar/", $current_siteaccess, "/", $toolbar )|ezurl}>{$toolbar}</a>
        </td>
    </tr>
{/section}
</table>

</form>