# Генератор разных навигационных элементов на сайте


Добавьте в конфиг приложения:
```php
"menu"=>[
                "MenuUp"=>'Меню в верху',
                "MenuDown"=>"Нижнее меню"
        ],
```
Используется как хелпер для view (в скрипте вида):
```php
/*по умолчанию меню генерируется стандатным помощником ZF3, генерирует меню с именем test*/
echo $this->menu("test");

/*генерирует меню по стандарту bootstrap4 - параметры все по умолчанию, см. файл помощника, там есть дефолтные параметры*/
echo $this->menu("test",[  "bootstrap4"=>[]   ]);


/*генерирует меню по стандарту bootstrap4 - параметры все по умолчанию, кроме ulClass, ему присваивается новое значение*/
echo $this->menu("test",[  "bootstrap4"=>["ulClass"=>"nav ulclass"]   ]);

```
Опции (ключи массива):
смотрите дефолтные параметры внутри помощника (файл menu.php):
передаются как есть
```php
        "zf3"=>[/*стандартные параметры для встроенного в ZF3 генератора меню*/
            "locale"=>"ru_RU",               //имя локали
            "ulClass"=>"navigation",         //класс для ul элемента (сдля стандартного ZEND меню)
            "indent"=>"",
            "minDepth"=>0,                   //минимальный уровень вывода
            "maxDepth"=>null,                //максимальный уровень
            "liActiveClass"=>"active",       //имя класса для активного пункта
            "escapeLabels"=>true,            //экранировать метки да/нет
            "addClassToListItem"=>false,
            "OnlyActiveBranch"=>false,
            "tpl"=>null,
        ],
        "bootstrap4"=>[ /*параметры для меню стандарта bootstrap4 (без логотипа и поисковой формы)*/
            "locale"=>"ru_RU",               //имя локали
            "ulClass"=>"nav",                //класс для ul элемента
            "indent"=>"",                    //идентификатор, обязательно если несколько меню на сайте
            "minDepth"=>0,                   //минимальный уровень вывода
            "maxDepth"=>null,                //максимальный уровень
            "liActiveClass"=>"active",       //имя класса для активного пункта
            "escapeLabels"=>true,            //экранировать метки да/нет
            "addClassToListItem"=>false,
            "OnlyActiveBranch"=>false,
            "tpl"=>null,
        ],


```

Для работы с базой в конфиге приложения должно быть объявлено DefaultSystemDb:
```php
......
    "databases"=>[
        //соединение с базой + имя драйвера
        'DefaultSystemDb' => [
            'driver'=>'MysqlPdo',
            //"unix_socket"=>"/tmp/mysql.sock",
            "host"=>"localhost",
            'login'=>"root",
            "password"=>"**********",
            "database"=>"simba4",
            "locale"=>"ru_RU",
            "character"=>"utf8"
        ],
    ],
.....
```
для работы с кешем аналогично:
```php
.....
    'caches' => [
        'DefaultSystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    // Store cached data in this directory.
                    'cache_dir' => './data/cache',
                    // Store cached data for 3 hour.
                    'ttl' => 60*60*2 
                ],
            ],
            'plugins' => [
                [
                    'name' => Serializer::class,
                    'options' => [
                    ],
                ],
            ],
        ],
    ],
.....
```

