<?php
// Adicione isso no começo do index.php (antes do switch)
// error_log("=== DEBUG INDEX ===");
// error_log("URL: " . $_SERVER['REQUEST_URI']);
// error_log("GET page: " . ($_GET['page'] ?? 'NULL'));
// error_log("GET id: " . ($_GET['id'] ?? 'NULL'));
// echo "<pre>";
// echo "URL: " . $_SERVER['REQUEST_URI'] . "\n";
// echo "page: " . ($_GET['page'] ?? 'NULL') . "\n";
// echo "id: " . ($_GET['id'] ?? 'NULL') . "\n";
// echo "</pre>";

// Pega a página da URL, se não existir define como 'login'
$url = $_GET['page'] ?? 'login';  // <<< ISSO É CRÍTICO! Valor padrão

# ROTAS DE URLS, toda indexação é feita aqui
switch ($url){
    case 'testar':
        include_once('testeLivro.php');
        break;
        
    case 'login':
        include_once('public/view/paginas_composta/login.php');
        break;
        
    case 'valida_login':
        include_once('public/controller/valida_login.php');
        break;
        
    case 'valida_dados_perfil':
        include_once('public/controller/valida_dados_perfil.php');
        break;
        
    case 'valida_dados_amigos':
        include_once('public/controller/valida_dados_amigos.php');
        break;

    //verificando erros de acesso
    case 'diagnostico':
        include_once('public/view/erro/diagnostico_amigos.php');
        break;

    case 'perfil':
        // Verifica se usuário está logado antes de mostrar perfil
        session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ?page=login');
            exit();
        }
        include_once('public/view/paginas_composta/perfil.php');
        break;
        
    case 'gerencia_amigos':
        // Verifica se usuário está logado
        session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ?page=login');
            exit();
        }
        include_once('public/view/paginas_composta/gerencia_amigos.php');
        break;
    
    case 'perfil_amigo':
        session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ?page=login');
            exit();
        }
        include_once('public/view/paginas_composta/perfil_amigo.php');
        break;
        
    case 'sair':
        include_once('public/view/modulos/sair.php');
        break;
        
    case 'amigos':  // Módulo sidebar
        include_once('public/view/modulos/amigos.php');
        break;
        
    case 'menu_nav':
        include_once('public/view/modulos/menu_nav.php');
        break;
        
    case 'online_chat':
        include_once('public/view/modulos/online_chat.php');
        break;
        
    case 'footer':
        include_once('public/view/modulos/footer.php');
        break;
        
    case 'sobre':
        include_once('public/view/modulos/sobre.php');
        break;
        
    case 'servicos':
        include_once('public/view/modulos/servicos.php');
        break;
        
    case 'contato':
        include_once('public/view/modulos/contato.php');
        break;
        
    case 'editorial':
        include_once('public/view/paginas_composta/placa_editorial.php');
        break;
        
    case 'configuracao':
        include_once('public/view/paginas_composta/configuracao.php');
        break;
        
    case 'logo':
        include_once('public/view/modulos/logo.php');
        break;
        
    case 'midia':
        include_once('public/view/modulos/midia.php');
        break;
        
    case 'cla':
        include_once('public/view/modulos/cla.php');
        break;
        
    case 'orby':
        include_once('public/view/paginas_composta/orby.php');
        break;
        
    // Páginas de erro
    case '400':
        include_once('public/view/erro/400.php');
        break;
        
    case '401':
        include_once('public/view/erro/401.php');
        break;
        
    case '403':
        include_once('public/view/erro/403.php');
        break;
        
    case '404':  // SEM ESPAÇOS!
        include_once('public/view/erro/404.php');
        break;
        
    case '405':
        include_once('public/view/erro/405.php');
        break;
        
    default:
        // Página não encontrada
        include_once('public/view/erro/404.php');
        break;
}
?>
