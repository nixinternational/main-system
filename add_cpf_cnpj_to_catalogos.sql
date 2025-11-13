-- Script SQL para adicionar a coluna cpf_cnpj à tabela catalogos
-- Execute este script diretamente no banco de dados de produção

ALTER TABLE catalogos ADD COLUMN IF NOT EXISTS cpf_cnpj VARCHAR(255) NULL;

-- Verificar se a coluna foi criada
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'catalogos' AND column_name = 'cpf_cnpj';

