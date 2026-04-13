<?php
class Usuarios{
    private $pdo;
  
    //public function __construct($dbname, $host, $user, $senha){
    public function __construct()
    {
      try{
        $dbPath = '../../orby_data/sistema_login.sqlite';
        //$this->pdo = new PDO("mysql:dbname=".$dbname.";host=".$host, $user, $senha);

        if(!file_exists($dbPath)){
          die("Arquivo de banco de dados não encontrado: $dbPath");
        }
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      }catch(PDOException $e){
        echo "Erro ao se conectar ao banco de dados: " .$e->getMessage();
        exit();
      }catch(Exception $e){
        echo "Erro generico: ". $e->getMessage();
        exit();
      }
    }
  
    public function getPdo(){
      return $this->pdo;
    }
    public function criandoUsuario($email, $usuario, $senha){
      $sql = "SELECT id FROM usuarios WHERE email = :email";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(":email", $email);
      $stmt->execute();
      if($stmt->rowCount() > 0){
        $_SESSION['erro'] = "Usuário já cadastrado!";
        header('Location: login');
        exit();
      }else{
        $sql = "INSERT INTO usuarios(email, usuario, senha) VALUES(:email, :usuario, :senha )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":usuario", $usuario);
        $stmt->bindValue(":senha", $senha);
        if ($stmt->execute()){
          $id = $this->pdo->lastInsertId();
          //usanod os mesmos dados para validar os dados do perfil
          //require_once("public/controller/valida_dados_perfil.php");
          $this->criarInfoUsuario($id, null, null, null, null, null, null, null, 'publico');
          $_SESSION['sucesso'] = "Usuário criado com sucesso!";
          header('Location: login');
          exit();
        } else {
          $_SESSION['erro'] = "Erro ao criar usuário!";
          header('Location: login');
          exit();
        }
      }
    }
  
    public function buscarUsuario($email, $senha){
      // Busca TODOS os usuários para fazer o login ou para saber se ele esta contido em algum local
      $sql = "SELECT * FROM usuarios";
      $stmt = $this->pdo->query($sql);
      $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      // Procura manualmente pelo email
      $usuarioEncontrado = null;
      foreach($todos as $user){
          if(strtolower(trim($user['email'])) === strtolower(trim($email))){
              $usuarioEncontrado = $user;
              break;
          }
      }
      
      if($usuarioEncontrado){
          if(password_verify($senha, $usuarioEncontrado['senha'])){
              $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
              $_SESSION['usuario_nome'] = $usuarioEncontrado['usuario'];
              $_SESSION['email'] = $usuarioEncontrado['email'];
              
              $amigos_ids = $this->carregarAmigos($usuarioEncontrado['id']);
              $_SESSION['amigos_ids'] = $amigos_ids;
              
              header('Location: perfil');
              exit();
          } else {
              $_SESSION['erro'] = "Senha Incorreta!";
              header('Location: login');
              exit();
          }
      } else {
          $_SESSION['erro'] = "Usuario não encontrado!";
          header('Location: login');
          exit();
      }
  }
    public function atualizarUsuario($id, $email, $usuario, $senha){
      $sql = "UPDATE usuarios SET email = :email, usuario = :usuario, senha = :senha WHERE id = :id";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(":email", $email);
      $stmt->bindValue(":usuario", $usuario);
      $stmt->bindValue(":senha", $senha);
      $stmt->bindValue(":id", $id);
      return $stmt->execute();
    }
  
    public function deletarUsuario($id){
      $this->deletarInfoUsuario($id);
      $sql = "DELETE FROM usuarios WHERE id = :id";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(":id", $id);
      return $stmt->execute();
    }
  
    public function criarInfoUsuario($id, $imagem_perfil, $foto_capa, $status_relacionamento, $sexo, $bio, $links, $localizacao, $privacidade){
      $sql = "INSERT INTO user_info 
              (id_usuario, imagem_perfil, foto_capa, status_relacionamento, sexo, bio, links, localizacao, privacidade) 
              VALUES 
              (:id, :imagem_perfil, :foto_capa, :status_relacionamento, :sexo, :bio, :links, :localizacao, :privacidade)";
      
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':id', $id);
      $stmt->bindParam(':imagem_perfil', $imagem_perfil, PDO::PARAM_LOB);
      $stmt->bindParam(':foto_capa', $foto_capa, PDO::PARAM_LOB);
      $stmt->bindParam(':status_relacionamento', $status_relacionamento);
      $stmt->bindParam(':sexo', $sexo);
      $stmt->bindParam(':bio', $bio);
      $stmt->bindParam(':links', $links);
      $stmt->bindParam(':localizacao', $localizacao);
      $stmt->bindParam(':privacidade', $privacidade);
      
      return $stmt->execute();
  }
  
    public function buscarInfoUsuario($id){
      $sql = "SELECT * FROM user_info WHERE id_usuario = :id";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      
      // Se não encontrar, retorna um array vazio em vez de false
      if (!$result) {
          return [];
      }
      
      return $result;
  }
  
  public function atualizarInfoUsuario($id, $imagem_perfil, $foto_capa, $status_relacionamento, $sexo, $bio, $links, $localizacao, $privacidade){
    
  // Primeiro verifica se o registro existe
  $check = $this->buscarInfoUsuario($id);
  
  if (empty($check)) {
      // Se não existe, cria um novo
      $this->criarInfoUsuario($id, $imagem_perfil, $foto_capa, $status_relacionamento, $sexo, $bio, $links, $localizacao, $privacidade);
      return true;
  }
  
  // Se existe, atualiza
  $sql = "UPDATE user_info SET 
          imagem_perfil = :imagem_perfil, 
          foto_capa = :foto_capa, 
          status_relacionamento = :status_relacionamento, 
          sexo = :sexo, 
          bio = :bio, 
          links = :links, 
          localizacao = :localizacao, 
          privacidade = :privacidade 
          WHERE id_usuario = :id";
  
  $stmt = $this->pdo->prepare($sql);
  $stmt->bindParam(':id', $id);
  $stmt->bindParam(':imagem_perfil', $imagem_perfil, PDO::PARAM_LOB);
  $stmt->bindParam(':foto_capa', $foto_capa, PDO::PARAM_LOB);
  $stmt->bindParam(':status_relacionamento', $status_relacionamento);
  $stmt->bindParam(':sexo', $sexo);
  $stmt->bindParam(':bio', $bio);
  $stmt->bindParam(':links', $links);
  $stmt->bindParam(':localizacao', $localizacao);
  $stmt->bindParam(':privacidade', $privacidade);
  
  return $stmt->execute();
}
    
  
    public function deletarInfoUsuario($id){
      $sql = "DELETE FROM user_info WHERE id_usuario = :id";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
    }


    public function carregarAmigos($id){
      $sql = "SELECT id_amigo FROM amigos WHERE id_usuario = :id";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $amigos_ids = [];
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $amigos_ids[] = $row['id_amigo'];
      }
      return $amigos_ids;
    }
    public function buscarUsuariosPorIds($ids){
      if (empty($ids)) {
          return [];
      }
      
      $placeholders = implode(',', array_fill(0, count($ids), '?'));
      $sql = "SELECT u.id, u.usuario, u.email, ui.imagem_perfil, ui.bio 
              FROM usuarios u
              LEFT JOIN user_info ui ON u.id = ui.id_usuario
              WHERE u.id IN ($placeholders)";
      
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute($ids);
      
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  /**
   * Adicionar um amigo
   */
  public function adicionarAmigo($id_usuario, $id_amigo){
      // Verifica se já é amigo
      $sql = "SELECT * FROM amigos WHERE id_usuario = :id_usuario AND id_amigo = :id_amigo";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':id_usuario', $id_usuario);
      $stmt->bindParam(':id_amigo', $id_amigo);
      $stmt->execute();
      
      if ($stmt->rowCount() > 0) {
          return false; // Já é amigo
      }
      
      // Adiciona amigo
      $sql = "INSERT INTO amigos (id_usuario, id_amigo) VALUES (:id_usuario, :id_amigo)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':id_usuario', $id_usuario);
      $stmt->bindParam(':id_amigo', $id_amigo);
      return $stmt->execute();
    }

    /**
     * Remover um amigo - VERSÃO SIMPLES
     */
    public function removerAmigo($id_usuario, $id_amigo){
        // Remove nos dois sentidos
        $sql = "DELETE FROM amigos WHERE (id_usuario = ? AND id_amigo = ?) OR (id_usuario = ? AND id_amigo = ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $id_amigo, $id_amigo, $id_usuario]);
    }

    /**
     * Verificar se dois usuários são amigos (nos dois sentidos)
     */
    public function saoAmigos($id_usuario1, $id_usuario2){
        $sql = "SELECT * FROM amigos WHERE (id_usuario = ? AND id_amigo = ?) OR (id_usuario = ? AND id_amigo = ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario1, $id_usuario2, $id_usuario2, $id_usuario1]);
        return $stmt->rowCount() > 0;
    }
    /**
   * Atualiza a sessão com a lista mais recente de amigos
   */
  public function atualizarSessaoAmigos($id_usuario) {
      $amigos = $this->carregarAmigos($id_usuario);
      $_SESSION['amigos_ids'] = $amigos;
      return $amigos;
  }

  /**
   * Buscar solicitação específica entre dois usuários
   */
  public function buscarSolicitacao($id_remetente, $id_destinatario, $status = 'pendente') {
      $sql = "SELECT * FROM solicitacoes 
              WHERE id_remetente = ? AND id_destinatario = ? AND status = ?";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$id_remetente, $id_destinatario, $status]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Enviar solicitação de amizade
   */
  public function enviarSolicitacao($id_remetente, $id_destinatario) {
      // Verifica se já são amigos
      if ($this->saoAmigos($id_remetente, $id_destinatario)) {
          return ['erro' => 'Vocês já são amigos'];
      }
      
      // Verifica se já existe alguma solicitação PENDENTE entre os dois
      $sql = "SELECT * FROM solicitacoes WHERE 
              ((id_remetente = ? AND id_destinatario = ?) OR 
               (id_remetente = ? AND id_destinatario = ?)) 
              AND status = 'pendente'";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$id_remetente, $id_destinatario, $id_destinatario, $id_remetente]);
      
      if ($stmt->rowCount() > 0) {
          $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($solicitacao['id_remetente'] == $id_remetente) {
              return ['erro' => 'Você já enviou uma solicitação para esta pessoa'];
          } else {
              return ['erro' => 'Esta pessoa já te enviou uma solicitação'];
          }
      }
      
      // Verifica se já existe solicitação recusada/cancelada
      $sql = "SELECT * FROM solicitacoes WHERE 
              ((id_remetente = ? AND id_destinatario = ?) OR 
               (id_remetente = ? AND id_destinatario = ?)) 
              AND status IN ('recusada', 'cancelada')";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$id_remetente, $id_destinatario, $id_destinatario, $id_remetente]);
      
      if ($stmt->rowCount() > 0) {
          // Atualiza a solicitação existente para pendente
          $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);
          $sql = "UPDATE solicitacoes SET 
                  status = 'pendente', 
                  data_envio = CURRENT_TIMESTAMP, 
                  data_resposta = NULL 
                  WHERE id_solicitacao = ?";
          $stmt = $this->pdo->prepare($sql);
          $stmt->execute([$solicitacao['id_solicitacao']]);
          return ['sucesso' => 'Solicitação enviada com sucesso'];
      }
      
      // Cria nova solicitação
      $sql = "INSERT INTO solicitacoes (id_remetente, id_destinatario, status) VALUES (?, ?, 'pendente')";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$id_remetente, $id_destinatario]);
      
      return ['sucesso' => 'Solicitação enviada com sucesso'];
  }
  /**
 * Aceitar solicitação de amizade
 */
  public function aceitarSolicitacao($id_solicitacao) {
    if (!$id_solicitacao || !is_numeric($id_solicitacao)) {
        return ['erro' => 'ID de solicitação inválido'];
    }
    
    $this->pdo->beginTransaction();
    
    try {
        // Primeiro, busca a solicitação
        $sql = "SELECT * FROM solicitacoes WHERE id_solicitacao = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_solicitacao]);
        $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$solicitacao) {
            throw new Exception('Solicitação não encontrada');
        }
        
        if ($solicitacao['status'] != 'pendente') {
            throw new Exception('Solicitação não está pendente');
        }
        
        // Atualiza status da solicitação
        $sql = "UPDATE solicitacoes SET status = 'aceita', data_resposta = CURRENT_TIMESTAMP WHERE id_solicitacao = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_solicitacao]);
        
        // INSERE AMIGOS NOS DOIS SENTIDOS
        $sql = "INSERT INTO amigos (id_usuario, id_amigo) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$solicitacao['id_remetente'], $solicitacao['id_destinatario']]);
        $stmt->execute([$solicitacao['id_destinatario'], $solicitacao['id_remetente']]);
        
        $this->pdo->commit();
        
        // Atualiza a sessão de ambos os usuários
        $this->atualizarSessaoAmigos($solicitacao['id_remetente']);
        $this->atualizarSessaoAmigos($solicitacao['id_destinatario']);
        
        return ['sucesso' => 'Amizade aceita com sucesso!'];
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        error_log("Erro ao aceitar solicitação: " . $e->getMessage());
        return ['erro' => $e->getMessage()];
    }
}
  /**
   * Recusar solicitação de amizade
   */
  public function recusarSolicitacao($id_solicitacao) {
      $sql = "UPDATE solicitacoes SET status = 'recusada', data_resposta = CURRENT_TIMESTAMP WHERE id_solicitacao = ?";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$id_solicitacao]);
      return ['sucesso' => 'Solicitação recusada'];
  }

  /**
   * Buscar solicitações pendentes para um usuário (recebidas)
   */
  public function getSolicitacoesPendentes($id_usuario) {
      $id_usuario = (int)$id_usuario;
      
      $sql = "SELECT 
                  s.id_solicitacao,
                  s.id_remetente,
                  s.id_destinatario,
                  s.status,
                  s.data_envio,
                  s.data_resposta,
                  u.id as remetente_id,
                  u.usuario as nome_remetente,
                  u.email,
                  ui.imagem_perfil,
                  ui.bio
              FROM solicitacoes s
              INNER JOIN usuarios u ON s.id_remetente = u.id
              LEFT JOIN user_info ui ON u.id = ui.id_usuario
              WHERE s.id_destinatario = ? 
                AND s.status = 'pendente'
              ORDER BY s.data_envio DESC";
      
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$id_usuario]);
      
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Limpar solicitações recusadas com mais de 30 dias
   */
  public function limparSolicitacoesAntigas() {
      $sql = "DELETE FROM solicitacoes WHERE status IN ('recusada', 'cancelada') AND data_resposta < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 30 DAY)";
      $stmt = $this->pdo->prepare($sql);
      return $stmt->execute();
  }
  /**
   * Verificar status da amizade entre dois usuários
   */
  public function verificarStatusAmizade($id_usuario1, $id_usuario2) {
    // Força conversão para inteiro
    $id1 = intval($id_usuario1);
    $id2 = intval($id_usuario2);
    
    // Query simples e direta
    $sql = "SELECT COUNT(*) as total FROM amigos 
            WHERE (id_usuario = $id1 AND id_amigo = $id2) 
               OR (id_usuario = $id2 AND id_amigo = $id1)";
    
    $result = $this->pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($row['total'] > 0) {
        return 'amigos';
    }
    
    // Verifica solicitações pendentes
    $sql = "SELECT COUNT(*) as total FROM solicitacoes 
            WHERE id_remetente = $id1 AND id_destinatario = $id2 AND status = 'pendente'";
    $result = $this->pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($row['total'] > 0) {
        return 'aguardando_resposta';
    }
    
    $sql = "SELECT COUNT(*) as total FROM solicitacoes 
            WHERE id_remetente = $id2 AND id_destinatario = $id1 AND status = 'pendente'";
    $result = $this->pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($row['total'] > 0) {
        return 'pendente';
    }
    
    return 'nao_amigos';
}

  
}

?>
