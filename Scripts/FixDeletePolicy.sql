-- Script para configurar DELETE policy corretamente no Supabase

-- Deletar policies antigas (se existirem)
DROP POLICY IF EXISTS "excluir público 1c4oq5_1" ON storage.objects;
DROP POLICY IF EXISTS "excluir público 1c4oq5_2" ON storage.objects;
DROP POLICY IF EXISTS "public-delete" ON storage.objects;

-- Criar nova policy de DELETE
CREATE POLICY "public-delete" ON storage.objects
  FOR DELETE TO public
  USING (bucket_id = 'productos-imagens');

-- Verificar se a policy foi criada
SELECT * FROM pg_policies 
WHERE tablename = 'objects' AND schemaname = 'storage' 
ORDER BY policyname;
