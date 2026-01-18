@echo off
REM Script para Windows - Criar migration e executar aplicação

echo Criando migration do banco de dados...
dotnet ef migrations add InitialCreate

echo.
echo Migration criada com sucesso!
echo.
echo Para executar a aplicacao:
echo    dotnet run
pause
