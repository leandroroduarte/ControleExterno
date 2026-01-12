<?php
// editar_produto.php - Editar produto
session_start();
if(!isset($_SESSION['user_id'])){ header('Location:index.php'); exit; }
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_nome'];
$id = intval($_GET['id'] ?? 0);
$errors = [];
$success = '';
$produto = null;
$fornecedor_name = '';

if ($id <= 0) {
  $errors[] = 'ID inválido.';
} else {
  try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=ControleExterno;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare('SELECT * FROM Produtos WHERE id=? AND user_id=?');
    $stmt->execute([$id, $user_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$produto) {
      $errors[] = 'Produto não encontrado.';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $produto) {
      $nome = trim($_POST['nome'] ?? '');
      $descricao = trim($_POST['descricao'] ?? '');
      $preco = trim($_POST['preco'] ?? '');
      $quantidade = trim($_POST['quantidade'] ?? '0');
      $fornecedor_id = intval($_POST['fornecedor_id'] ?? 0);

      if ($nome === '') $errors[] = 'Nome é obrigatório.';
      if ($preco === '' || !is_numeric($preco) || $preco < 0) $errors[] = 'Preço inválido.';
      if (!is_numeric($quantidade) || $quantidade < 0) $errors[] = 'Quantidade inválida.';

      $imagem = $produto['imagem'];
      if ($_FILES['imagem']['name'] !== '') {
        if ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
          $errors[] = 'Erro ao fazer upload da imagem.';
        } else {
          $tipos_permitidos = ['image/jpeg','image/png','image/gif','image/webp'];
          if (!in_array($_FILES['imagem']['type'], $tipos_permitidos, true)) {
            $errors[] = 'Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WEBP.';
          } elseif ($_FILES['imagem']['size'] > 5242880) {
            $errors[] = 'Arquivo muito grande. Máximo 5MB.';
          } else {
            if (!is_dir('uploads')) { mkdir('uploads', 0755, true); }
            if ($produto['imagem'] && file_exists('uploads/'.$produto['imagem'])) { unlink('uploads/'.$produto['imagem']); }
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $imagem = 'produto_' . $user_id . '_' . time() . '.' . $ext;
            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/' . $imagem)) {
              $errors[] = 'Erro ao salvar a imagem.';
              $imagem = $produto['imagem'];
            }
          }
        }
      }

      if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE Produtos SET nome=?,descricao=?,preco=?,quantidade=?,imagem=?,fornecedor_id=? WHERE id=? AND user_id=?');
        $stmt->execute([$nome, $descricao?:null, floatval($preco), intval($quantidade), $imagem?:null, $fornecedor_id ?: null, $id, $user_id]);
        $success = 'Produto atualizado com sucesso!';
        $stmt = $pdo->prepare('SELECT * FROM Produtos WHERE id=? AND user_id=?');
        $stmt->execute([$id, $user_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
      }
    }

    // buscar nome do fornecedor atual, se houver
    if ($produto && !empty($produto['fornecedor_id'])) {
      $stmtf = $pdo->prepare('SELECT nome FROM Fornecedores WHERE id = ? AND user_id = ?');
      $stmtf->execute([$produto['fornecedor_id'], $user_id]);
      $ff = $stmtf->fetch(PDO::FETCH_ASSOC);
      $fornecedor_name = $ff['nome'] ?? '';
    }

  } catch (PDOException $e) {
    $errors[] = 'Erro no banco: '.$e->getMessage();
  }
}

if (!$produto) { $produto = ['nome'=>$_POST['nome']??'','descricao'=>$_POST['descricao']??'','preco'=>$_POST['preco']??'','quantidade'=>$_POST['quantidade']??'0','imagem'=>'','fornecedor_id'=>0]; }
?>
<!doctype html>
<html lang="pt-br">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Editar Produto</title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Segoe UI,Tahoma,Verdana;background:#f5f7fa;color:#333}.navbar{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:15px 30px;display:flex;justify-content:space-between}.container{max-width:800px;margin:30px auto;padding:0 20px}.form-card{background:#fff;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.05)}label{display:block;margin-bottom:8px;font-weight:600}input,textarea,select{width:100%;padding:12px;border:1px solid #ddd;border-radius:5px}textarea{resize:vertical;min-height:100px}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}.form-actions{display:flex;gap:10px;margin-top:30px}.btn-submit{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:12px 24px;border-radius:5px;cursor:pointer;border:none;font-weight:600}.btn-submit:hover{opacity:0.9}.error{background:#ffe6e6;padding:12px;border-radius:6px;margin-bottom:20px;color:#c33}.success{background:#e6ffe6;padding:12px;border-radius:6px;margin-bottom:20px;color:#333}.image-preview{margin-top:10px;max-width:300px;border-radius:5px}.file-input-label{display:inline-block;padding:10px 16px;background:#667eea;color:#fff;border-radius:5px;cursor:pointer;margin-top:10px}.file-input-label:hover{background:#5568d3}input[type="file"]{display:none}input[type="number"]{-moz-appearance:textfield}input[type="number"]::-webkit-outer-spin-button,input[type="number"]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}@media(max-width:768px){.form-row{grid-template-columns:1fr}.container{margin:20px auto;padding:0 15px}.form-card{padding:20px}input,textarea,select{font-size:16px}label{font-size:14px}}
</style></head>
<body>
  <div class="navbar"><h1>🔐 Controle Externo</h1><div><?php echo htmlspecialchars($user_name); ?> <a href="produtos.php" style="margin-left:10px;color:#fff">Voltar</a></div></div>
  <div class="container"><div class="form-card">
    <h2>✏️ Editar Produto</h2>
    <?php if(!empty($errors)){ echo '<div class="error"><ul>'; foreach($errors as $err) echo '<li>'.htmlspecialchars($err).'</li>'; echo '</ul></div>'; } ?>
    <?php if($success){ echo '<div class="success">'.htmlspecialchars($success).'<br><br><a href="produtos.php" style="color:#667eea;font-weight:600">Voltar para lista de produtos</a></div>'; } ?>
    <?php if($produto && empty($errors) && !$success): ?>
    <form method="POST" enctype="multipart/form-data">
      <label for="nome">Nome do Produto *</label>
      <input type="text" id="nome" name="nome" maxlength="255" required value="<?php echo htmlspecialchars($produto['nome']); ?>">
      
      <label for="descricao">Descrição</label>
      <textarea id="descricao" name="descricao" maxlength="1000"><?php echo htmlspecialchars($produto['descricao'] ?? ''); ?></textarea>
      
      <div class="form-row">
        <div>
          <label for="preco">Preço *</label>
          <input type="number" id="preco" name="preco" step="0.01" min="0" required value="<?php echo htmlspecialchars($produto['preco']); ?>">
        </div>
        <div>
          <label for="quantidade">Quantidade</label>
          <input type="number" id="quantidade" name="quantidade" min="0" value="<?php echo htmlspecialchars($produto['quantidade']); ?>">
        </div>
      </div>
      
      <label>Imagem Atual</label>
      <?php if($produto['imagem'] && file_exists('uploads/'.$produto['imagem'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="image-preview" style="max-width:300px">
      <?php else: ?>
        <div style="padding:30px;background:#f5f7fa;border-radius:5px;text-align:center;color:#999">Sem imagem</div>
      <?php endif; ?>
      
      <label for="imagem" style="margin-top:20px">Nova Imagem (deixe em branco para manter a atual)</label>
      <input type="file" id="imagem" name="imagem" accept="image/*">
      <label for="imagem" class="file-input-label">📷 Escolher Imagem</label>
      <img id="preview" class="image-preview" style="display:none;">

      <label for="fornecedor_search" style="margin-top:12px">Fornecedor</label>
      <div class="suggestions" style="position:relative">
        <input type="text" id="fornecedor_search" name="fornecedor_name" placeholder="Digite para buscar fornecedor..." autocomplete="off" value="<?php echo htmlspecialchars($fornecedor_name ?? ''); ?>">
        <input type="hidden" id="fornecedor_id" name="fornecedor_id" value="<?php echo htmlspecialchars($produto['fornecedor_id'] ?? ''); ?>">
        <div id="fornecedor_suggestions" class="suggestions-list" style="display:none"></div>
      </div>
      
      <div style="margin-top:30px;display:flex;gap:10px">
        <button type="submit" class="btn-submit">✅ Atualizar</button>
        <a href="produtos.php" style="padding:12px 24px;border-radius:5px;background:#999;color:#fff;text-decoration:none;font-weight:600;display:inline-block">Cancelar</a>
      </div>
    </form>
    <?php endif; ?>
  </div>
  </div>
  <script>
    const imagemInput=document.getElementById('imagem');
    const preview=document.getElementById('preview');
    imagemInput.addEventListener('change',function(e){
      const file=e.target.files[0];
      if(file){
        const reader=new FileReader();
        reader.onload=function(event){
          preview.src=event.target.result;
          preview.style.display='block';
        };
        reader.readAsDataURL(file);
      }
    });
    // Autocomplete fornecedores (mostra todos ao focar/clicar e filtra ao digitar)
    const fornecedorSearch = document.getElementById('fornecedor_search');
    const fornecedorIdEl = document.getElementById('fornecedor_id');
    const fornecedorSuggestions = document.getElementById('fornecedor_suggestions');
    let fornecedorTimer = null;

    function fetchFornecedoresEdit(q) {
      fetch('fornecedores_busca.php?q='+encodeURIComponent(q)).then(r=>r.json()).then(list=>{
        fornecedorSuggestions.innerHTML='';
        if(!list || list.length===0){ fornecedorSuggestions.style.display='none'; return; }
        list.forEach(function(item){
          const div = document.createElement('div');
          div.className='suggestion-item';
          div.textContent = item.nome;
          div.dataset.id = item.id;
          div.addEventListener('click', function(){
            fornecedorSearch.value = this.textContent;
            fornecedorIdEl.value = this.dataset.id;
            fornecedorSuggestions.style.display='none';
          });
          fornecedorSuggestions.appendChild(div);
        });
        fornecedorSuggestions.style.display='block';
      }).catch(()=>{ fornecedorSuggestions.style.display='none'; });
    }

    fornecedorSearch && fornecedorSearch.addEventListener('input', function(e){
      const q = this.value.trim();
      fornecedorIdEl.value = '';
      if(fornecedorTimer) clearTimeout(fornecedorTimer);
      fornecedorTimer = setTimeout(function(){ fetchFornecedoresEdit(q); }, 200);
    });

    ['focus','click'].forEach(evt => { fornecedorSearch && fornecedorSearch.addEventListener(evt, function(e){ const q = this.value.trim(); fetchFornecedoresEdit(q); }); });
    document.addEventListener('click', function(e){ if(!e.target.closest('.suggestions') && e.target !== fornecedorSearch){ fornecedorSuggestions.style.display='none'; } });
  </script>
</body>
</html>
