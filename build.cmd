:: Composer ��װ���½ű�
@echo off
title Composer Plugs Install And Optimize
echo.
echo ========= 1. �����Ѱ�װ��� =========
rmdir /s/q vendor thinkphp runtime
echo.
echo ========= 2. ���ز���װ��� =========
composer update --profile --prefer-dist --optimize-autoloader
echo.
echo ========= 3. ѹ����������� =========
composer dump-autoload --optimize
exit