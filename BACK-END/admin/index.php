<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Quarto.php';
require_once __DIR__ . '/../models/Reserva.php';

Auth::requireLogin();

$controller = $_GET['c'] ?? 'dashboard';
$action = $_GET['a'] ?? 'list';

class AdminUserController {
  private PDO $pdo; private User $user;
  public function __construct(PDO $pdo){ $this->pdo=$pdo; $this->user=new User($pdo);}  
  public function list(){ $lista = $this->user->listAll(); require __DIR__ . '/usuarios.php'; }
  public function delete(){ $id = (int)($_GET['id'] ?? 0); $currentId = (int)($_SESSION['user_id'] ?? 0); if($id && $id !== $currentId){ $this->user->delete($id);} header('Location: index.php?c=users&a=list' . ($id===$currentId?'&error=own':'')); }
}

class AdminQuartoController {
  private PDO $pdo; private Quarto $quarto;
  public function __construct(PDO $pdo){ $this->pdo=$pdo; $this->quarto=new Quarto($pdo);}  
  public function list(){ $lista = $this->quarto->listAll(); require __DIR__ . '/quartos.php'; }
  public function delete(){
    $id=(int)($_GET['id']??0);
    $qs = 'index.php?c=quartos&a=list';
    if($id){
      $res = $this->quarto->delete($id);
      if (!empty($res['success'])) {
        if (!empty($res['soft'])) { $qs .= '&soft=1'; }
        else { $qs .= '&deleted=1'; }
      } else { $qs .= '&error=1'; }
    }
    header('Location: ' . $qs);
  }
}

class AdminReservaController {
  private PDO $pdo; private Reserva $reserva;
  public function __construct(PDO $pdo){ $this->pdo=$pdo; $this->reserva=new Reserva($pdo);}  
  public function list(){ $lista = $this->reserva->listAll(); require __DIR__ . '/reservas.php'; }
  public function delete(){ $id=(int)($_GET['id']??0); if($id){ $this->reserva->delete($id);} header('Location: index.php?c=reservas&a=list'); }
}

switch($controller){
  case 'users':
    $ctrl = new AdminUserController($pdo);
    break;
  case 'quartos':
    $ctrl = new AdminQuartoController($pdo);
    break;
  case 'reservas':
    $ctrl = new AdminReservaController($pdo);
    break;
  default:
    header('Location: index.php?c=quartos&a=list');
    exit;
}

if (!method_exists($ctrl, $action)) { $action = 'list'; }
$ctrl->$action();
