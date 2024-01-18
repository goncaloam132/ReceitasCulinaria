<?php
session_start();

// Incluir o arquivo de conexão
include_once '../../db/connection.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obter o ID da receita a ser apagada 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $receitaID = $_GET['id'];
} else {
    header("Location: receitas.php");
    exit();
}

// Verificar se a confirmação foi enviada pelo utilizador
if (isset($_POST['confirmacao']) && $_POST['confirmacao'] === 'Sim') {
    // Lógica para apagar a receita
    function apagarReceita($receitaID) {
        $conn = pdo_connect_mysql();

        // Preparar a declaração SQL para excluir a receita
        $stmt = $conn->prepare("DELETE FROM receitas WHERE id = ?");
        $stmt->bindParam(1, $receitaID);
        $stmt->execute();
    }

    // Chamar a função para apagar a receita
    apagarReceita($receitaID);

    // Redirecionar para a página de receitas ou outra página desejada após a exclusão
    header("Location: receitas.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apagar Receita</title>

    <!-- Incluir CSS do Bootstrap via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

    <!-- Incluir JS do Bootstrap (já incluindo a dependência do Popper.js) via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>


    <!-- Incluir seu CSS personalizado -->
    <link rel="stylesheet" href="receitas.css">
    <link rel="stylesheet" href="../../main.css">
</head>
<body>

<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="../paginainicial.php" class="logo d-flex align-items-center me-auto me-lg-0">
            <h1>ReceitasCulinária<span>.</span></h1>
        </a>
    </div>
</header>

<section class="vh-100">
    <div class="container-fluid h-custom fade-up text-center">
        <div class="row mt-3 d-flex justify-content-center align-items-center text-center">
            <p class="h2">Tem a certeza que deseja apagar esta receita?</p>
            <div class="mt-3">
            <form method="post" action="">
                <input type="submit" name="confirmacao" class="btn btn-danger me-2" value="Sim">
                <a href="javascript:history.go(-1);" class="btn btn-secondary">Cancelar</a>
            </form>
            </div>
        </div>
    </div>

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
</section>

</body>
</html>
