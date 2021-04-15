# DLE FavAll
Пользовательские закладки

![Версия модуля](https://img.shields.io/github/manifest-json/v/Gokujo/dle_favall?color=success&filename=manifest.json&label=%D0%92%D0%B5%D1%80%D1%81%D0%B8%D1%8F&style=flat-square)![Версия DLE](https://img.shields.io/badge/dynamic/json?color=orange&label=DLE&query=dle&url=https%3A%2F%2Fraw.githubusercontent.com%2FGokujo%2Fdle_favall%2Fmain%2Fmanifest.json&style=flat-square)

![Авторство](https://img.shields.io/badge/dynamic/json?color=blue&label=Автор&query=author&url=https%3A%2F%2Fraw.githubusercontent.com%2FGokujo%2Fdle_favall%2Fmain%2Fmanifest.json&style=flat-square)


## Описание:
- Создание закладок в админпанеле по умолчанию
- Перестроение закладок
- Просмотр закладок всех пользователей
- Пользователю доступно создание своих закладок
- Перестроение своих закладок

## Скриншоты
![screenshot 2021-04-15 001.png](screens/screenshot%202021-04-15%20001.png)![screenshot 2021-04-15 001.png](screens/screenshot%202021-04-15%20001.png)
![screenshot 2021-04-15 002.png](screens/screenshot%202021-04-15%20002.png)![screenshot 2021-04-15 003.png](screens/screenshot%202021-04-15%20003.png)![screenshot 2021-04-15 004.png](screens/screenshot%202021-04-15%20004.png)![screenshot 2021-04-15 005.png](screens/screenshot%202021-04-15%20005.png)![screenshot 2021-04-15 006.png](screens/screenshot%202021-04-15%20006.png)![screenshot 2021-04-15 007.png](screens/screenshot%202021-04-15%20007.png)![screenshot 2021-04-15 008.png](screens/screenshot%202021-04-15%20008.png)![screenshot 2021-04-15 009.png](screens/screenshot%202021-04-15%20009.png)![screenshot 2021-04-15 010.png](screens/screenshot%202021-04-15%20010.png)![screenshot 2021-04-15 011.png](screens/screenshot%202021-04-15%20011.png)![screenshot 2021-04-15 012.png](screens/screenshot%202021-04-15%20012.png)![screenshot 2021-04-15 013.png](screens/screenshot%202021-04-15%20013.png)![screenshot 2021-04-15 014.png](screens/screenshot%202021-04-15%20014.png)![screenshot 2021-04-15 015.png](screens/screenshot%202021-04-15%20015.png)![screenshot 2021-04-15 016.png](screens/screenshot%202021-04-15%20016.png)


# Установка
## Файл .htaccess
**Найти**

```
RewriteRule ^favorites/page/([0-9]+)(/?)+$ index.php?do=favorites&cstart=$1 [L]
```

**Ниже**

```
# favall v.4.5.0
RewriteRule ^favall/([0-9]+)/user/([^/]*)(/?)+$ index.php?do=favall&list=$1&user=$2 [L]
RewriteRule ^favall/([0-9]+)/user/([^/]*)/page/([0-9]+)(/?)+$ index.php?do=favall&list=$1&user=$2&cstart=$3 [L]
RewriteRule ^favall_manager(/?)+$ index.php?do=favall&manager=manager [L]
RewriteRule ^favall_catalog(/?)+$ index.php?do=favall&catalog=catalog [L]
```

## Шаблоны
### main.tpl
перед тегом `</head>` прописать

```HTML
<link media="screen" href="{THEME}/favall/css/favall.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="{THEME}/favall/js/favall_nestable.js"></script>
<script type="text/javascript" src="{THEME}/favall/js/favall.js"></script>
```

### fullstory.tpl, shortstory.tpl и searchresult.tpl
Прописываем кнопку с закладками

```HTML
<div class="favall_btn" data-favall_open="{news-id}">Избранное</div>
```

### login.tpl, profile_popup.tpl и userinfo.tpl
тег выводит список закладок сгенерированный в /favall/favall_link.tpl
генерация каждой ссылки происходит между соответствуюищими тегами:
- **[login_panel]...[/login_panel]** - для панели авторизованного юзера
- **[profile]...[/profile]** - для профиля
- **[profile_popup]...[/profile_popup]** - для модального окна с информацией о юзере

прописать тег вывода списка закладок: `{favall_link}`

### login.tpl
для авторизованного пользователя ссылку на персональный менеджер вкладок

```HTML
<a href="/favall_manager/">Управление закладками</a>
```


Адрес просмотра всех закладок пользователей
САЙТ.РУ/favall_manager/
