@echo off
:loop
php path\to\your\project\bin\console payment:check-status
timeout /t 60
goto loop