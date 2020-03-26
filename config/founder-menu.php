<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/21 下午5:01
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

return [
    'founder_plugins' => [
        'name' => '插件管理',
        'url' => 'plugins.get-plugin-data',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-puzzle-piece',
        'sort' => '0',
        'item' => 'plugins',
        'parents' => ['system',],
        'child' => [
            'plugins_enable' => [
                'name' => '启用插件',
                'url' => 'plugins.enable',
                'url_params' => '',
                'permit' => 1,
                'menu' => 0,
                'icon' => '',
                'sort' => '1',
                'item' => 'plugins_enable',
                'parents' => ['system', 'plugins',],
            ],

            'plugins_disable' => [
                'name' => '禁用插件',
                'url' => 'plugins.disable',
                'url_params' => '',
                'permit' => 1,
                'menu' => 0,
                'icon' => '',
                'sort' => '2',
                'item' => 'plugins_disable',
                'parents' => ['system', 'plugins',],
            ],

            'plugins_manage' => [
                'name' => '插件安装',
                'url' => 'plugins.manage',
                'url_params' => '',
                'permit' => 1,
                'menu' => 0,
                'icon' => '',
                'sort' => '3',
                'item' => 'plugins_manage',
                'parents' => ['system', 'plugins',],
            ],

            'plugins_delete' => [
                'name' => '插件卸载',
                'url' => 'plugins.delete',
                'url_params' => '',
                'permit' => 1,
                'menu' => 0,
                'icon' => '',
                'sort' => '4',
                'item' => 'plugins_delete',
                'parents' => ['system', 'plugins',],
            ],

            'plugins_update' => [
                'name' => '插件升级',
                'url' => 'plugins.update',
                'url_params' => '',
                'permit' => 1,
                'menu' => 0,
                'icon' => '',
                'sort' => '5',
                'item' => 'plugins_update',
                'parents' => ['system', 'plugins',],
            ],
        ],
    ],
    'supervisor' => [
        'name' => '队列管理',
        'url' => 'supervisord.supervisord.index',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-history',
        'sort' => '5',
        'item' => 'supervisor',
        'parents' => ['system'],
        'child' => [
            'supervisord_supervisord_index' => [
                'name' => '队列运行状态',
                'url' => 'supervisord.supervisord.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'sort' => '2',
                'item' => 'supervisord_supervisord_index',
                'parents' => ['system', 'supervisor', 'supervisord_supervisord_index'],
                'child' => []
            ],
            'supervisord_supervisord_store' => [
                'name' => '服务器设置',
                'url' => 'supervisord.supervisord.store',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'sort' => '2',
                'item' => 'supervisord_supervisord_store',
                'parents' => ['system', 'supervisor', 'supervisord_supervisord_store'],
                'child' => []
            ],
            'supervisord_supervisord_queue' => [
                'name' => '队列设置',
                'url' => 'supervisord.supervisord.queue',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'sort' => '2',
                'item' => 'supervisord_supervisord_queue',
                'parents' => ['system', 'supervisor', 'supervisord_supervisord_queue'],
                'child' => []
            ],
        ],
    ],
    'site_setting' => [
        'name' => '站点设置',
        'url' => 'siteSetting.index',
        'url_params' => '',
        'permit' => 1,
        'menu' => 1,
        'icon' => 'fa-cog',
        'sort' => '5',
        'item' => 'site_setting_index',
        'parents' => ['system'],
        'child' => [
            'site_setting.index' => [
                'name' => '查看设置',
                'url' => 'siteSetting.index.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 0,
                'icon' => '',
                'sort' => '1',
                'item' => 'site_setting_index_index',
                'parents' => ['system', 'site_setting', 'system_site_setting_index_index'],
                'child' => []
            ],
            'site_setting.store' => [
                'name' => '保存设置',
                'url' => 'site_setting.store.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 0,
                'icon' => '',
                'sort' => '2',
                'item' => 'site_setting_store_index',
                'parents' => ['system', 'site_setting','system_site_setting_store_index'],
                'child' => []
            ],

        ]
    ],
    'work_order'      => [
        'name'              => '工单管理',
        'url'               => 'setting.work-order.index',
        'url_params'        => '',
        'permit'            => 1,
        'menu'              => 1,
        'icon'              => 'fa-list-ul',
        'sort'              => '5',
        'item'              => 'log-viewer',
        'parents'           => ['system',],
        'child'      => [
            'work_order_store_page' => [
                'name'       => '工单提交页面',
                'url'        => 'setting.work-order.store-page',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 0,
                'icon'       => 'fa-list-ul',
                'sort'       => '6',
                'item'       => 'operation_log',
                'parents'    => ['system',],
            ],

            'work_order_details' => [
                'name'       => '工单详情页面',
                'url'        => 'setting.work-order.details',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 0,
                'icon'       => 'fa-list-ul',
                'sort'       => '6',
                'item'       => 'operation_log',
                'parents'    => ['system',],
            ],
        ]
    ],
//    'log_viewer'      => [
//        'name'              => '系统日志',
//        'url'               => 'developer.log-viewer',
//        'url_params'        => '',
//        'permit'            => 1,
//        'menu'              => 1,
//        'icon'              => 'fa-history',
//        'sort'              => '5',
//        'item'              => 'log-viewer',
//        'parents'           => ['system',],
//    ],
    /*    'shop_upgrade'      => [
            'name'              => '系统升级',
            'url'               => 'update.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => 'fa-history',
            'sort'              => '5',
            'item'              => 'shop_upgrade',
            'parents'           => ['system',],
        ]*/
];
