<?php
require_once __DIR__ . '/_layout_header.php';
require_login();

if (!isset($lista)) {
  require_once __DIR__ . '/../models/User.php';
  $userModel = new User($pdo);
  $lista = $userModel->listAll();
}
?>
<?php if (!empty($_GET['error']) && $_GET['error'] === 'own'): ?>
  <div class="alert alert-warning mt-3">Você não pode excluir o usuário com o qual está logado.</div>
<?php endif; ?>
<h2 class="my-4">Usuários</h2>
<div class="d-flex justify-content-end mb-3">
  <a href="usuario_form.php" class="btn btn-success">Novo Usuário</a>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Status</th>
            <th style="width:160px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lista as $u): ?>
          <tr>
            <td><?php echo (int)$u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['nome']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo ((int)$u['ativo'] === 1) ? 'Ativo' : 'Inativo'; ?></td>
            <td>
              <a href="usuario_form.php?id=<?php echo (int)$u['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
              <a href="index.php?c=users&a=delete&id=<?php echo (int)$u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este usuário?');">Excluir</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_footer.php'; ?>
