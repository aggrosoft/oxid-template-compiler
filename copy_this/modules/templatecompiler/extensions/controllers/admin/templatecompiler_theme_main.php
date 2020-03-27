<?php

use ScssPhp\ScssPhp\Compiler;

class templatecompiler_theme_main extends templatecompiler_theme_main_parent {

    public function compiletheme() {
        $sTheme = $this->getEditObjectId();

        /** @var \OxidEsales\Eshop\Core\Theme $oTheme */
        $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
        if (!$oTheme->load($sTheme)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_THEME_NOT_LOADED'));
            return;
        }

        $sScssPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getViewsDir() . $sTheme . "/build/scss/";
        $sFilePath = $sScssPath . "style.scss";

        $sParent = $oTheme->getInfo('parentTheme');
        if ($sParent) {
            $sParentScssPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getViewsDir() . $sParent . "/build/scss/";
            $sParentFilePath = $sParentScssPath . "style.scss";
        }

        $blHasThemeStyleScss = file_exists($sFilePath);
        $blHasParentStyleScss = $sParent ? file_exists($sParentFilePath) : false;

        if ($blHasThemeStyleScss || $blHasParentStyleScss) {
            $scss = new Compiler();
            $scss->setImportPaths($sParent ? [$sScssPath, $sParentScssPath] : $sScssPath);
            $scss->setFormatter(\ScssPhp\ScssPhp\Formatter\Crunched::class);

            try{
                $css = $scss->compile(file_get_contents($blHasThemeStyleScss ? $sFilePath : $sParentFilePath));
            } catch (\Exception $e) {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($e->getMessage());
                return;
            }


            $sOutPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getOutDir() . $sTheme . "/src/css/";
            $sOutFile = $sOutPath . 'styles.min.css';

            file_put_contents($sOutFile, $css);

        }
    }

    public function initializetheme () {
        $sTheme = $this->getEditObjectId();

        /** @var \OxidEsales\Eshop\Core\Theme $oTheme */
        $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
        if (!$oTheme->load($sTheme)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_THEME_NOT_LOADED'));
            return;
        }

        $oParent = $oTheme->getParent();

        $sParent = $oTheme->getInfo('parentTheme');
        $sBaseTheme = $sParent ? $sParent : $sTheme;
        $sThemePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getViewsDir() . $sBaseTheme;

        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        $sGitExecutable = $config->getShopConfVar("sGitExecutable", null, 'module:templatecompiler');
        $sNpmExecutable = $config->getShopConfVar("sNpmExecutable", null, 'module:templatecompiler');
        $sPath = $config->getShopConfVar("sPath", null, 'module:templatecompiler');
        if ($sPath) {
            $sPath = getenv('PATH') . ':' . $sPath;
            putenv('PATH='.$sPath);
        }

        if ( !is_dir($sThemePath.'/build/')) {

            $repo = $this->getTemplateRepository($sBaseTheme);

            //will git clone into a tmp folder
            $compileDir = $config->getConfigParam('sCompileDir');
            $themeDir = $compileDir . "_theme_repo_$sBaseTheme/";

            exec('rm -rf ' . $themeDir);

            $cmd = $sGitExecutable . ' clone -b v'.($oParent ? $oParent->getInfo('version') : $oTheme->getInfo('version')) . ' --single-branch --depth 1 ' . $repo . ' ' . $themeDir;
            exec($cmd);

            exec('cp -r ' . $themeDir . 'build/ ' . $sThemePath);
            exec('cp ' . $themeDir.'package.json ' . $sThemePath);

        }

        if ( !is_dir($sThemePath.'/node_modules/')) {
            echo 'cd ' . $sThemePath . ' && '.$sNpmExecutable.' install >>> ';
            system('cd ' . $sThemePath . ' && '.$sNpmExecutable.' install 2>&1');
            exit();
        }
    }

    public function getTemplateRepository ($sTheme) {
        switch($sTheme){
            case 'wave':
                return 'https://github.com/OXID-eSales/wave-theme';
                break;
        }
    }

}