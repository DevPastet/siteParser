<?php
define('MODX_API_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/index.php');
$modx= new modX();
$modx->initialize('mgr');

require_once MODX_CORE_PATH.'components/siteparser/model/custom/simple_html_dom.php';

$params = array(
    'url' => $_POST['url']
);

$path = $modx->getOption('siteparser.core_path',null,$modx->getOption('core_path').'components/siteparser/');
$siteParser = $modx->getService('siteparser','SiteParser', $path.'model/siteparser/', $params);
