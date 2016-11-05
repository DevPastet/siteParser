<?php

class SiteParser {
    protected $modx = null;
    public $template = 'default';
    public $config = array();
    public $url = '';

    function __construct(modX &$modx, $params)
    {
        $this->url = $params['url'];
        if(isset($params['template'])){
            $this->template = $params['template'];
        }
        require_once MODX_CORE_PATH.'components/siteparser/model/custom/templates/'.$this->template.'.php';
        $this->config = $config;

        $this->modx =& $modx;
        $fields = $this->getTempFields();
        $this->getSiteFields($fields);
    }

    // Получаем поля шаблона запроса
    public function getTempFields()
    {
        $fields = $this->config['parser'];
        return $fields;
    }

    // формируем хапрос
    public function request($url, $param)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => "mSocial",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $param
        );
        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $curlResult = curl_exec( $ch );
        curl_close( $ch );
        return $curlResult;
    }

    // Запрашиваем по шаблону
    public function getSiteFields($fields)
    {
        $html = str_get_html($this->request($this->url));

        $result = array();
        foreach($fields as $key => $val)
        {
            if(empty($val['attr']))
            {
                if(!empty($val['type_parser']))
                {
                    $tag = $val['type_parser'];
                    $result[$key] = trim($html->find($val['tags'], 0)->tag);
                }else{
                    $result[$key] = trim($html->find($val['tags'], 0)->plaintext);
                }
            }else{
                $attr = $val['attr'];
                $result[$key] = $html->find($val['tags'], 0)->$attr;
            }
        }
        $this->saveResource($result);
    }

    // Сохраняем ресурс
    public function saveResource($fields)
    {
        $docParams = array();
        $tvParams = array();
        foreach($fields as $key => $val){
            $pos = strpos($key, 'tv.');
            if ($pos === false) {
                $docParams[$key] = $val;
            } else {
                $key = substr($key, 3);
                $tvParams[$key] = $val;
            }
        }
        $document = $this->modx->newObject('modResource');
        $document->set('createdby', $this->modx->user->get('id'));
        $document->set('createdon', time());
        $document->set('publishedon', date('Y-m-d g:i a'));
        foreach($this->config['default'] as $key => $val){
            $document->set($key, $val);
        }

        foreach($docParams as $key => $val){
            $document->set($key, $val);
        }
        $document->save();

        foreach($tvParams as $key => $val){
            $document->setTVValue($key, $val);
        }
        $document->save();
        exit;
    }
}