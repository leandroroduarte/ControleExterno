@echo off
cls
echo.
echo ======================================
echo   Sistema de Cadastro de Usuários
echo   Servidor iniciando...
echo ======================================
echo.

cd /d "c:\laragon\www\projetoCsharp\CadastroUsuarios"
dotnet .\bin\Release\net8.0\CadastroUsuarios.dll

echo.
echo ======================================
echo   Servidor encerrado
echo ======================================
echo.
pause
