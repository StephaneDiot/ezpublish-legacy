{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<form method="post" action={concat( 'package/install/', $package.name )|ezurl}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Install package'|i18n('design/admin/package')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

    <p>{'The package can be installed on your system. Installing the package will copy files, create content classes etc., depending on the package.
If you do not want to install the package at this time, you can do so later on the view page for the package.'|i18n('design/admin/package')|break}</p>

    <h3>{'Install items'|i18n('design/admin/package')|break}</h3>
    <ul>
    {section var=install loop=$install_elements}
        <li>{$install.description|wash}</li>
    {/section}
    </ul>

</div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
    <div class="block">
        <input class="button" type="submit" name="InstallPackageButton" value="{'Install package'|i18n('design/admin/package')}" />
        <input class="button" type="submit" name="SkipPackageButton" value="{'Skip installation'|i18n('design/admin/package')}" />
    </div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>

</form>
