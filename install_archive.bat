title Создаём архив установки плагина
@echo off
cls

echo Script written by Maxim Harder
echo (c) devcraft.club, 2020-2021

rem Задаём название нового шаблона и путь до 7zip
set /p template="Название шаблона (по умолчанию: Default): " || set template=Default
set PATH=%PATH%;%ProgramFiles%\7-Zip\
set plugin=dle_favall

rem Создаём временную папку, в которой будем работать и копирует туда всё
mkdir temp
robocopy upload temp /E

rem Входим во временную папку
cd temp

rem Переименовываем папку с шаблоном и возвращаемся обратно во временную папку
cd templates
ren  Default %template%
rename  Default %template%
cd ..

rem Архивируем всё в архив
echo Начинаем архивацию
7z a -mx0 -r -tzip -aoa %plugin%.zip *

rem Возвращаемся в корень и копируем туда архив
cd ..
copy /Y temp\%plugin%.zip plugin.zip

rem Удаляем временную папку
rd /s /q temp

rem Заканчиваем
exit;
