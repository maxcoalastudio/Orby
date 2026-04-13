<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    return;
}

$conn = new Usuarios();
$amigos_atuais = $conn->carregarAmigos($_SESSION['usuario_id']);

if ($_SESSION['amigos_ids'] != $amigos_atuais) {
    $_SESSION['amigos_ids'] = $amigos_atuais;
}

if (isset($_SESSION['amigos_ids']) && !empty($_SESSION['amigos_ids'])) {
    $placeholders = implode(',', array_fill(0, min(5, count($_SESSION['amigos_ids'])), '?'));
    $ids_para_mostrar = array_slice($_SESSION['amigos_ids'], 0, 5);
    
    $sql = "SELECT u.id, u.usuario, ui.imagem_perfil 
            FROM usuarios u
            LEFT JOIN user_info ui ON u.id = ui.id_usuario
            WHERE u.id IN ($placeholders)";
    
    $stmt = $conn->getPdo()->prepare($sql);
    foreach ($ids_para_mostrar as $key => $id) {
        $stmt->bindValue(($key+1), $id, PDO::PARAM_INT);
    }
    $stmt->execute();
    $amigos_sidebar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <div class="sidebar-amigos">
        <h3>Amigos Online</h3>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($amigos_sidebar as $amigo): ?>
                <li style="display: flex; align-items: center; gap: var(--spacing-2); margin-bottom: var(--spacing-2);">
                    <img src="<?php echo (!empty($amigo['imagem_perfil'])) ? 'data:image/jpeg;base64,' . base64_encode($amigo['imagem_perfil']) : '/public/image/perfil_default.jpg'; ?>" 
                         alt="<?php echo $amigo['usuario']; ?>" 
                         class="avatar avatar-sm">
                    <a href="index.php?page=perfil_amigo&id=<?php echo $amigo['id']; ?>" style="flex: 1;">
                        <?php echo $amigo['usuario']; ?>
                    </a>
                    <span class="online-status online">●</span>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="?page=gerencia_amigos" class="btn btn-outline w-100" style="margin-top: var(--spacing-3);">Ver todos os amigos</a>
    </div>
    
<?php } else { ?>
    <div class="sidebar-amigos">
        <h3>Amigos</h3>
        <p style="color: var(--gray-500);">Você ainda não tem amigos.</p>
        <a href="?page=gerencia_amigos" class="btn btn-primary w-100" style="margin-top: var(--spacing-3);">Encontrar amigos</a>
    </div>
<?php } ?>