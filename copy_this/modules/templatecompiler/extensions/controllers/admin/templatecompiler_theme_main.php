<?php

use ScssPhp\ScssPhp\Compiler;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\StringAsset;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\JSqueezeFilter;
use Assetic\Filter\UglifyCssFilter;

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

            $filter = new ScssphpFilter();
            $filter->setFormatter(\ScssPhp\ScssPhp\Formatter\Crunched::class);
            $filter->addImportPath($sScssPath);
            if ($sParent) {
                $filter->addImportPath($sParentScssPath);
            }

            $collection = new AssetCollection(array(
                new StringAsset(file_get_contents($blHasThemeStyleScss ? $sFilePath : $sParentFilePath), array($filter))
            ));

            try{
                $css = $collection->dump();
            } catch (\Exception $e) {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($e->getMessage());
                return;
            }

            $sOutPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getOutDir() . $sTheme . "/src/css/";
            $sOutFile = $sOutPath . 'styles.min.css';

            file_put_contents($sOutFile, $css);
        }
    }

    public function compilescripts() {
        $sTheme = $this->getEditObjectId();

        /** @var \OxidEsales\Eshop\Core\Theme $oTheme */
        $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
        if (!$oTheme->load($sTheme)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_THEME_NOT_LOADED'));
            return;
        }

        $sJsPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getViewsDir() . $sTheme . "/build/js/";

        $sParent = $oTheme->getInfo('parentTheme');
        if ($sParent) {
            $sParentJsPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getViewsDir() . $sParent . "/build/js/";
        }

        $assets = [
            'node_modules/jquery/dist/jquery.min.js',
            'build/vendor/jquery-ui/js/jquery-ui.js',
            'node_modules/popper.js/dist/umd/popper.min.js',
            'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
            'build/vendor/jquery-unveil/js/jquery.unveil.js',
            'build/vendor/jquery-flexslider2/js/jquery.flexslider.js',
            'build/vendor/jquery-bootstrap-validation/js/jqBootstrapValidation.js',
            'build/js/main.js',
            'build/js/pages/compare.js',
            'build/js/pages/details.js',
            'build/js/pages/review.js',
            'build/js/pages/start.js'
        ];

        $collection = new AssetCollection([], new JSqueezeFilter());

        foreach($assets as $asset) {
            if (file_exists($sJsPath.$asset)){
                $collection->add(new FileAsset($sJsPath.$asset));
            }elseif($sParent && file_exists($sParentJsPath.$asset)){
                $collection->add(new FileAsset($sParentJsPath.$asset));
            }
        }

        try{
            $js = $collection->dump();
        } catch (\Exception $e) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($e->getMessage());
            return;
        }

        $sOutPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getOutDir() . $sTheme . "/src/js/";
        $sOutFile = $sOutPath . 'script.min.js';

        file_put_contents($sOutFile, $js);
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