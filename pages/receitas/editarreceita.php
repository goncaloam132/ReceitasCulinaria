<?php
session_start();

// Incluir o arquivo de conexão
include_once '../../db/connection.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obter o ID da receita a ser editada (vindo do parâmetro na URL)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $receitaID = $_GET['id'];
} else {
    header("Location: receitas.php");
    exit();
}

// Função para obter detalhes da receita
function obterDetalhesReceita($receitaID) {
    $conn = pdo_connect_mysql();
    $stmt = $conn->prepare("SELECT * FROM receitas WHERE id = ?");
    $stmt->bindParam(1, $receitaID);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Chamar a função para obter detalhes da receita
$detalhesReceita = obterDetalhesReceita($receitaID);

// Verificar se a receita foi encontrada
if (!$detalhesReceita) {
    header("Location: receitas.php");
    exit();
}

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $categoria = $_POST['categoria'];
    $ingredientes = $_POST['ingredientes'];
    $instrucoes = $_POST['instrucoes'];
    $data_feita = $_POST['data_feita'];
    $descricao = $_POST['descricao'];
    $tempo_preparacao = $_POST['tempo_preparacao'];

    // Atualizar a receita no banco de dados
    $conn = pdo_connect_mysql();
    $stmt = $conn->prepare("UPDATE receitas SET titulo=?, categoria=?, ingredientes=?, instrucoes=?, data_feita=?, descricao=?, tempo_preparacao=? WHERE id=?");
    $stmt->execute([$titulo, $categoria, $ingredientes, $instrucoes, $data_feita, $descricao, $tempo_preparacao, $receitaID]);

    // Redirecionar para a página de visualização da receita
    header("Location: verreceita.php?id=" . $receitaID);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Receita</title>

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

    
<div class="container-fluid fade-up text-top">
    <h1>Editar Receita</h1>

    <form method="post" action="">
        <div class="mb-3">
            <label for="titulo" class="form-label">Nome da Receita:</label>
            <input type="text" name="titulo" class="form-control" value="<?php echo $detalhesReceita['titulo']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="categoria" class="form-label">Categoria:</label>
            <select name="categoria" class="form-select" required>
                <option value="Prato Principal" <?php echo ($detalhesReceita['categoria'] == 'Prato Principal') ? 'selected' : ''; ?>>Prato Principal</option>
                <option value="Sobremesa" <?php echo ($detalhesReceita['categoria'] == 'Sobremesa') ? 'selected' : ''; ?>>Sobremesa</option>
                <option value="Aperitivo" <?php echo ($detalhesReceita['categoria'] == 'Aperitivo') ? 'selected' : ''; ?>>Aperitivo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="ingredientes" class="form-label">Ingredientes:</label>
            <textarea name="ingredientes" class="form-control" required><?php echo $detalhesReceita['ingredientes']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="instrucoes" class="form-label">Instruções:</label>
            <textarea name="instrucoes" class="form-control" required><?php echo $detalhesReceita['instrucoes']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="data_feita" class="form-label">Data Produção:</label>
            <input type="date" name="data_feita" class="form-control" value="<?php echo $detalhesReceita['data_feita']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição:</label>
            <textarea name="descricao" class="form-control" required><?php echo $detalhesReceita['descricao']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="tempo_preparacao" class="form-label">Tempo de Preparação (minutos):</label>
            <input type="number" name="tempo_preparacao" class="form-control" value="<?php echo $detalhesReceita['tempo_preparacao']; ?>" required>
        </div>

        <div class="mb-3">
            <input type="submit" class="btn btn-primary" value="Salvar Alterações">
            <a href="verreceita.php?id=<?php echo $receitaID; ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>


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



