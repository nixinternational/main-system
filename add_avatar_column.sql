-- Script SQL para adicionar a coluna avatar à tabela users
-- Execute este script diretamente no banco de dados de produção

ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL;

-- Verificar se a coluna foi criada
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'users' AND column_name = 'avatar';

