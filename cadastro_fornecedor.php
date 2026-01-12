<?php
// cadastro_fornecedor.php - Cadastro de novo fornecedor
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$user_id = $_SESSION['user_id']; $user_name = $_SESSION['user_nome'];
$errors = []; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $cpf_cnpj = trim($_POST['cpf_cnpj'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    if ($nome === '') $errors[] = 'Nome é obrigatório.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if (empty($errors)) {
        try {
            $host='127.0.0.1'; $port=3306; $db='ControleExterno'; $user='root'; $pass='';
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
            $stmt = $pdo->prepare('INSERT INTO Fornecedores (nome,email,telefone,cpf_cnpj,cep,endereco,numero,complemento,bairro,cidade,estado,user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$nome,$email?:null,$telefone?:null,$cpf_cnpj?:null,$cep?:null,$endereco?:null,$numero?:null,$complemento?:null,$bairro?:null,$cidade?:null,$estado?:null,$user_id]);
            $success = 'Fornecedor cadastrado com sucesso!';
            $nome = $email = $telefone = $cpf_cnpj = $cep = $endereco = $numero = $complemento = $bairro = $cidade = $estado = '';
        } catch (PDOException $e) { $errors[] = 'Erro ao salvar fornecedor: '.$e->getMessage(); }
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cadastrar Fornecedor</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;background:#f5f7fa;color:#333}
    .navbar{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between}
    .container{max-width:800px;margin:30px auto;padding:0 20px}
    .form-card{background:white;border-radius:10px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.05)}
    label{display:block;margin-bottom:8px;font-weight:600}
    input,select{width:100%;padding:12px;border:1px solid #ddd;border-radius:5px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}
    .form-actions{display:flex;gap:10px;margin-top:30px}
    .btn-submit{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:12px 24px;border-radius:5px}
    @media (max-width: 768px) {
      .form-row{grid-template-columns:1fr}
      .container{margin:20px auto;padding:0 15px}
      .form-card{padding:20px}
      input,select{font-size:16px}
      label{font-size:14px}
    }
  </style>
</head>
<body>
  <div class="navbar"><h1>🔐 Controle Externo</h1><div><?php echo htmlspecialchars($user_name); ?> <a href="fornecedores.php" style="margin-left:10px;color:#fff">Voltar</a></div></div>
  <div class="container">
    <div class="form-card">
      <h2>➕ Cadastrar Novo Fornecedor</h2>
      <?php if(!empty($errors)){ echo '<div style="background:#ffe6e6;padding:12px;border-radius:6px;margin-bottom:20px;color:#c33"><ul>'; foreach($errors as $err) echo '<li>'.htmlspecialchars($err).'</li>'; echo '</ul></div>'; } ?>
      <?php if($success){ echo '<div style="background:#e6ffe6;padding:12px;border-radius:6px;margin-bottom:20px;color:#333">'.htmlspecialchars($success).'<br><br><a href="fornecedores.php" style="color:#667eea;font-weight:600">Ver lista de fornecedores</a></div>'; } ?>
      <form method="POST">
        <label for="nome">Nome *</label>
        <input type="text" id="nome" name="nome" maxlength="255" required value="<?php echo htmlspecialchars($nome ?? ''); ?>">
        <div class="form-row">
          <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="255" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="seu@exemplo.com" autocomplete="email">
          </div>
          <div>
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" maxlength="20" inputmode="tel" placeholder="(11) 99999-9999" value="<?php echo htmlspecialchars($telefone ?? ''); ?>">
          </div>
        </div>
        <label for="cpf_cnpj">CPF/CNPJ</label>
        <input type="text" id="cpf_cnpj" name="cpf_cnpj" maxlength="20" placeholder="000.000.000-00 ou 00.000.000/0000-00" value="<?php echo htmlspecialchars($cpf_cnpj ?? ''); ?>">
        <label for="cep">CEP</label>
        <div style="display:flex;gap:10px"><input type="text" id="cep" name="cep" maxlength="10" placeholder="00000-000" value="<?php echo htmlspecialchars($cep ?? ''); ?>" style="flex:1" inputmode="numeric"><button type="button" id="btn-buscar-cep" style="background:#667eea;color:#fff;padding:12px;border:none;border-radius:5px">Buscar</button></div>
        <div style="margin-top:12px"> 
          <label for="endereco">Endereço</label>
          <input type="text" id="endereco" name="endereco" maxlength="255" value="<?php echo htmlspecialchars($endereco ?? ''); ?>">
        </div>
        <div class="form-row">
          <div><label for="numero">Número</label><input type="text" id="numero" name="numero" maxlength="10" value="<?php echo htmlspecialchars($numero ?? ''); ?>"></div>
          <div><label for="complemento">Complemento</label><input type="text" id="complemento" name="complemento" maxlength="255" value="<?php echo htmlspecialchars($complemento ?? ''); ?>"></div>
        </div>
        <div class="form-row" style="margin-top:12px">
          <div><label for="bairro">Bairro</label><input type="text" id="bairro" name="bairro" maxlength="100" value="<?php echo htmlspecialchars($bairro ?? ''); ?>"></div>
          <div><label for="cidade">Cidade</label><input type="text" id="cidade" name="cidade" maxlength="100" value="<?php echo htmlspecialchars($cidade ?? ''); ?>"></div>
        </div>
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
          <option value="">Selecione um estado</option>
          <option value="AC">Acre</option>
          <option value="AL">Alagoas</option>
          <option value="AP">Amapá</option>
          <option value="AM">Amazonas</option>
          <option value="BA">Bahia</option>
          <option value="CE">Ceará</option>
          <option value="DF">Distrito Federal</option>
          <option value="ES">Espírito Santo</option>
          <option value="GO">Goiás</option>
          <option value="MA">Maranhão</option>
          <option value="MT">Mato Grosso</option>
          <option value="MS">Mato Grosso do Sul</option>
          <option value="MG">Minas Gerais</option>
          <option value="PA">Pará</option>
          <option value="PB">Paraíba</option>
          <option value="PR">Paraná</option>
          <option value="PE">Pernambuco</option>
          <option value="PI">Piauí</option>
          <option value="RJ">Rio de Janeiro</option>
          <option value="RN">Rio Grande do Norte</option>
          <option value="RS">Rio Grande do Sul</option>
          <option value="RO">Rondônia</option>
          <option value="RR">Roraima</option>
          <option value="SC">Santa Catarina</option>
          <option value="SP">São Paulo</option>
          <option value="SE">Sergipe</option>
          <option value="TO">Tocantins</option>
        </select>
        <div class="form-actions">
          <button type="submit" class="btn-submit">💾 Salvar Fornecedor</button>
          <a href="fornecedores.php" class="btn btn-cancel">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
  <script>
    function onlyDigits(s){return (s||'').replace(/\D/g,'');}
    const tel=document.getElementById('telefone'); tel&&tel.addEventListener('input',function(e){let v=onlyDigits(e.target.value).slice(0,11);let out='';if(v.length<=2)out=v;else if(v.length<=6)out='('+v.slice(0,2)+') '+v.slice(2);else if(v.length<=10)out='('+v.slice(0,2)+') '+v.slice(2,6)+'-'+v.slice(6);else out='('+v.slice(0,2)+') '+v.slice(2,7)+'-'+v.slice(7);e.target.value=out;});
    const cepEl=document.getElementById('cep'); cepEl&&cepEl.addEventListener('input',function(e){let v=onlyDigits(e.target.value).slice(0,8); if(v.length>5) e.target.value = v.slice(0,5)+'-'+v.slice(5); else e.target.value = v;});
    const cpfEl=document.getElementById('cpf_cnpj'); cpfEl&&cpfEl.addEventListener('input',function(e){let v=onlyDigits(e.target.value).slice(0,14); if(v.length<=11){v=v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/,function(_,a,b,c,d){let s=a||''; if(b) s+='.'+b; if(c) s+='.'+c; if(d) s+='-'+d; return s;});}else{v=v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*/,function(_,a,b,c,d,f){let s=a||''; if(b) s+='.'+b; if(c) s+='.'+c; if(d) s+='/'+d; if(f) s+='-'+f; return s;});} e.target.value=v;});
    document.getElementById('btn-buscar-cep').addEventListener('click',function(){const cep=onlyDigits(document.getElementById('cep').value); if(cep.length!==8){alert('CEP deve ter 8 dígitos');return;}const loading=null; fetch(`https://viacep.com.br/ws/${cep}/json/`).then(r=>r.json()).then(data=>{ if(data.erro){alert('CEP não encontrado');return;} document.getElementById('endereco').value=data.logradouro||''; document.getElementById('bairro').value=data.bairro||''; document.getElementById('cidade').value=data.localidade||''; document.getElementById('estado').value=data.uf||''; }).catch(e=>{console.error(e);alert('Erro ao buscar CEP');});});
  </script>
</body>
</html>