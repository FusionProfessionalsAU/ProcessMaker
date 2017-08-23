<?php
namespace Build\Utils;

include_once('workflow/engine/config/paths_installed.php');

/**
 * Class ProcessMakerPhpBuilderHelper
 * Main Builder class, has methods to build PMUI and MAFE,
 * TODO Improve build functions of PMDynaform and PMDynaformZip
 */
class ProcessMakerPhpBuilderHelper
{
    /**
     * Build Parameters
     * @var array
     */
    protected $parameters = array();
    /**
     * Build Path
     * @var string
     */
    protected $buildPath = "./processmaker";
    /**
     * Directory Array of build files
     * @var array
     */
    public $dir = array();
    /**
     * Process Maker base directory
     * @var string
     */
    public $baseDir = "./";
    /**
     * Public directory of Process Maker
     * @var string
     */
    public $publicDir = "./workflow/public_html";
    /**
     * Build temporal directory
     * @var string
     */
    public $buildTempDir = "./shared/buildTemp";
    /**
     * Production directories
     * @var array
     */
    public $prodDir = array();
    /**
     * Developer directories
     * @var array
     */
    public $devDir = array();
    /**
     * Build Log Directory
     * @var string
     */
    public $logDir = "./shared/log";
    /**
     * Versions class, it contains versions and hashes from repositories
     * @var Versions
     */
    public $versions;
    /**
     * Default build mode
     * @var string
     */
    public $mode = "prod";
    /**
     * Search for extensions build
     * @var bool
     */
    public $extension = false;
    /**
     * Enabled Extensions Directory array
     * @var bool
     */
    public $enabledExtensions = array();
    /**
     * Default theme name
     * @var string
     */
    public $theme = "mafe";
    /**
     * Php build utils
     * @var PhpBuilderUtils
     */
    public $utils;


    /**
     * Construct that loads the config file
     *
     * @return mixed parameters
     */
    public function __construct()
    {
        $this->utils = new PhpBuilderUtils();
        $this->versions = new Versions();
        $this->parameters = $this->getConfig(false);

        return $this->parameters;
    }

    /**
     * Get Function of class key
     * @param string $key
     * @return $value
     */
    public function __get($key)
    {
        return $this->$key;
    }

    /**
     * Set Function of class key
     * @param string $key
     * @param string $value
     *
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Get Config and configure Class variables from Arguments
     * @param $clean
     * @return array
     */
    public function getConfig($clean)
    {
        $config = $this->utils->readArguments();
        // Show Help
        if (isset($config['help'])) {
            $this->utils->exitCode(4);
        }
        $baseDir = "./";
        if (defined('PATH_TRUNK')) {
            $baseDir = PATH_TRUNK;
            $this->utils->silentMode = true;
        }
        $this->baseDir = empty($config['base_dir']) ? $baseDir : $config['base_dir'];
        $this->publicDir = $this->baseDir . "/workflow/public_html";
        $this->buildTempDir = $this->baseDir . "/shared/buildTemp";
        $this->logDir = $this->baseDir . "/shared/log";
        if (defined('PATH_DATA')) {
            $this->logDir = PATH_DATA . "log";
            $this->buildTempDir = PATH_DATA . "buildTemp";
        }
        $this->utils->__set("logDir", $this->logDir);
        $this->utils->refreshDir($this->buildTempDir);
        if (!file_exists($this->publicDir)) {
            $this->utils->exitCode(5);
        }
        //  Check mode dev or prod, by default prod
        $this->mode = (empty($config['mode'])) ? 'prod' : strtolower($config['mode']);
        $this->extension = (empty($config['extension'])) ? false : true;
        $this->theme = (empty($config['theme'])) ? 'mafe' : $config['theme'];

        switch ($this->mode) {
            case 'prod':
                $this->utils->echoContent("Running Production mode.");
                $this->dir['targetDir'] = $this->publicDir . "/lib";
                break;
            case 'dev':
                $this->utils->echoContent("Running Developer mode.");
                $this->dir['targetDir'] = $config['publicDir'] . "/lib-dev";
                break;
            default:
                $this->utils->exitCode(6);
                break;
        }
        $this->dir['pmUIFontsDir'] = $this->dir['targetDir'] . "/fonts";

        //    Config Directories
        $this->dir['jsTargetDir'] = $this->dir['targetDir'] . "/js";
        $this->dir['cssTargetDir'] = $this->dir['targetDir'] . "/css";
        $this->dir['cssImagesTargetDir'] = $this->dir['cssTargetDir'] . "/images";
        $this->dir['imgTargetDir'] = $this->dir['targetDir'] . "/img";

        $this->dir['pmUIDir'] = $this->dir['targetDir'] . "/pmUI";
        $this->dir['mafeDir'] = $this->dir['targetDir'] . "/mafe";
        $this->dir['pmdynaformDir'] = $this->dir['targetDir'] . "/pmdynaform";

        // TODO This clean function must be used only if we need to remove cache from a rake build;
        //  don't use it for now
        if ($clean) {
            $this->utils->echoContent("Preparing dirs...");
            $this->utils->prepare_dirs($this->dir);
        }
        // After delete add this
        $this->dir['baseDir'] = $this->baseDir;
        $this->dir['publicDir'] = $this->publicDir . "/workflow/public_html";
        $this->dir['extensionDir'] = (empty($config['extensionDir'])) ? $this->baseDir . "workflow/engine/plugins" : $config['extensionDir'];

        return $config;
    }

    /**
     * Main Build Function
     */
    public function buildAll()
    {
        $this->buildPmUi();
        $this->buildMafe();
        $this->buildPMDynaform();

        $hashVendors = $this->versions->pmui_hash . "-" . $this->versions->mafe_hash;
        // Building minified JS Files
        $mafeCompresedFile = $this->dir['targetDir'] . "/js";
        $files = $this->utils->getJsIncludeFiles();
        $this->utils->minifyFiles("mafe-$hashVendors", 'js', $files, $this->dir['baseDir'] . "/",
            $mafeCompresedFile . "/", false);

        // Building minified CSS
        $mafeCompresedFile = $this->dir['targetDir'] . "/css";
        $files = $this->utils->getCssIncludeFiles();
        $this->utils->minifyFiles("mafe-$hashVendors", 'css', $files, $this->dir['baseDir'] . "/",
            $mafeCompresedFile . "/", false);

        //Create build-hash file
        $this->utils->echoContent("Create File: buildhash");
        $this->utils->writeToFile("buildhash", $this->dir['targetDir'], $hashVendors, true);

        //Create versions file
        $this->utils->echoContent("Create File: versions");
        $versionsContent = json_encode($this->versions);
        $this->utils->writeToFile("versions", $this->dir['targetDir'], $versionsContent, true);
        $this->cleanUp();
        $this->utils->exitCode(0);
    }

    /**
     * Builds PMUI
     * @return array|Exception
     */
    public function buildPmUi()
    {
        try {
            $this->utils->echoHeader("Building PMUI Library");
            //    Return Values
            $response = array();
            $response['success'] = false;
            $response['js'] = "";
            $response['css'] = "";
            $response['customjs'] = false;
            $response['customcss'] = false;
            $response['hash'] = "";
            $response['version'] = "";

            $source = $this->dir['baseDir'] . "/vendor/colosa/pmUI";
            $target = $this->dir['targetDir'];
            $pmUIDir = $target . "/pmUI";
            $pmUIFontsDir = $target . "/fonts";
            $jsTargetDir = $target . "/js";
            $cssTargetDir = $target . "/css";
            $imgTargetDir = $target . "/img";
            //first hash
            $vh = $this->utils->getHash($source);
            $this->versions->pmui_hash = $vh['hash'];

            $version = file_get_contents($source . '/VERSION.txt');
            $this->versions->pmui_ver = $version;
            $response['version'] = $version;

            $themeDir = $this->dir['baseDir'] . "/vendor/colosa/MichelangeloFE/themes/";
            $themeDir = $themeDir . $this->theme;

            $this->utils->echoContent("1.- Copying lib files into: $pmUIDir");
            $this->utils->copy("$source/build/js/pmui-$version.js", "$pmUIDir/pmui.min.js");
            $this->utils->copy("$themeDir/build/pmui-$this->theme.css", "$pmUIDir/pmui.min.css");
            $this->utils->recCopy("$themeDir/build/images", "$target/css/images");
            $this->utils->recCopy("$source/img", $imgTargetDir);

            $this->utils->echoContent("2.- Copying lib files into: $jsTargetDir");
            $this->utils->copy("$source/libraries/restclient/restclient-min.js", "$jsTargetDir/restclient.min.js");

            $this->utils->echoContent("3.- Copying font files into: $pmUIFontsDir");
            $this->utils->recCopy("$source/themes/$this->theme/fonts", $pmUIFontsDir);

            $this->utils->echoFooter("PMUI Build Finished!");
            $response['success'] = true;

            return $response;
        } catch (Exception $e) {
            $this->utils->exitCode(8, $e);

            return $e;
        }
    }

    /**
     * Builds MAFE Files
     * @return array|Exception
     */
    function buildMafe()
    {
        try {
            $this->utils->echoHeader("Building PM Michelangelo FE");

            $target = $this->dir['targetDir'];
            $source = $this->baseDir . "/vendor/colosa/MichelangeloFE";
            $buildTempDir = $this->buildTempDir . "/vendor/colosa/MichelangeloFE";
            $mafeDir = $target . "/mafe";
            $jsTargetDir = $target . "/js";
            $cssTargetDir = $target . "/css";
            $imgTargetDir = $target . "/img";

            $response = array();
            $response['success'] = false;
            $response['js'] = "";
            $response['css'] = "";
            $response['customjs'] = false;
            $response['customcss'] = false;
            $response['hash'] = "";

            $vh = $this->utils->getHash($source);
            $this->versions->mafe_hash = $vh['hash'];
            $this->versions->mafe_ver = $vh['version'] . '.' . $vh['hash'];

            // Loads all plugins
            $pluginDir = $this->extension ? $this->utils->getExtensionsDirArray($this->dir['extensionDir']) : false;
            if ($this->extension) {
                // Loads all enabled Plugins
                $pluginAdd = "/colosa/MichelangeloFE";
                if (!empty($this->enabledExtensions)) {
                    $pluginDir = $this->utils->prependPluginDir($this->enabledExtensions, $this->dir['extensionDir']);
                }
                $pluginDir = $this->utils->appendPluginDir($pluginDir, $pluginAdd);

            }

            $buildConfig = $source . "/config/build.json";
            $mafeSources = file_get_contents($buildConfig);
            $mafeConfig = $this->utils->json_clean_decode($mafeSources);
            foreach ($mafeConfig as $lib) {
                $this->utils->minifyByConfig($lib, $source, $buildTempDir, false, $pluginDir);
            }

            //Compress App Files
            $appBuildConfig = $source . "/config/applications.json";
            $appMafeSources = file_get_contents($appBuildConfig);
            $appConfig = $this->utils->json_clean_decode($appMafeSources);
            foreach ($appConfig as $lib) {
                $this->utils->minifyByConfig($lib, $source, $buildTempDir, false, $pluginDir);
            }

            //    Copying files to mafe Dir
            $this->utils->echoContent("Copying files into: $mafeDir");
            $this->utils->recCopy($source . "/lib/jQueryUI/images/", $cssTargetDir . "/images/");
            //Compiled Libraries from buildTemp directory
            $this->utils->copyIfExist($source, "/build/js/designer.js", $mafeDir, "/designer.min.js", $buildTempDir);
            $this->utils->copyIfExist($source, "/build/js/mafe.js", $mafeDir, "/mafe.min.js", $buildTempDir);
            $this->utils->copyIfExist($source, "/build/css/mafe.css", $mafeDir, "/mafe.min.css", $buildTempDir);

            $this->utils->recCopy($source . "/img", $imgTargetDir);
            // IMPROVEMENT Register files that have been copied to $imgTargetdir as a plugin consequence
            if ($pluginDir) {
                foreach ($pluginDir as $row) {
                    $this->utils->recCopy($row . "/img", $imgTargetDir);
                }
            }

            $this->utils->echoContent("Copying files into: $jsTargetDir");

            $this->utils->copy($source . "/lib/wz_jsgraphics/wz_jsgraphics.js", $jsTargetDir . "/wz_jsgraphics.js");
            $this->utils->copy($source . "/lib/jQuery/jquery-1.10.2.min.js", $jsTargetDir . "/jquery-1.10.2.min.js");
            $this->utils->copy($source . "/lib/underscore/underscore-min.js", $jsTargetDir . "/underscore-min.js");
            $this->utils->copy($source . "/lib/jQueryUI/jquery-ui-1.10.3.custom.min.js",
                $jsTargetDir . "/jquery-ui-1.10.3.custom.min.js");
            $this->utils->copy($source . "/lib/jQueryLayout/jquery.layout.min.js",
                $jsTargetDir . "/jquery.layout.min.js");
            $this->utils->copy($source . "/lib/modernizr/modernizr.js", $jsTargetDir . "/modernizr.js");

            $this->utils->recCopy($source . "/src/formDesigner/img/", $target . "/img");
            $this->utils->echoFooter("Michelangelo FE Build Finished");
            $response['success'] = true;

            return $response;
        } catch (Exception $e) {
            $this->utils->exitCode(9, $e);

            return $e;
        }

    }

    /**
     * Builds PMDynaform, not implemented yet
     * temporally gets hash and version for main file Versions
     * Take a look of buildPMDynaform of PHPBuilderUtils to improve this function
     * @return Exception|null
     */
    function buildPMDynaform()
    {
        try {
            $target = $this->dir['targetDir'];
            $source = $this->baseDir . "/vendor/colosa/pmDynaform";
            $vh = $this->utils->getHash($source);
            $this->versions->pmdynaform_hash = $vh['hash'];
            $this->versions->pmdynaform_ver = $vh['version'] . "." . $vh['hash'];

            $response = null;

            return $response;
        } catch (Exception $e) {
            $this->utils->exitCode(10, $e);

            return $e;
        }
    }

    /**
     * Clean up function after build
     *
     */
    function cleanUp()
    {
        try {
            $this->utils->delTree($this->buildTempDir);
        } catch (Exception $e) {
            $this->utils->log($e);
        }
    }
}

/**
 * Class PhpBuilderUtils
 * Php Builder Utils has methods as recursive copy, write and minify files
 */
class PhpBuilderUtils
{
    /**
     * Extension config File Directory location.
     *
     * @var string
     */
    public $extensionsJsConfig;
    public $logger;
    public $logDir;
    public $pathSep;

    /**
     * Class constructor.
     *
     */
    public function __construct()
    {
        $this->extensionsJsConfig = "";
        $this->silentMode = false;
        $this->logger = array();
        $this->logDir = "";
        if (defined('PATH_SEP')) {
            $this->pathSep = PATH_SEP;
        } else {
            $this->pathSep = "/";
        }
    }

    /**
     * Get Function of class key
     * @param string $key
     * @return $value
     */
    public function __get($key)
    {
        return $this->$key;
    }

    /**
     * Set Function of class key
     * @param string $key
     * @param string $value
     *
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Logger function.
     *
     * @param mixed $e
     */
    public function log($e)
    {
        $this->logger[] = $e;
    }

    /**
     * Recursive copy of directories,
     * can be updated with compare values and overwrite if newer
     * @param string $src Source directory
     * @param string $dst Target directory
     * @param bool $delete Deletes directory first, copy after delete
     *
     */
    function recCopy($src, $dst, $delete = false)
    {
        if ($delete && file_exists($dst)) {
            $this->delTree($dst);
        }
        if (is_dir($src)) {
            if (!file_exists($dst) && is_writable($dst)) {
                mkdir($dst);
            } else {
                $this->log("File doesn't exist or directory is not writable, file: $dst \n");
            }
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $this->recCopy("$src/$file", "$dst/$file");
                }
            }
        } else {
            $this->copy($src, $dst);
        }
    }

    public function copy($src, $dst)
    {
        try {
            if (file_exists($src) && is_writable($dst)) {
                copy($src, $dst);
            } else {
                $this->log("File doesn't exist or directory is not writable, file: $dst \n");
            }
        } catch (Exception $e) {
            $this->log($e);
        }
    }

    /**
     * Copy tmp File if exists, else copy file from source
     *
     * @param $source
     * @param $file
     * @param $dest
     * @param $destFile
     * @param $tmp
     */
    function copyIfExist($source, $file, $dest, $destFile, $tmp)
    {
        if (file_exists($tmp . $file)) {
            $this->copy($tmp . $file, $dest . $destFile);
        } else {
            $this->copy($source . $file, $dest . $destFile);
        }
    }

    /**
     * Force write file if directory doesn't exist
     *
     * @param $dir
     * @param $contents
     */
    function file_force_contents($dir, $contents)
    {
        $parts = explode($this->pathSep, $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= $part . $this->pathSep)) {
                mkdir($dir, 0777, true);
            }
        }
        $this->file_put_contents($this->pathSep . $file, $dir, $contents);
    }

    public function file_put_contents($file, $dir = "", $contents)
    {
        $dst = $dir . $file;
        try {
            if (is_writable($dir)) {
                file_put_contents($dst, $contents);
            } else {
                $this->log("Cannot write file, check directory permissions, file: $dst \n");
            }
        } catch (Exception $e) {
            $this->log($e);
        }
    }

    /**
     * Clean decode of json string, removes comment lines
     * @param string $json Json string being decoded
     * @param bool $assoc When TRUE return as associative array
     * @param int $depth User specified recursion depth
     * @param int $options Decode options
     * @return mixed Value encoded in Json, False and Null
     *
     */
    function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        // Search and remove comments like /* */ and //
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $json = json_decode($json, $assoc, $depth, $options);
        } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
            $json = json_decode($json, $assoc, $depth);
        } else {
            $json = json_decode($json, $assoc);
        }

        return $json;
    }

    /**
     * Clean up directories
     * @param array $dirs Array of directories to remove and create
     *
     */

    function prepare_dirs($dirs)
    {
        foreach ($dirs as $dir) {
            $this->refreshDir($dir, true);
        }
    }

    /**
     * Clean up directories
     * @param string $dir Directory to remove and create
     * @param bool $showDetail When TRUE echoes directory create action
     *
     */
    function refreshDir($dir, $showDetail = false)
    {
        if (is_writable($dir)) {
            $this->delTree($dir);
        } elseif (!file_exists($dir)) {
            //todo check conditionals
        } else {
            $this->exitCode(7, $dir);
        }
        //Todo change the permissions here
        mkdir($dir, 0777);
        if ($showDetail) {
            $this->echoContent("Create: $dir");
        }
    }

    /**
     * Delete a tree directory structure
     * @param array $dir Directories
     * @return bool True if success, otherwise False
     */
    function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    /**
     * Echo content
     * @param string $content String to show
     *
     */
    function echoContent($content)
    {
        if (!$this->silentMode) {
            echo "$content\n";
        }
    }

    /**
     * Echo content with Format
     * @param string $content String to show
     *
     */
    function echoHeader($content)
    {
        if (!$this->silentMode) {
            echo "\n\e[1;34m$content\033[0m\n";
        }
    }

    /**
     * Echo content with Format
     * @param string $content String to show
     *
     */
    function echoFooter($content)
    {
        if (!$this->silentMode) {
            echo "\n\e[1;32m$content\033[0m\n";
        }
    }

    /**
     * Echo content with Format
     * @param string $content String to show
     *
     */
    function echoError($content = "Error")
    {
        echo "\n\e[1;31m$content\033[0m\n";
    }

    /**
     * Echo content with Format
     * @param string $content String to show
     *
     */
    function echoWarning($content = "Please review files")
    {
        if (!$this->silentMode) {
            echo "\n\e[1;33m$content\033[0m\n";
        }
    }

    /**
     * Write String to file
     * @param string $filename String with filename, please include extension
     * @param string $directory Directory of file
     * @param string $contents Content to be written
     * @param bool $overwrite Replace or append content to file, True when you want to overwrite it
     *
     */
    function writeToFile($filename, $directory, $contents, $overwrite = true)
    {
        $current = file_exists($directory . "/" . $filename) ? file_get_contents($directory . "/" . $filename) : "";
        if (!$overwrite) {
            $current .= $contents;
        } else {
            $current = $contents;
        }
        $this->file_put_contents("/$filename", $directory, $current);
    }

    /**
     * Recursive add a directory content to zip file
     * @param mixed $zip
     * @param String $dir Directory
     * @param String $base Base directory
     * @return mixed
     */
    function addDirectoryToZip($zip, $dir, $base)
    {
        $newFolder = str_replace($base, '', $dir);
        $zip->addEmptyDir($newFolder);
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $zip = $this->addDirectoryToZip($zip, $file, $base);
            } else {
                $newFile = str_replace($base, '', $file);
                $zip->addFile($file, $newFile);
            }
        }

        return $zip;
    }

    /**
     * Returns Directory as array from all extensions.
     * @param String $baseDir Base dir of a Process Maker Installation
     * @return array
     */
    function getExtensionsDirArray($baseDir)
    {
        return $this->directoryToArray($baseDir, false, true, false);
    }

    /**
     * Returns directory files as array without .. and .
     * @param String $directory
     * @return array
     */
    function dirContent($directory)
    {
        return array_diff(scandir($directory), array('..', '.'));
    }

    /**
     * Get an array that represents directory tree
     * @param string $directory Directory path
     * @param bool $recursive Include sub directories
     * @param bool $listDirs Include directories on listing
     * @param bool $listFiles Include files on listing
     * @param regex $exclude Exclude paths that matches this regex
     * @return array $arrayItems Directory tree
     */
    function directoryToArray($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '')
    {
        $arrayItems = array();
        $skipByExclude = false;
        if (!file_exists($directory)) {
            return $arrayItems;
        }
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
                if ($exclude) {
                    preg_match($exclude, $file, $skipByExclude);
                }
                if (!$skip && !$skipByExclude) {
                    if (is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
                        if ($recursive) {
                            $arrayItems = array_merge($arrayItems,
                                $this->directoryToArray($directory . DIRECTORY_SEPARATOR . $file, $recursive, $listDirs,
                                    $listFiles, $exclude));
                        }
                        if ($listDirs) {
                            $file = $directory . DIRECTORY_SEPARATOR . $file;
                            $arrayItems[] = $file;
                        }
                    } else {
                        if ($listFiles) {
                            $file = $directory . DIRECTORY_SEPARATOR . $file;
                            $arrayItems[] = $file;
                        }
                    }
                }
            }
            closedir($handle);
        }

        return $arrayItems;
    }

    /**
     * Minify files by read config of json file, use this with PMUI and MAFE build.json
     *
     * @param Object $lib Config read from build.json
     * @param String $source Source directory
     * @param String $target Target directory
     * @param bool $addMinExt When TRUE, adds '.min' before extension
     * @param bool $addPluginFiles When TRUE, search from extensions folders to append to build file
     */
    function minifyByConfig($lib, $source, $target, $addMinExt = false, $addPluginFiles = false)
    {
        // Todo Please standardize target_dir or target_to from config files
        $target_dir = !empty($lib->target_dir) ? $lib->target_dir : $lib->target_to;
        $sourceFrom = "";
        if (!empty($lib->target_from)) {
            $sourceFrom = $lib->target_from . "/";
            if ($addPluginFiles) {
                $addPluginFiles = $this->appendPluginDir($addPluginFiles, "/" . $sourceFrom);
            }
        }
        $files = $lib->files;
        if ($addMinExt) {
            $lib->name = $lib->name . ".min";
        }
        switch ($lib->extension) {
            case "js":
                $this->echoContent("Building Js File");
                $this->minifyFiles($lib->name, 'js', $files, $source . "/" . $sourceFrom, $target . "/$target_dir",
                    $addPluginFiles);
                break;
            case "css":
                $this->echoContent("Building Css File");
                $this->minifyFiles($lib->name, 'css', $files, $source . "/", $target . "/$target_dir", $addPluginFiles);
                break;
            default:
                $this->echoWarning('No extension defined or usable to build file');
                break;
        }
    }

    /**
     * Build minified file from files
     * TODO Include a minifier as else statement
     * @param String $name Filename of target
     * @param String $extension Extension of file (css, js)
     * @param Array $files Array with names of files to include into build file
     * @param String $basePath Source directory to search files
     * @param String $targetPath Target directory to save build file
     * @param bool $addPlugin When TRUE, includes files from Plugin-Extensions folders
     * @param bool $minify When TRUE, minify the files, NOT INCLUDED YET.
     * @return bool|mixed|string
     */
    function minifyFiles($name, $extension, $files, $basePath, $targetPath, $addPlugin = false, $minify = false)
    {
        if (!$minify) {
            $minifiedPath = $targetPath . $name . "." . $extension;
            $content = "";
            $content = $content . $this->joinFiles($basePath, $files, $addPlugin);
            $this->file_force_contents($minifiedPath, $content);

            return $content;
        }

        return false;
    }

    /**
     * Appends Plugin-Extension Directory to every file in
     * @param Array $pluginDir
     * @param String $pluginAdd
     * @return Array
     */
    function appendPluginDir($pluginDir, $pluginAdd)
    {
        foreach ($pluginDir as $key => $row) {
            $pluginDir[$key] = $row . $pluginAdd;
        }

        return $pluginDir;
    }

    /**
     * Prepend Plugin-Extension Directory to every file in
     * @param Array $pluginDir
     * @param String $baseDir
     * @return Array
     */
    function prependPluginDir($pluginDir, $baseDir)
    {
        foreach ($pluginDir as $key => $row) {
            $pluginDir[$key] = $baseDir . '/' . $row;
        }

        return $pluginDir;
    }

    /**
     * Join files into one
     * @param string $baseDir Base file of Directory
     * @param array $files file source array
     * @param bool $plugin When True, searches for extensions files
     * @param string $separator Custom file separator
     * @return string $content File contents
     */
    function joinFiles($baseDir, $files, $plugin = false, $separator = "\n")
    {
        $content = "";
        foreach ($files as $file) {
            $content = $content . file_get_contents("$baseDir/$file") . $separator;
            if ($plugin && is_array($plugin)) {
                foreach ($plugin as $row) {
                    if (file_exists("$row/$file")) {
                        $content = $content . file_get_contents("$row/$file") . $separator;
                        $this->echoWarning("Custom code added for: $row/$file");
                    }
                }
            }
        }

        return $content;
    }

    /**
     * Get hash and version of repositories
     * @param string $source Base directory of repository
     * @return array $values with hash and version of repository
     */
    function getHash($source)
    {
        $values = [];
        $values['hash'] = substr(md5($source), 0, 7);
        $values['version'] = '1.0.0';

        return $values;
    }

    /**
     * Get Git hash and version of repositories
     * @param string $source Base directory of repository
     * @return array $values with hash and version of repository
     */
    function getGitHash($source)
    {
        $cwd = getcwd();
        chdir($source);
        exec('git rev-parse --short HEAD', $hashes);
        exec('git rev-parse --abbrev-ref HEAD', $version);
        chdir($cwd);
        $values = [];
        $values['hash'] = !empty($hashes[0]) ? $hashes[0] : '';
        $values['version'] = !empty($version[0]) ? $version[0] : '';

        return $values;
    }

    /**
     * Shows help
     */
    function showHelpDerp()
    {
        echo <<<END
Build options:

-—base_dir
    If you want to call on build.php from outside of the processmaker directory,
    you must specify your project location using —-base_dir

    php build.php --base_dir=/Users/optimus/processmaker

-—mode
    Specifies the flavor to build, use dev or prod.
    Default option is prod.

    php build.php --mode=dev

    Dev will only build the files required, Prod will also minify them.

-—extensions
    Add extensions directories to Process Maker build (js, css, img), allocated in
    'processmaker/workflow/extensions/{extensionName}'
    Files from vendor must include 'colosa/Michelangelo/' as example.
    Default option is false.

    php build.php --extensions

-—clean
    NOT AVAILABLE YET: Will tell the build system to either delete files before building or not.
    —-clean will delete files before building


-—cleanCache
    NOT AVAILABLE YET: Clears the cache before doing the build. This will only delete certain
    cache files before doing a build.

    php build.php --cleanCache

    Will clean the cache directory.

END;
    }

    /**
     * Exit code helper
     * @param int $code
     * @param string $contents
     */
    public function exitCode($code, $contents = "")
    {
        switch ($code) {
            case 0:
                $this->echoFooter("-- DONE --");
                break;
            case 4:
                $this->showHelpDerp();
                break;
            case 5:
                $this->echoContent("This ain't no Process Maker install directory mate");
                $this->echoContent("Please check if your executing this file into processmaker");
                $this->echoContent("Pro tip: You can also specify the base dir with: --base_dir=\"path/to/processmaker\"");
                break;
            case 6:
                $this->echoContent("Mode doesn't exist pal. Use prod or dev mode.");
                break;
            case 7:
                $this->echoError("Error. Directory $contents is not writable, please check permissions.");
                break;
            case 8:
                $this->echoError("Error when building PMUI, please review logs.");
                $this->echoContent($contents);
                break;
            case 9:
                $this->echoError("Error when building MAFE, please review logs.");
                $this->echoContent($contents);
                break;
            case 10:
                $this->echoError("Error when building MAFE, please review logs.");
                $this->echoContent($contents);
                break;
            default:
                $this->echoContent("Exit Code Not found, exit anyway lol");
                break;
        }
        $this->writeToFile("build.log", $this->logDir, $this->logger);
        exit($code);
    }

    /**
     * Read arguments Helper
     * @return array
     */
    public function readArguments()
    {
        // Read arguments
        global $argv;
        if (!isset($config)) {
            $config = array();
        }
        if (isset($argv)) {
            foreach ($argv as $arg) {
                if (substr($arg, 0, 2) == '--') {
                    $arg = substr($arg, 2, strlen($arg));
                }
                if (substr($arg, 0, 1) == '-') {
                    $arg = substr($arg, 1, strlen($arg));
                }
                $params = explode('=', $arg);
                if (count($params) > 1) {
                    $config[$params[0]] = $params[1];
                } else {
                    $config[$params[0]] = true;
                }
            }
        }

        return $config;
    }

    /**
     * Include files to minify, this array is a copy from rake include files
     * @return array
     */
    function getJsIncludeFiles()
    {

        $includeFiles = [
            "workflow/public_html/lib/js/wz_jsgraphics.js",
            "workflow/public_html/lib/js/jquery-1.10.2.min.js",
            "workflow/public_html/lib/js/underscore-min.js",
            "workflow/public_html/lib/js/jquery-ui-1.10.3.custom.min.js",
            "workflow/public_html/lib/js/jquery.layout.min.js",
            "workflow/public_html/lib/js/modernizr.js",
            "workflow/public_html/lib/js/restclient.min.js",
            "workflow/public_html/lib/pmUI/pmui.min.js",
            "workflow/public_html/lib/mafe/mafe.min.js",
            "workflow/public_html/lib/mafe/designer.min.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/tiny_mce.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmGrids/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmSimpleUploader/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmVariablePicker/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualchars/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/xhtmlxtras/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/wordcount/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualblocks/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/table/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/template/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualblocks/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/preview/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/print/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/style/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/save/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/tabfocus/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/searchreplace/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/paste/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/media/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/lists/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/insertdatetime/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/example/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pagebreak/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/example_dependency/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/noneditable/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/fullpage/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/layer/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/legacyoutput/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/fullscreen/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/iespell/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/inlinepopups/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/autoresize/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/contextmenu/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advlist/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/autolink/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/directionality/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/emotions/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/editor_template.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advhr/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advlink/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advimage/editor_plugin.js",
            "gulliver/js/tinymce/jscripts/tiny_mce/plugins/nonbreaking/editor_plugin.js",
            "gulliver/js/codemirror/lib/codemirror.js",
            "gulliver/js/codemirror/addon/hint/show-hint.js",
            "gulliver/js/codemirror/addon/hint/javascript-hint.js",
            "gulliver/js/codemirror/addon/hint/sql-hint.js",
            "gulliver/js/codemirror/addon/hint/php-hint.js",
            "gulliver/js/codemirror/addon/hint/html-hint.js",
            "gulliver/js/codemirror/mode/javascript/javascript.js",
            "gulliver/js/codemirror/addon/edit/matchbrackets.js",
            "gulliver/js/codemirror/mode/htmlmixed/htmlmixed.js",
            "gulliver/js/codemirror/mode/xml/xml.js",
            "gulliver/js/codemirror/mode/css/css.js",
            "gulliver/js/codemirror/mode/clike/clike.js",
            "gulliver/js/codemirror/mode/php/php.js",
            "gulliver/js/codemirror/mode/sql/sql.js",
        ];

        return $includeFiles;
    }

    /**
     * Include files to minify, this array is a copy from rake include files
     * @return array
     */
    function getCssIncludeFiles()
    {
        return [
            "gulliver/js/codemirror/lib/codemirror.css",
            "gulliver/js/codemirror/addon/hint/show-hint.css",
            "workflow/public_html/lib/pmUI/pmui.min.css",
            "workflow/public_html/lib/mafe/mafe.min.css",
        ];
    }

    /**
     * pmUITheme config helper, from ruby config file
     * @return array
     */
    function pmUIThemeConfigRB()
    {
        return [
            'http_path' => "/",
            'css_dir' => "./css",
            'sass_dir' => "./src/sass",
            'images_dir' => "./img",
            'javascripts_dir' => "./src",
            'http_generated_images_path' => "../img",
            'http_fonts_path' => '../fonts',
        ];
    }

    /**
     * Build PMDynaform Wannabe,
     * TODO USE IT AS BASE TO A REAL DYNAFORM BUILDER, SCRIPT IS NOT COMPLETE
     * @param $home
     * @param $target
     * @param $mode
     */
    function buildPmdynaform($home, $target, $mode)
    {
        $source = $home . "/vendor/colosa/pmDynaform";

        $this->echoHeader("Building PmDynaform Library");

        $pmdynaformDir = $target . "/pmdynaform";

        if (!file_exists("$pmdynaformDir/build")) {
            mkdir("$pmdynaformDir/build", 0777);
        }
        $this->recCopy("$source/build", "$pmdynaformDir/build", false);
        if (!file_exists("$pmdynaformDir/libs")) {
            mkdir("$pmdynaformDir/libs", 0777);
        }
        $this->recCopy("$source/libs", "$pmdynaformDir/libs", false);
        $config = file_get_contents($source . "/config/templates.json");
        $config = $this->json_clean_decode($config);
        $template = "";

        foreach ($config as $tmp) {

            $s = $this->joinFiles($source, $tmp->files);
            $template = $template . $s;
        }

        //    todo review something else within Dynaform

        $this->echoFooter("PMUI Dynaform Finished!");
    }

    /**
     * Build PMDynaformZip Wannabe,
     * TODO USE IT AS BASE TO A REAL DYNAFORMZIP BUILDER
     * @param $home
     * @param $target
     * @param $mode
     */
    function buildPmdynaformZip($home, $target, $mode)
    {
        $source = $home . "/vendor/colosa/pmDynaform";

        $this->echoHeader("Building Compress Zip Library");
        //    if ($mode == "dev"){
        $minified = [
            "src/language/en.js",
            "src/PMDynaform.js",
            "src/core/Selector.js",
            "src/core/Utils.js",
            "src/core/Mask.js",
            "src/core/Validators.js",
            "src/core/Project.js",
            "src/core/Formula.js",
            "src/core/PMRest.js",
            "src/core/TransformJSON.js",
            "src/core/FileStream.js",
            "src/core/FullScreen.js",
            "src/core/Script.js",
            "src/util/ArrayList.js",
            "src/extends/core/ProjectMobile.js",
            "src/implements/WebServiceManager.js",
            "src/extends/core/ProcessFlow.js",
            "src/extends/core/ProcessFlowIOS.js",
            "src/extends/core/FormsHandler.js",
            "src/extends/core/DataLocalManager.js",
            "src/extends/views/MediaElement.js",
            "src/proxy/RestProxy.js",
            "src/views/Validator.js",
            "src/views/Panel.js",
            "src/views/FormPanel.js",
            "src/views/Field.js",
            "src/views/Grid.js",
            "src/views/GridMobile.js",
            "src/views/Button.js",
            "src/views/DropDown.js",
            "src/views/Radio.js",
            "src/views/Submit.js",
            "src/views/TextArea.js",
            "src/views/Text.js",
            "src/views/File.js",
            "src/views/CheckBox.js",
            "src/views/CheckGroup.js",
            "src/views/Suggest.js",
            "src/views/Link.js",
            "src/views/Label.js",
            "src/views/Title.js",
            "src/views/Empty.js",
            "src/views/Hidden.js",
            "src/views/Image.js",
            "src/views/SubForm.js",
            "src/views/GeoMap.js",
            "src/views/Annotation.js",
            "src/views/Datetime.js",
            "src/views/PanelField.js",
            "src/extends/views/ScannerCode.js",
            "src/extends/views/Signature.js",
            "src/extends/views/Geo.js",
            "src/extends/views/FileMobile.js",
            "src/models/Validator.js",
            "src/models/Panel.js",
            "src/models/FormPanel.js",
            "src/models/Field.js",
            "src/models/Grid.js",
            "src/models/Button.js",
            "src/models/DropDown.js",
            "src/models/Radio.js",
            "src/models/Submit.js",
            "src/models/TextArea.js",
            "src/models/Text.js",
            "src/models/File.js",
            "src/models/CheckBox.js",
            "src/models/CheckGroup.js",
            "src/models/Datetime.js",
            "src/models/Suggest.js",
            "src/models/Link.js",
            "src/models/Label.js",
            "src/models/Title.js",
            "src/models/Empty.js",
            "src/models/Hidden.js",
            "src/models/Image.js",
            "src/models/SubForm.js",
            "src/models/GeoMap.js",
            "src/models/Annotation.js",
            "src/extends/models/ScannerCode.js",
            "src/extends/models/Signature.js",
            "src/extends/models/Geo.js",
            "src/extends/models/FileMobile.js",
            "src/models/PanelField.js",
            "src/extends/core/get_scripts.js"
        ];
        $index = [
            'appMobile/form-dev.html',
            "src/templates/template.html",
            "src/templates/controls.html",
            "src/templates/GridTemplate.html",
            "src/templates/GeoMapTemplate.html",
            "src/templates/annotation.html",
            "src/templates/Qrcode_mobile.html",
            "src/templates/Signature_mobile.html",
            "src/templates/GeoMobile.html",
            "src/templates/FileMobile.html",
            "src/templates/PanelField.html",
            'appMobile/form-dev2.html'
        ];

        $dev = $source . "/build-dev";
        $css = $dev . "/css";
        $js = $dev . "/js";
        $img = $dev . "/img";
        $libs = $dev . "/libs";


        $this->refreshDir($dev);
        $this->refreshDir($css);
        $this->refreshDir($js);
        $this->refreshDir($img);
        $this->refreshDir($libs);
        $this->recCopy($source . "/fonts", $css . "/fonts", false);

        $this->minifyFiles('pmDynaform', 'js', $minified, $source . "/", $dev . "/js/", false);
        $this->recCopy($source . "/libs", $dev . "/libs", false);
        $this->copy($source . "/appMobile/appBuild.js", $dev . "/appBuild.js");
        $this->copy($source . "/img/geoMap.jpg", $dev . "/geoMap.jpg");
        $htmlFile = $this->joinFiles($source, $index);
        $this->file_put_contents("/index.html", $dev, $htmlFile);

        $cssmin = [
            "libs/bootstrap-3.1.1/css/bootstrap.min.css",
            "libs/datepicker/bootstrap-datetimepicker.css",
            "css/pmDynaform.css"
        ];
        $minified = [
            "libs/jquery/jquery-1.11.js",
            "libs/bootstrap-3.1.1/js/bootstrap.min.js",
            "libs/datepicker/bootstrap-datetimepicker.js",
            "libs/underscore/underscore-1.6.js",
            "libs/backbone/backbone-min.js",
            "libs/restclient/restclient.js",
            "js/pmDynaform.js"
        ];
        $index = [
            "appMobile/form-prod.html",
            "src/templates/template.html",
            "src/templates/controls.html",
            "src/templates/GridTemplate.html",
            "src/templates/GeoMapTemplate.html",
            "src/templates/annotation.html",
            "src/templates/Qrcode_mobile.html",
            "src/templates/Signature_mobile.html",
            "src/templates/GeoMobile.html",
            "src/templates/FileMobile.html",
            "src/templates/PanelField.html",
            "appMobile/form-prod2.html"
        ];

        $prod = $source . "/build-prod";
        $zipProd = $source . "/build-prod-zip";
        $css = $prod . "/css";
        $js = $prod . "/js";
        $img = $prod . "/img";

        $this->refreshDir($prod);
        $this->refreshDir($css);
        $this->refreshDir($js);
        $this->refreshDir($img);

        $this->recCopy($source . "/fonts", $css . "/fonts", false);
        $this->recCopy($dev . "/libs/bootstrap-3.1.1/fonts/", $prod . "/fonts/", false);


        $this->minifyFiles('pmDynaform.min', 'css', $cssmin, $dev . "/", $prod . "/css/", false);
        $this->minifyFiles('pmDynaform.min', 'js', $minified, $dev . "/", $prod . "/js/", false);

        $this->copy($source . "/appMobile/appBuild.js", $prod . "/appBuild.js");
        $this->copy($source . "/img/geoMap.jpg", $prod . "/geoMap.jpg");
        $this->recCopy($source . "/img", $prod . "/img", false);
        $htmlFile = $this->joinFiles($source, $index);
        $this->file_put_contents("/index.html", $prod, $htmlFile);


        // Create Zip File
        $this->refreshDir($zipProd);
        $directoryToZip = $source . "/build-prod";
        $outputFile = "$zipProd/build-prod.zip";
        $zipFile = new ZipArchive();
        $zipFile->open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zipFile = $this->addDirectoryToZip($zipFile, $prod, $directoryToZip);
        $zipFile->close();
        if (file_exists($outputFile)) {
            copy($outputFile, $target . "/build-prod.zip");
        }
        $this->echoFooter("PMUI Dynaform Zip Finished!");
    }
}

/**
 * Class Versions
 * Helper to easy build Versions file
 */
class Versions
{
    public $pmui_ver = "";
    public $pmui_hash = "";
    public $mafe_ver = "";
    public $mafe_hash = "";
    public $pmdynaform_ver = "";
    public $pmdynaform_hash = "";

    /**
     * Get Function of class key
     * @param string $key
     * @return $value
     */
    public function __get($key)
    {
        return $this->$key;
    }

    /**
     * Set Function of class key
     * @param string $key
     * @param string $value
     *
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
}
