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

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário 
    $nomeCompleto = $_POST['nome_completo'];
    $morada = $_POST['morada'];
    $numeroTelefone = $_POST['numero_telefone'];

    // Inserir ou atualizar dados pessoais na base de dados
    function inserirAtualizarDadosPessoais($userID, $nomeCompleto, $morada, $numeroTelefone) {
        // Obter uma conexão PDO
        $conn = pdo_connect_mysql();

        // Verificar se já existem dados pessoais para este utilizador
        $stmtCheck = $conn->prepare("SELECT * FROM dados_pessoais WHERE id_usuario = ?");
        $stmtCheck->bindParam(1, $userID);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Se existir, atualizar os dados pessoais
            $stmt = $conn->prepare("UPDATE dados_pessoais SET nome_completo=?, morada=?, numero_telefone=? WHERE id_usuario=?");
        } else {
            // Se não existir, inserir os dados pessoais
            $stmt = $conn->prepare("INSERT INTO dados_pessoais (id_usuario, nome_completo, morada, numero_telefone) VALUES (?, ?, ?, ?)");
        }

        // Atualizar ou inserir os dados pessoais
        $stmt->bindParam(1, $userID);
        $stmt->bindParam(2, $nomeCompleto);
        $stmt->bindParam(3, $morada);
        $stmt->bindParam(4, $numeroTelefone);
        $stmt->execute();
    }

    // Chamar a função para inserir ou atualizar dados pessoais
    inserirAtualizarDadosPessoais($userID, $nomeCompleto, $morada, $numeroTelefone);

    // Contar o número de receitas inseridas pelo utilizador
    $conn = pdo_connect_mysql();
    $stmtCount = $conn->prepare("SELECT COUNT(id) as qtd_receitas FROM receitas WHERE id_usuario = ?");
    $stmtCount->bindParam(1, $userID);
    $stmtCount->execute();
    $result = $stmtCount->fetch(PDO::FETCH_ASSOC);
    $qtdReceitas = $result['qtd_receitas'];

    // Atualizar a quantidade de receitas na tabela de dados pessoais
    $stmtUpdateCount = $conn->prepare("UPDATE dados_pessoais SET qtd_receitas = ? WHERE id_usuario = ?");
    $stmtUpdateCount->bindParam(1, $qtdReceitas);
    $stmtUpdateCount->bindParam(2, $userID);
    $stmtUpdateCount->execute();

    // Redirecionar para a página de visualização de dados pessoais ou outra página desejada
    header("Location: perfilutilizador.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Dados Pessoais</title>

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
        <h1>Editar Dados Pessoais</h1>

        <form method="post" action="">
            <div class="mb-3">
                <label for="nome_completo" class="form-label">Nome Completo:</label>
                <input type="text" name="nome_completo" class="form-control" value="<?php echo $dadosPessoais['nome_completo']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="morada" class="form-label">Morada:</label>
                <input type="text" name="morada" class="form-control" value="<?php echo $dadosPessoais['morada']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="numero_telefone" class="form-label">Número de Telefone:</label>
                <input type="text" name="numero_telefone" class="form-control" value="<?php echo $dadosPessoais['numero_telefone']; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Atualizar Dados Pessoais</button>
        </form>

        <div class="mt-3">
            <a href="perfilutilizador.php" class="btn btn-secondary">Voltar</a>
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