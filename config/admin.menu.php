<?php
namespace Mf\Navigation;

use Admin\Service\JqGrid\ColModelHelper;
use Admin\Service\JqGrid\NavGridHelper;
use Admin\Service\Zform\RowModelHelper;
use Laminas\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Меню сайта",
        "options" => [
            "container" => "menu",
            "podval" =>"<p><b>Указывается MVC переход ИЛИ прямой переход, MVC переход в приоритете</b></p>",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select t.*,
                        (not EXISTS(select id from menu as st where st.subid=t.id)) as isLeaf
                            from menu as t where subid=:nodeid and locale=':locale' and sysname=':sysname'",
                    "interface_name"=>"menu",
                ],
            ],
             "edit"=>[
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select * from menu",
                    "interface_name"=>"menu",
                ],
                "cache" =>[
                    "tags"=>["menu"],
                    "keys"=>["menu"],
                ],
             ],
             "add"=>[
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select * from menu",
                    "parent_id_field" => "subid",
                    "interface_name"=>"menu",
                ],
                "cache" =>[
                    "tags"=>["menu"],
                    "keys"=>["menu"],
                ],
             ],
             "del"=>[
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select * from menu",
                    "interface_name"=>"menu",
                ],
                "cache" =>[
                    "tags"=>["menu"],
                    "keys"=>["menu"],
                ],
             ],
            
            /*внешний вид*/
            "layout"=>[
                "caption" => "Меню сайта",
                "height" => "auto",
                //"width" => "auto",
                "sortname" => "poz",
                "sortorder" => "asc",
                "hidegrid" => false,
                "treeGrid"=>true,
                "ExpandColumn"=>"label",
                "ExpandColClick"=>true,
               "treeGridModel"=>"adjacency",
                "gridview"=>false,
                
                //"colMenu"  =>  true ,
                /*область перед телом сетки, toolbar
                * все настройки как в Zform
                */
                "toolbar"=> [true,"top"],
                "toolbarModel"=>[
                    "rowModel" => [
                        'elements' => [
                            RowModelHelper::select("locale",[
                                "plugins"=>[
                                    "rowModel"=>[//плагин срабатывает при генерации формы до ее вывода
                                        "Locale"=>[],
                                    ],
                                ],
                                'options'=>[
                                    "label"=>"Локаль: "
                                ],
                                "attributes"=>["onchange"=>"change_toolbar()"]
                            ]),
                            RowModelHelper::select("sysname",[
                                "plugins"=>[
                                    "rowModel"=>[//плагин срабатывает при генерации формы до ее вывода
                                        Service\Admin\Zform\Plugin\MenuNames::class=>[],
                                    ],
                                ],
                                'options'=>[
                                    "label"=>"Имя меню: "
                                ],
                                "attributes"=>["onchange"=>"change_toolbar()"]
                            ]),
                        ],
                    ],
                    "read"=>[//наолняет элементы toolbar начальными значениями
                        Service\Admin\Zform\Plugin\ToolBarInit::class=>[ ],
                    ],
                ],
        
                "treeIcons"=>[
                    "plus"  =>"ui-icon-triangle-1-e",
                    "minus"=>"ui-icon-triangle-1-s",
                    "leaf"=>"ui-icon-bullet",
                ],
                "treeReader"  =>[
                    "parent_id_field" => "subid",
                    "level_field" => "level",
                ], 
                "navgrid" => [
                    "button" => NavGridHelper::Button(["search"=>false,"add"=>true,"edit"=>true,"del"=>true,"refresh"=>true]),
                    "editOptions"=>NavGridHelper::editOptions(["reloadAfterSubmit"=>false,]),
                    "addOptions"=>NavGridHelper::addOptions(["reloadAfterSubmit"=>false,"closeAfterAdd"=>true]),
                    "delOptions"=>NavGridHelper::delOptions(),
                ],
                "colModel" => [
                    ColModelHelper::text("label",
                                         [
                                             "label"=>"Имя",
                                             "width"=>250,
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),
                    ColModelHelper::select("mvc",["label"=>"MVC Переход",
                                                  "width"=>"300",
                                                  "plugins"=>[
                                                      "colModel"=>["GetMvcUrls"=>[]], //вывод в форматтере select
                                                      //поддерживается только псевдонимы!!!
                                                      "ajaxRead"=>["GetMvcUrls"=>[]], //подгрузка при редактированиив форме
                                                      ],
                                                  ]),
                    ColModelHelper::text("url",
                                         [
                                             "label"=>"Прямой переход",
                                             "width"=>"200",
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),
                    ColModelHelper::text("poz",
                                         [
                                             "label"=>"Порядок",
                                             "width"=>150,
                                         ]),
                    ColModelHelper::options("options",
                                         [
                                             "label"=>"Опции",
                                             "width"=>150,
                                             "hidden" => true,
                                             "editrules"=>[
                                                 "edithidden"=>true,
                                             ],
                                             "editoptions"=>[
                                                 //тип сериализатора serialize или json (по умолчанию)
                                                 'serialize' => 'json',
                                                 /*собственно сами элементы, подобно Laminas формам*/
                                                 "elements"=>[
                                                     [
                                                         'spec' =>[
                                                             "type" => "select",
                                                             "name" => "target",
                                                             'options' => [
                                                                 'label' => 'Цель',
                                                                 'empty_option' => 'По умолчанию',
                                                                 'value_options' => [
                                                                     '_blank' => 'Новое окно',
                                                                     '_top' => 'Главное окно',
                                                                     '_self' => 'Текущее окно',
                                                                     '_parent' => 'Родительское окно',
                                                                 ],
                                                             ],
                                                         ],
                                                     ],
                                                     
                                                     [
                                                         'spec' =>[
                                                             "type" => "text",
                                                             "name" => "li_class",
                                                             'options' => [
                                                                 'label' => 'Класс CSS в <li>',
                                                             ],
                                                             'attributes'=> [
                                                             ],
                                                         ],
                                                     ],
                                                     [
                                                         'spec' =>[
                                                             "type" => "text",
                                                             "name" => "a_class",
                                                             'options' => [
                                                                 'label' => 'Класс CSS в <a>',
                                                             ],
                                                             'attributes'=> [
                                                             ],
                                                         ],
                                                     ],
                                                     [
                                                        'spec' =>[
                                                            "type" => "checkbox",
                                                            "name" => "header",
                                                            'options' => [
                                                                'label' => 'Заголовок/разделитель',
                                                            ],
                                                        ],
                                                    ],

                                                 ],
                                             ],
                                         ]),

                    ColModelHelper::hidden("id"),
                    ColModelHelper::cellActions("myaction",["formatoptions"=>["editformbutton"=>true]]),
                ],
            ],
        ],
];