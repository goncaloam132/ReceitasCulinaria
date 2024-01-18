<?php
session_start();

// Incluir o arquivo de conexão
include '../../db/connection.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obter o ID do utilizador atualmente logado
$userID = $_SESSION['user_id'];

// Função para obter todas as receitas de um utilizador
function obterReceitasUsuario($userID) {
    // Obter uma conexão PDO
    $conn = pdo_connect_mysql();

    // Preparar a declaração SQL para obter as receitas
    $stmt = $conn->prepare("SELECT * FROM receitas WHERE id_usuario = ?");
    $stmt->bindParam(1, $userID);
    $stmt->execute();

    // Retornar todas as receitas como um array associativo
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter todas as receitas do utilizador atual
$receitasUsuario = obterReceitasUsuario($userID);

// Variáveis para pesquisa e filtros
$termo_pesquisa = isset($_GET['termo_pesquisa']) ? $_GET['termo_pesquisa'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'todas';

// Construir e executar a consulta SQL para pesquisa ou filtragem por categoria
$conn = pdo_connect_mysql();

if ($categoria === 'favoritos') {
    // Filtrar por favoritos
    $stmt = $conn->prepare("SELECT r.* FROM receitas r JOIN receitas_favoritas f ON r.id = f.id_receita WHERE f.id_usuario = ?");
    $stmt->execute([$userID]);
} elseif ($categoria !== 'todas' && empty($termo_pesquisa)) {
    // Filtrar por categoria
    $stmt = $conn->prepare("SELECT * FROM receitas WHERE id_usuario = ? AND categoria = ?");
    $stmt->execute([$userID, $categoria]);
} else {
    // Pesquisar ou exibir todas as receitas
    $stmt = $conn->prepare("SELECT * FROM receitas WHERE id_usuario = ? AND (titulo LIKE ? OR categoria = ?)");
    $stmt->execute([$userID, "%$termo_pesquisa%", $categoria]);
}

$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Receitas</title>

    <!-- Incluir CSS do Bootstrap via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

    <!-- Incluir JS do Bootstrap (já incluindo a dependência do Popper.js) via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <!-- Incluir seu CSS personalizado -->
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="receitas.css">
</head>
<body>

<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="../paginainicial.php" class="logo d-flex align-items-center me-auto me-lg-0">
            <h1>ReceitasCulinária<span>.</span></h1>
        </a>
    </div>
</header>

<section class="vh-100 h-custom">
    <form method="get" action="">
    <div class="container mt-5">
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="termo_pesquisa" placeholder="Pesquisar receitas..." value="<?php echo $termo_pesquisa; ?>">
            <div class="input-group-append">
                <label for="categoria" class="input-group-text">Categoria:</label>
                <select name="categoria" class="form-select">
                    <option value="todas" <?php echo ($categoria == 'todas') ? 'selected' : ''; ?>>Todas</option>
                    <option value="Aperitivo" <?php echo ($categoria == 'Aperitivo') ? 'selected' : ''; ?>>Aperitivo</option>
                    <option value="Prato Principal" <?php echo ($categoria == 'Prato Principal') ? 'selected' : ''; ?>>Prato Principal</option>
                    <option value="Sobremesa" <?php echo ($categoria == 'Sobremesa') ? 'selected' : ''; ?>>Sobremesa</option>
                    <option value="favoritos" <?php echo ($categoria == 'favoritos') ? 'selected' : ''; ?>>Favoritos</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary ms-3">Pesquisar/Filtrar</button>
        </div>
    </div>
    </form>

    <section>
        <div class="container-fluid h-custom fade-up text-center">
            <h1>Minhas Receitas</h1>

            <?php if (!empty($receitas)): ?>
                <ul class="list-group mt-4">
                    <?php foreach ($receitas as $receita): ?>
                        <li class="list-group-item">
                            <strong>Nome da Receita:</strong> <?php echo $receita['titulo']; ?>
                            <a href="verreceita.php?id=<?php echo $receita['id']; ?>" class="btn btn-primary btn-sm ms-3">Ver receita</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="mt-4">Nenhuma receita encontrada.</p>
            <?php endif; ?>

            <a href="inserirreceitas.php" class="btn btn-primary mt-4">Inserir Receitas</a>
            <a href="../paginainicial.php" class="btn btn-secondary mt-4">Voltar</a>
        </div>
    </section>
</section>

    <footer class="fixed-bottom">
    <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>ReceitasCulinária</span></strong>. Todos os Direitos Reservados
            </div>
        </div>
        <div class="container social-links d-flex justify-content-end">
            <a href="#!" class="text-white me-4">
                <i class="bi bi-facebook"></i>
            </a>
            <a href="#!" class="text-white me-4">
                <i class="bi bi-twitter"></i>
            </a>
            <a href="#!" class="text-white me-4">
                <i class="bi bi-google"></i>
            </a>
            <a href="#!" class="text-white">
                <i class="bi bi-linkedin"></i>
            </a>
        </div>
    </div>
</footer>


</body>
</html>