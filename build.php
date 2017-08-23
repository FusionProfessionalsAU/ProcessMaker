<?php
/**
 * Process Maker Php Build tool v. 0.3.1
 * Rev. 05-27-2016
 *
 * Use this as an external tool for Phpstorm
 * Configure as it follows:
 *
 * Name: PM php Builder
 * Group: Process Maker
 * Description: Process Maker Php Builder.
 * Tools Settings
 * Program: $PhpExecutable$
 * Parameters:
 *  build.php --base_dir=$ProjectFileDir$ --mode=prod --extension
 *      --extensionDir=$ProjectFileDir$/workflow/engine/plugins
 * Working Directory: $ProjectFileDir$
 * Please, notice you can change --extensionDir to your own extension folder.
 *
 * Also, use the respective directory owner permisions:
 *      ~/processmaker$ sudo chown -R www-data:www-data workflow/public_html/
 *      ~/processmaker$ sudo chown -R www-data:www-data vendor/colosa/MichelangeloFE/build
 *
 * Use a shortcut:
 *
 * Go to Settings -> Appearance & Behavior -> Keymap, search for ProcessMaker, open the context
 * menu of the option and select Add Keyboard Shortcut, use whatever shortcut you want, for example Alt+b
 *
 * If you need help: php build.php --help
 *
 */
include_once('class.Build.php');
use Build\Utils\ProcessMakerPhpBuilderHelper;

$phpBuilder = new ProcessMakerPhpBuilderHelper();
$phpBuilder->utils->echoContent("Config finished, start deploying...");
$phpBuilder->buildAll();
