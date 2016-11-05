<?php
define('MODX_API_MODE', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/index.php');
$modx= new modX();
$modx->initialize('mgr');

$limit = (int) $modx->getOption('limit',$_POST,10);
$start = (int) $modx->getOption('start',$_POST,0);
$sort = $modx->getOption('sort',$_POST,'id');
$dir = $modx->getOption('dir',$_POST,'ASC');

if(!empty($_POST['parent'])) {
    $parentArr = $modx->getChildIds($_POST['parent'],100,array('context' => 'web'));
    $parentArr[] = $_POST['parent'];
    $where['parent:IN'] = $parentArr;
}

$criteria = $modx->newQuery('modResource');
$criteria->limit($limit, $start);
$criteria->sortby($sort,$dir);
$criteria->where($where);
$total_pages = $modx->getCount('modResource',$criteria);
$pages = $modx->getCollection('modResource',$criteria);

// Init our array
$data = array(
    'results' => array(),
    'total' => $total_pages

);
foreach ($pages as $p) {
    $arrDoc = $p->toArray();

    if($parentDoc = $modx->getObject('modResource',$arrDoc['parent'])){
        $arrDoc['parent'] = $parentDoc->get('pagetitle');
    }

    if(!empty($arrDoc['pagetitle'])){
        $arrDoc['pagetitle'] = '<a href="/manager/?a=resource/update&id='.$arrDoc['id'].'" target="_blank">'.$arrDoc['pagetitle'].'</a>';
    }

    if($doc = $modx->getObject('modResource', $arrDoc['id'])){
        $arrDoc['price'] = $doc->getTVvalue('curPrice');
        $arrDoc['oldprice'] = $doc->getTVvalue('priceNotSale');

        $filterJson = $doc->getTVvalue('migxFilter');
        if(!empty($filterJson)){
            $filters = json_decode($filterJson);
            $filterStr = '';
            foreach($filters as $filterVal){
                $filterStr .= $filterVal->info.'</br>';
            }
            $arrDoc['filters'] = $filterStr;
        }
    }

    $data['results'][] = $arrDoc;
}


$modx->log(1,print_r($_POST,true));
print json_encode($data);