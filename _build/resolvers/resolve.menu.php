<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:

            $action = $modx->getObject('modAction',array(
                'namespace' => 'siteparser',
            ));
            if(!$action){
                $newAction = $modx->newObject('modAction');
                $newAction->set('namespace','siteparser');
                $newAction->set('controller','controllers/');
                $newAction->set('haslayout','1');
                $newAction->set('lang_topics','siteparser:default');
                $newAction->save();

                $id = $newAction->get('id');
            }else{
                $id = $action->get('id');
            }

            $menu = $modx->getObject('modMenu',array(
                'namespace' => 'siteparser',
            ));
            if($menu){
                $menu->set('action',$id);
                $menu->save();
            }
            break;

        case xPDOTransport::ACTION_UPGRADE:
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;