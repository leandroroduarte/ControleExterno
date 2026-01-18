#!/bin/bash
# Script para criar migration e executar aplicação

echo "🔧 Criando migration do banco de dados..."
dotnet ef migrations add InitialCreate

echo "✅ Migration criada com sucesso!"
echo ""
echo "🚀 Para executar a aplicação:"
echo "   dotnet run"
