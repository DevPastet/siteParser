var siteParser = function (config) {
    config = config || {};
    siteParser.superclass.constructor.call(this, config);
};
Ext.extend(siteParser, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('siteParser', siteParser);

siteParser = new siteParser();

siteParser.grid.Items = function(config) {
    config = config || {};

    this.sm = new Ext.grid.CheckboxSelectionModel();


    Ext.applyIf(config,{
        id: 'siteParser-grid-items'
        ,url: '/assets/components/siteparser/connector.php'
        ,fields: ['id','pagetitle','parent']
        ,baseParams: {
            controller: 'get_resourse'
        }
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,width: '97.1%'
        ,sm: this.sm
        ,columns: [this.sm,{
            header: 'Id'
            ,dataIndex: 'id'
        },{
            header: 'Заголовок'
            ,dataIndex: 'pagetitle'
        },{
            header: 'Родитель'
            ,dataIndex: 'parent'
        }]
        ,tbar: [{
            id: 'links-block'
            ,xtype: 'textarea'
            ,emptyText: 'Введите ссылки'
            ,width: 300
        },{
            id: 'modx-go-parser'
            ,text: 'Парсить'
        }]
    });
    siteParser.grid.Items.superclass.constructor.call(this,config);

};

Ext.extend(siteParser.grid.Items,MODx.grid.Grid,{
    windows: {},
    getSelectedAsList: function() {
        alert(1);
    }
});

Ext.reg('siteParser-grid-items',siteParser.grid.Items);


siteParser.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: ''
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeItem: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: 'Парсер сайтов. 1.0.0 beta'
                ,items: [{
                    xtype: 'siteParser-grid-items'
                    ,preventRender: true
                    ,cls: 'main-wrapper'
                }]
            }]
        }]
    });
    siteParser.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(siteParser.panel.Home,MODx.Panel);
Ext.reg('siteParser-panel-home',siteParser.panel.Home);

Ext.onReady(function() {
    MODx.load({ xtype: 'siteParser-page-home'});
});

siteParser.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'siteParser-panel-home'
            ,renderTo: 'ext-gen18'
        }]
    });
    siteParser.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(siteParser.page.Home,MODx.Component);
Ext.reg('siteParser-page-home',siteParser.page.Home);


Ext.onReady(function(){
    var element = Ext.get('modx-go-parser');
    element.on('click', function(e, target, options){
        var links_block = Ext.get('links-block').dom.value;
        if(links_block != 'Введите ссылки'){
            var new_links = [];
            links_block = links_block.replace('\r','').split('\n');
            for(var idx in links_block){
                if(idx != 'remove' && idx != 'in_array'){
                    //if(idx == 0){
                        if(links_block[idx] != ''){
                            Ext.Ajax.request({
                                url: '/connectors/components/siteparser/parse_ajax.php',
                                method: 'POST',
                                params: {
                                    url: links_block[idx]
                                },
                                success: function(response){
                                    console.log(response.responseText);
                                }
                            });

                        }
                    /*}else{
                        new_links[idx] = links_block[idx];
                    }*/
                }
            }
            //new_links = new_links.join('\n').substr(2);
            alert("Парсер отработал!");
            Ext.get('links-block').dom.value = '';
        }
    }, this);
});
