<?php

$sMetadataVersion = '1.1';

$aModule = array(
    'id'           => 'templatecompiler',
    'title'        => 'Template Compiler',
    'description'  => 'Compile oxid eShop template assets through admin interface',
    'thumbnail'    => '',
    'version'      => '1.1.2',
    'author'       => 'Aggrosoft GmbH',
    'extend'      => array(
        'theme_main' => 'templatecompiler/extensions/controllers/admin/templatecompiler_theme_main'
    ),
    'files'       => array(

    ),
    'templates'   => array(

    ),
    'events'       => array(

    ),
    'settings' => array(
        array('group' => 'templatecompiler_environment', 'name' => 'sPath', 'type' => 'str',   'value' => ''),
        array('group' => 'templatecompiler_environment', 'name' => 'sGitExecutable', 'type' => 'str',   'value' => 'git'),
        array('group' => 'templatecompiler_environment', 'name' => 'sNpmExecutable', 'type' => 'str',   'value' => 'npm'),
        array('group' => 'templatecompiler_environment', 'name' => 'aExtraCSSFiles', 'type' => 'aarr',   'value' => []),
    ),
    'blocks' => array(
        [
            'template' => 'theme_main.tpl',
            'block' => 'admin_theme_main_form',
            'file' => '/views/blocks/admin/admin_theme_main_form.tpl',
        ],
    )
);
