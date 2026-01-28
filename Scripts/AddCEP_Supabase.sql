-- Script para adicionar coluna CEP nas tabelas do Supabase PostgreSQL
-- Executar no SQL Editor do Supabase

-- Adicionar coluna CEP na tabela clientes
ALTER TABLE clientes
ADD COLUMN cep VARCHAR(10) NOT NULL DEFAULT '';

-- Adicionar coluna CEP na tabela fornecedores
ALTER TABLE fornecedores
ADD COLUMN cep VARCHAR(10) NOT NULL DEFAULT '';

-- Remover o padrão para futuras inserções (opcional)
ALTER TABLE clientes
ALTER COLUMN cep DROP DEFAULT;

ALTER TABLE fornecedores
ALTER COLUMN cep DROP DEFAULT;

-- Verificar se as colunas foram adicionadas
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name IN ('clientes', 'fornecedores') 
AND column_name = 'cep';
