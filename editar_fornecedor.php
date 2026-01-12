<?php
// editar_fornecedor.php - Editar fornecedor
session_start(); if(!isset($_SESSION['user_id'])){header('Location:index.php');exit;} $user_id=$_SESSION['user_id']; $user_name=$_SESSION['user_nome']; $id=intval($_GET['id']??0); $errors=[]; $success=''; $fornecedor=null;
if($id<=0){$errors[]='ID inválido.';} else { try{ $pdo=new PDO('mysql:host=127.0.0.1;port=3306;dbname=ControleExterno;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]); $stmt=$pdo->prepare('SELECT * FROM Fornecedores WHERE id=? AND user_id=?'); $stmt->execute([$id,$user_id]); $fornecedor=$stmt->fetch(PDO::FETCH_ASSOC); if(!$fornecedor) $errors[]='Fornecedor não encontrado.'; if($_SERVER['REQUEST_METHOD']==='POST' && $fornecedor){ $nome=trim($_POST['nome']??''); $email=trim($_POST['email']??''); $telefone=trim($_POST['telefone']??''); $cpf_cnpj=trim($_POST['cpf_cnpj']??''); $cep=trim($_POST['cep']??''); $endereco=trim($_POST['endereco']??''); $numero=trim($_POST['numero']??''); $complemento=trim($_POST['complemento']??''); $bairro=trim($_POST['bairro']??''); $cidade=trim($_POST['cidade']??''); $estado=trim($_POST['estado']??''); if($nome==='') $errors[]='Nome é obrigatório.'; if($email!=='' && !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Email inválido.'; if(empty($errors)){ $stmt=$pdo->prepare('UPDATE Fornecedores SET nome=?,email=?,telefone=?,cpf_cnpj=?,cep=?,endereco=?,numero=?,complemento=?,bairro=?,cidade=?,estado=? WHERE id=? AND user_id=?'); $stmt->execute([$nome,$email?:null,$telefone?:null,$cpf_cnpj?:null,$cep?:null,$endereco?:null,$numero?:null,$complemento?:null,$bairro?:null,$cidade?:null,$estado?:null,$id,$user_id]); $success='Fornecedor atualizado com sucesso!'; $stmt=$pdo->prepare('SELECT * FROM Fornecedores WHERE id=? AND user_id=?'); $stmt->execute([$id,$user_id]); $fornecedor=$stmt->fetch(PDO::FETCH_ASSOC); } } } catch(PDOException $e){ $errors[]='Erro no banco: '.$e->getMessage(); } }
if(!$fornecedor){ $fornecedor=['nome'=>$_POST['nome']??'','email'=>$_POST['email']??'','telefone'=>$_POST['telefone']??'','cpf_cnpj'=>$_POST['cpf_cnpj']??'','cep'=>$_POST['cep']??'','endereco'=>$_POST['endereco']??'','numero'=>$_POST['numero']??'','complemento'=>$_POST['complemento']??'','bairro'=>$_POST['bairro']??'','cidade'=>$_POST['cidade']??'','estado'=>$_POST['estado']??'']; }
?>
<!doctype html>
<html lang="pt-br">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Editar Fornecedor</title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Segoe UI, Tahoma, Verdana;background:#f5f7fa;color:#333}.navbar{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:15px 30px;display:flex;justify-content:space-between}.container{max-width:800px;margin:30px auto;padding:0 20px}.form-card{background:#fff;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.05)}label{display:block;margin-bottom:8px;font-weight:600}input,select{width:100%;padding:12px;border:1px solid #ddd;border-radius:5px} .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}.form-actions{display:flex;gap:10px;margin-top:30px}.btn-submit{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:12px 24px;border-radius:5px}.errors{background:#ffe6e6;padding:12px;border-radius:6px;margin-bottom:20px;color:#c33}.success{background:#e6ffe6;padding:12px;border-radius:6px;margin-bottom:20px;color:#333}
@media (max-width: 768px) {.form-row{grid-template-columns:1fr}.container{margin:20px auto;padding:0 15px}.form-card{padding:20px}input,select{font-size:16px}label{font-size:14px}}
</style></head>
<body>
  <div class="navbar"><h1>🔐 Controle Externo</h1><div><?php echo htmlspecialchars($user_name); ?> <a href="fornecedores.php" style="margin-left:10px;color:#fff">Voltar</a></div></div>
  <div class="container"><div class="form-card">
    <h2>✏️ Editar Fornecedor</h2>
    <?php if(!empty($errors)){ echo '<div class="errors"><ul>'; foreach($errors as $err) echo '<li>'.htmlspecialchars($err).'</li>'; echo '</ul></div>'; } ?>
    <?php if($success){ echo '<div class="success">'.htmlspecialchars($success).'<br><br><a href="fornecedores.php" style="color:#667eea;font-weight:600">Voltar para lista de fornecedores</a></div>'; } ?>
    <?php if($fornecedor && empty($errors) && !$success): ?>
    <form method="POST">
      <label for="nome">Nome *</label>
      <input type="text" id="nome" name="nome" maxlength="255" required value="<?php echo htmlspecialchars($fornecedor['nome']); ?>">
      <div class="form-row"><div><label for="email">Email</label><input type="email" id="email" name="email" maxlength="255" value="<?php echo htmlspecialchars($fornecedor['email'] ?? ''); ?>" placeholder="seu@exemplo.com" autocomplete="email"></div><div><label for="telefone">Telefone</label><input type="tel" id="telefone" name="telefone" maxlength="20" inputmode="tel" placeholder="(11) 99999-9999" value="<?php echo htmlspecialchars($fornecedor['telefone'] ?? ''); ?>"></div></div>
      <label for="cpf_cnpj">CPF/CNPJ</label>
      <input type="text" id="cpf_cnpj" name="cpf_cnpj" maxlength="20" placeholder="000.000.000-00 ou 00.000.000/0000-00" value="<?php echo htmlspecialchars($fornecedor['cpf_cnpj'] ?? ''); ?>">
      <label for="cep">CEP</label>
      <div style="display:flex;gap:10px"><input type="text" id="cep" name="cep" maxlength="10" placeholder="00000-000" value="<?php echo htmlspecialchars($fornecedor['cep'] ?? ''); ?>" style="flex:1" inputmode="numeric"><button type="button" id="btn-buscar-cep" style="background:#667eea;color:#fff;padding:12px;border:none;border-radius:5px">Buscar</button></div>
      <div style="margin-top:12px"><label for="endereco">Endereço</label><input type="text" id="endereco" name="endereco" maxlength="255" value="<?php echo htmlspecialchars($fornecedor['endereco'] ?? ''); ?>"></div>
      <div class="form-row" style="margin-top:12px"><div><label for="numero">Número</label><input type="text" id="numero" name="numero" maxlength="10" value="<?php echo htmlspecialchars($fornecedor['numero'] ?? ''); ?>"></div><div><label for="complemento">Complemento</label><input type="text" id="complemento" name="complemento" maxlength="255" value="<?php echo htmlspecialchars($fornecedor['complemento'] ?? ''); ?>"></div></div>
      <div class="form-row" style="margin-top:12px"><div><label for="bairro">Bairro</label><input type="text" id="bairro" name="bairro" maxlength="100" value="<?php echo htmlspecialchars($fornecedor['bairro'] ?? ''); ?>"></div><div><label for="cidade">Cidade</label><input type="text" id="cidade" name="cidade" maxlength="100" value="<?php echo htmlspecialchars($fornecedor['cidade'] ?? ''); ?>"></div></div>
      <label for="estado">Estado</label>
      <select id="estado" name="estado"><option value="">Selecione um estado</option>
      <option value="AC" <?php echo ($fornecedor['estado']==='AC'?'selected':'');?>>Acre</option>
      <option value="AL" <?php echo ($fornecedor['estado']==='AL'?'selected':'');?>>Alagoas</option>
      <option value="AP" <?php echo ($fornecedor['estado']==='AP'?'selected':'');?>>Amapá</option>
      <option value="AM" <?php echo ($fornecedor['estado']==='AM'?'selected':'');?>>Amazonas</option>
      <option value="BA" <?php echo ($fornecedor['estado']==='BA'?'selected':'');?>>Bahia</option>
      <option value="CE" <?php echo ($fornecedor['estado']==='CE'?'selected':'');?>>Ceará</option>
      <option value="DF" <?php echo ($fornecedor['estado']==='DF'?'selected':'');?>>Distrito Federal</option>
      <option value="ES" <?php echo ($fornecedor['estado']==='ES'?'selected':'');?>>Espírito Santo</option>
      <option value="GO" <?php echo ($fornecedor['estado']==='GO'?'selected':'');?>>Goiás</option>
      <option value="MA" <?php echo ($fornecedor['estado']==='MA'?'selected':'');?>>Maranhão</option>
      <option value="MT" <?php echo ($fornecedor['estado']==='MT'?'selected':'');?>>Mato Grosso</option>
      <option value="MS" <?php echo ($fornecedor['estado']==='MS'?'selected':'');?>>Mato Grosso do Sul</option>
      <option value="MG" <?php echo ($fornecedor['estado']==='MG'?'selected':'');?>>Minas Gerais</option>
      <option value="PA" <?php echo ($fornecedor['estado']==='PA'?'selected':'');?>>Pará</option>
      <option value="PB" <?php echo ($fornecedor['estado']==='PB'?'selected':'');?>>Paraíba</option>
      <option value="PR" <?php echo ($fornecedor['estado']==='PR'?'selected':'');?>>Paraná</option>
      <option value="PE" <?php echo ($fornecedor['estado']==='PE'?'selected':'');?>>Pernambuco</option>
      <option value="PI" <?php echo ($fornecedor['estado']==='PI'?'selected':'');?>>Piauí</option>
      <option value="RJ" <?php echo ($fornecedor['estado']==='RJ'?'selected':'');?>>Rio de Janeiro</option>
      <option value="RN" <?php echo ($fornecedor['estado']==='RN'?'selected':'');?>>Rio Grande do Norte</option>
      <option value="RS" <?php echo ($fornecedor['estado']==='RS'?'selected':'');?>>Rio Grande do Sul</option>
      <option value="RO" <?php echo ($fornecedor['estado']==='RO'?'selected':'');?>>Rondônia</option>
      <option value="RR" <?php echo ($fornecedor['estado']==='RR'?'selected':'');?>>Roraima</option>
      <option value="SC" <?php echo ($fornecedor['estado']==='SC'?'selected':'');?>>Santa Catarina</option>
      <option value="SP" <?php echo ($fornecedor['estado']==='SP'?'selected':'');?>>São Paulo</option>
      <option value="SE" <?php echo ($fornecedor['estado']==='SE'?'selected':'');?>>Sergipe</option>
      <option value="TO" <?php echo ($fornecedor['estado']==='TO'?'selected':'');?>>Tocantins</option>
      </select>
      <div class="form-actions"><button type="submit" class="btn-submit">💾 Atualizar Fornecedor</button><a href="fornecedores.php" class="btn btn-cancel">Cancelar</a></div>
    </form>
    <?php endif; ?>
  </div></div>
  <script>
    function onlyDigits(s){return (s||'').replace(/\D/g,'');}
    const telefoneEl=document.getElementById('telefone'); telefoneEl&&telefoneEl.addEventListener('input',function(e){let v=onlyDigits(e.target.value).slice(0,11);let out='';if(v.length<=2)out=v;else if(v.length<=6)out='('+v.slice(0,2)+') '+v.slice(2);else if(v.length<=10)out='('+v.slice(0,2)+') '+v.slice(2,6)+'-'+v.slice(6);else out='('+v.slice(0,2)+') '+v.slice(2,7)+'-'+v.slice(7);e.target.value=out;});
    const cepEl=document.getElementById('cep'); cepEl&&cepEl.addEventListener('input',function(e){let v=onlyDigits(e.target.value).slice(0,8); if(v.length>5) e.target.value = v.slice(0,5)+'-'+v.slice(5); else e.target.value = v;});
    const cpfEl=document.getElementById('cpf_cnpj'); cpfEl&&cpfEl.addEventListener('input',function(e){let v=onlyDigits(e.target.value).slice(0,14); if(v.length<=11){v=v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/,function(_,a,b,c,d){let s=a||''; if(b) s+='.'+b; if(c) s+='.'+c; if(d) s+='-'+d; return s;});}else{v=v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*/,function(_,a,b,c,d,f){let s=a||''; if(b) s+='.'+b; if(c) s+='.'+c; if(d) s+='/'+d; if(f) s+='-'+f; return s;});} e.target.value=v;});
    document.getElementById('btn-buscar-cep').addEventListener('click',function(){const cep=onlyDigits(document.getElementById('cep').value); if(cep.length!==8){alert('CEP deve ter 8 dígitos');return;} fetch(`https://viacep.com.br/ws/${cep}/json/`).then(r=>r.json()).then(data=>{ if(data.erro){alert('CEP não encontrado');return;} document.getElementById('endereco').value=data.logradouro||''; document.getElementById('bairro').value=data.bairro||''; document.getElementById('cidade').value=data.localidade||''; document.getElementById('estado').value=data.uf||''; }).catch(e=>{console.error(e);alert('Erro ao buscar CEP');});});
  </script>
</body>
</html>