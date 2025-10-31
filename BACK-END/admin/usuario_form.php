<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
$userModel = new User($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$erro = '';
$u = ['nome' => '', 'email' => '', 'ativo' => 1];
if ($editing) {
  $row = $userModel->getById($id);
  if ($row) $u = $row; else $erro = 'Usuário não encontrado';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [
    'nome' => trim($_POST['nome'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'senha' => $_POST['senha'] ?? '',
    'ativo' => isset($_POST['ativo']) ? 1 : 0,
  ];
  if (!$data['nome'] || !$data['email']) {
    $erro = 'Nome e Email são obrigatórios';
    $u = array_merge($u, $data);
  } else {
    if ($editing) { $userModel->update($id, $data); } else { $userModel->create($data); }
    header('Location: usuarios.php');
    exit;
  }
}
?>
<h2 class="my-4"><?php echo $editing ? 'Editar Usuário' : 'Novo Usuário'; ?></h2>
<div class="card">
  <div class="card-body">
    <?php if ($erro): ?><div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Nome *</label>
        <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($u['nome']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($u['email']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha <?php echo $editing ? '(deixe em branco para manter)' : '*'; ?></label>
        <input type="password" name="senha" class="form-control" <?php echo $editing ? '' : 'required'; ?>>
      </div>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" <?php echo ((int)$u['ativo'] === 1) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="ativo">Ativo</label>
      </div>
      <div>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><?php echo $editing ? 'Salvar' : 'Cadastrar'; ?></button>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_footer.php'; ?>
