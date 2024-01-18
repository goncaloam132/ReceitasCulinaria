<?php
session_start();

include "../../db/connection.php";


// Verificar se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obter o ID do utilizador atualmente logado
$userID = $_SESSION['user_id'];

// Função para obter dados pessoais do utilizador
function obterDadosPessoais($userID) {
    // Obter uma conexão PDO
    $conn = pdo_connect_mysql();

    // Preparar a declaração SQL para obter dados pessoais
    $stmt = $conn->prepare("SELECT nome_completo, morada, numero_telefone, qtd_receitas FROM dados_pessoais WHERE id_usuario = ?");
    $stmt->bindParam(1, $userID);
    $stmt->execute();

    // Verificar se há resultados
    if ($stmt->rowCount() > 0) {
        // Retornar os dados pessoais como um array associativo
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Se nenhum resultado for encontrado, retornar um array vazio
        return array();
    }
}

// Obter dados pessoais do utilizador atual
$dadosPessoais = obterDadosPessoais($userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dados Pessoais</title>

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
    <link rel="stylesheet" href="perfil.css">

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

    <div class="container-fluid fade-up text-top h-custom">
        <h1>Dados Pessoais</h1>

        <?php if (!empty($dadosPessoais)): ?>
            <ul class="list-group">
                <li class="list-group-item"><strong>Nome Completo:</strong> <?php echo $dadosPessoais['nome_completo']; ?></li>
                <li class="list-group-item"><strong>Morada:</strong> <?php echo $dadosPessoais['morada']; ?></li>
                <li class="list-group-item"><strong>Número de Telefone:</strong> <?php echo $dadosPessoais['numero_telefone']; ?></li>
                <li class="list-group-item"><strong>Quantidade de Receitas Inseridas:</strong> <?php echo $dadosPessoais['qtd_receitas']; ?></li>
            </ul>
        <?php else: ?>
            <p class="mt-3">Nenhum dado pessoal encontrado.</p>
        <?php endif; ?>

        <div class="mt-3">
            <a href="inserirdados.php" class="btn btn-primary me-2">Inserir Dados</a>
            <a href="editarperfil.php" class="btn btn-secondary me-2">Editar</a>
            <a href="../paginainicial.php" class="btn btn-secondary">Voltar</a>
        </div>
    </div>

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

