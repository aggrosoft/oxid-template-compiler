[{$smarty.block.parent}]
<form name="compile" id="compile" action="[{$oViewConf->getSelfLink()}]" method="post">
    <p>
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="theme_main">
        <input type="hidden" name="fnc" value="compiletheme">
        <input type="hidden" name="oxid" value="[{$oTheme->getInfo('id')}]">
        <input type="submit" value="[{oxmultilang ident="THEME_COMPILE"}]">
    </p>
</form>
<form name="compilejs" id="compilejs" action="[{$oViewConf->getSelfLink()}]" method="post">
    <p>
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="theme_main">
        <input type="hidden" name="fnc" value="compilescripts">
        <input type="hidden" name="oxid" value="[{$oTheme->getInfo('id')}]">
        <input type="submit" value="[{oxmultilang ident="THEME_COMPILE_SCRIPTS"}]">
    </p>
</form>
<form name="compile" id="compile" action="[{$oViewConf->getSelfLink()}]" method="post">
    <p>
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="theme_main">
        <input type="hidden" name="fnc" value="initializetheme">
        <input type="hidden" name="oxid" value="[{$oTheme->getInfo('id')}]">
        <input type="submit" value="[{oxmultilang ident="THEME_INITIALIZE"}]">
    </p>
</form>