﻿title ������ ��娢 ��⠭���� �������
@echo off
cls

echo Script written by Maxim Harder
echo (c) devcraft.club, 2020-2021

rem ����� �������� ������ 蠡���� � ���� �� 7zip
set /p template="�������� 蠡���� (�� 㬮�砭��: Default): " || set template=Default
set PATH=%PATH%;%ProgramFiles%\7-Zip\
set plugin=dle_favall

rem ������ �६����� �����, � ���ன �㤥� ࠡ���� � ������� �㤠 ���
mkdir temp
robocopy upload temp /E

rem �室�� �� �६����� �����
cd temp

rem ��२�����뢠�� ����� � 蠡����� � �����頥��� ���⭮ �� �६����� �����
cd templates
ren  Default %template%
rename  Default %template%
cd ..

rem ��娢��㥬 ��� � ��娢
echo ��稭��� ��娢���
7z a -mx0 -r -tzip -aoa %plugin%.zip *

rem �����頥��� � ��७� � �����㥬 �㤠 ��娢
cd ..
copy /Y temp\%plugin%.zip plugin.zip

rem ����塞 �६����� �����
rd /s /q temp

rem �����稢���
exit;
