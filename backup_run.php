<?php
// backup_run.php - Gera backup do banco ControleExterno e da pasta uploads
// Pode ser executado via navegador (http://localhost:8080/ControleExterno/backup_run.php)
// ou via CLI: php backup_run.php

$results = [
  'db' => ['ok' => false, 'message' => ''],
  'uploads' => ['ok' => false, 'message' => '']
];

$backupDir = __DIR__ . DIRECTORY_SEPARATOR . 'backups';
if (!is_dir($backupDir)) { mkdir($backupDir, 0777, true); }
$ts = date('Ymd-His');

// Config do banco (mantido igual aos arquivos do projeto)
$host = '127.0.0.1';
$port = 3306;
$db   = 'ControleExterno';
$user = 'root';
$pass = '';

// 1) Dump do banco via mysqldump
$sqlFile = $backupDir . DIRECTORY_SEPARATOR . "db-$ts.sql";

function findMysqldumpPath(): string {
    // Tenta via PATH do Windows
    @exec('where mysqldump', $out, $code);
    if ($code === 0 && !empty($out)) {
        return trim($out[0]);
    }
    // Tenta localizar via Laragon
    $candidates = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe');
    if (!empty($candidates)) {
        return $candidates[0];
    }
    // Retorna nome genérico (caso esteja no PATH)
    return 'mysqldump';
}

$mysqldump = findMysqldumpPath();
$cmd = '"' . $mysqldump . '"'
     . " --host=$host --port=$port --user=$user --password=$pass"
     . " --databases $db --routines --events --default-character-set=utf8mb4"
     . ' > "' . $sqlFile . '" 2>&1';

@exec($cmd, $outDump, $codeDump);
if (file_exists($sqlFile) && filesize($sqlFile) > 0 && $codeDump === 0) {
    $results['db']['ok'] = true;
    $results['db']['message'] = 'Dump gerado em ' . basename($sqlFile);
} else {
    $log = isset($outDump) ? implode("\n", $outDump) : '';
    $results['db']['message'] = 'Falha ao gerar dump. Verifique o caminho do mysqldump. Saída: ' . $log;
}

// 2) Zip da pasta uploads
$uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
$zipFile = $backupDir . DIRECTORY_SEPARATOR . "uploads-$ts.zip";

function zipDir($source, $zipPath): bool {
    if (!extension_loaded('zip')) { return false; }
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) { return false; }
    $source = realpath($source);
    if ($source === false) { $zip->close(); return false; }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $file) {
        $filePath = realpath($file);
        $localPath = ltrim(str_replace($source, '', $filePath), DIRECTORY_SEPARATOR);
        if (is_dir($filePath)) {
            $zip->addEmptyDir($localPath);
        } else {
            $zip->addFile($filePath, $localPath);
        }
    }
    return $zip->close();
}

if (is_dir($uploadsDir)) {
    if (zipDir($uploadsDir, $zipFile) && file_exists($zipFile)) {
        $results['uploads']['ok'] = true;
        $results['uploads']['message'] = 'Uploads zipado em ' . basename($zipFile);
    } else {
        $results['uploads']['message'] = 'Falha ao zipar uploads (verifique extensão zip habilitada).';
    }
} else {
    $results['uploads']['message'] = 'Pasta uploads não encontrada.';
}

$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    echo "Backup concluído:\n";
    echo "- Banco: " . ($results['db']['ok'] ? 'OK' : 'ERRO') . ' - ' . $results['db']['message'] . "\n";
    echo "- Uploads: " . ($results['uploads']['ok'] ? 'OK' : 'ERRO') . ' - ' . $results['uploads']['message'] . "\n";
    exit(($results['db']['ok'] && $results['uploads']['ok']) ? 0 : 1);
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Backup</title>
  <style>
    body { font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; background:#f5f7fa; display:flex; align-items:center; justify-content:center; min-height:100vh; }
    .card { background:#fff; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,0.1); padding:30px; max-width:600px; }
    h1 { margin:0 0 15px; color:#333; }
    .ok { color:#2e7d32; }
    .err { color:#d32f2f; }
    .mono { font-family: Consolas, monospace; font-size:13px; background:#f0f0f0; padding:8px; border-radius:6px; }
    a.btn { display:inline-block; margin-top:15px; padding:10px 16px; background:linear-gradient(135deg,#667eea 0%, #764ba2 100%); color:#fff; text-decoration:none; border-radius:6px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>🗄️ Backup</h1>
    <p class="<?php echo $results['db']['ok'] ? 'ok' : 'err'; ?>">Banco: <?php echo htmlspecialchars($results['db']['message']); ?></p>
    <p class="<?php echo $results['uploads']['ok'] ? 'ok' : 'err'; ?>">Uploads: <?php echo htmlspecialchars($results['uploads']['message']); ?></p>
    <p class="mono">Arquivos salvos em: <?php echo htmlspecialchars($backupDir); ?></p>
    <a class="btn" href="dashboard.php">Voltar ao Dashboard</a>
  </div>
</body>
</html>
