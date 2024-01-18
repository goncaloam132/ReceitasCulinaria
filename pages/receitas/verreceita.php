<?php
session_start();

// Incluir o arquivo de conexão
include_once '../../db/connection.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

// Obter o ID da receita a ser exibida 
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

// Variável $conn declarada fora do escopo da função
$conn = pdo_connect_mysql();

function isReceitaFavorita($userID, $receitaID) {
    global $conn; 
    $stmt = $conn->prepare("SELECT COUNT(*) FROM receitas_favoritas WHERE id_usuario = ? AND id_receita = ?");
    $stmt->execute([$userID, $receitaID]);
    $count = $stmt->fetchColumn();

    return ($count > 0);
}

function marcarReceitaFavorita($userID, $receitaID) {
    global $conn; // Adicione esta linha para acessar a variável $conn globalmente
    // Verifica se a receita já é favorita
    if (isReceitaFavorita($userID, $receitaID)) {
        // Se favorita, desmarca (exclui o registro)
        $stmt = $conn->prepare("DELETE FROM receitas_favoritas WHERE id_usuario = ? AND id_receita = ?");
        $stmt->execute([$userID, $receitaID]);
        return false; 
    } else {
        // Se não é favorita, marca (insere o registro)
        $stmt = $conn->prepare("INSERT INTO receitas_favoritas (id_usuario, id_receita) VALUES (?, ?)");
        $stmt->execute([$userID, $receitaID]);
        return true; // Retorna true indicando que a receita é agora favorita
    }
}

// Lógica para adicionar/remover dos favoritos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['favoritos'])) {
    $receitaID = $_POST['receitaID'];
    
    // Marcar ou desmarcar a receita como favorita
    if (marcarReceitaFavorita($userID, $receitaID)) {
        echo "Receita adicionada aos favoritos!";
    } else {
        echo "Receita removida dos favoritos!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receitas</title>

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
        <h1><?php echo $detalhesReceita['titulo']; ?></h1>

        <?php if (isset($detalhesReceita['foto_path'])): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="imagem-receita">
                        <img src="<?php echo $detalhesReceita['foto_path']; ?>" class="img-fluid rounded" alt="Foto da Receita">
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">Categoria: <?php echo isset($detalhesReceita['Categoria']) ? $detalhesReceita['Categoria'] : 'Categoria não definida'; ?></p>
                    <p class="mb-2">Ingredientes: <?php echo $detalhesReceita['ingredientes']; ?></p>
                    <p class="mb-2">Instruções: <?php echo $detalhesReceita['instrucoes']; ?></p>
                    <p class="mb-2">Data Produção: <?php echo $detalhesReceita['data_feita']; ?></p>
                    <p class="mb-2">Descrição: <?php echo $detalhesReceita['descricao']; ?></p>
                    <p class="mb-2">Tempo de Preparação: <?php echo $detalhesReceita['tempo_preparacao']; ?></p>

                    <form method="post" action="" class="mt-3">
                    <button type="submit" class="btn btn-warning me-2" name="favoritos">
                        <?php echo (isReceitaFavorita($userID, $receitaID)) ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos'; ?>
                    </button>
                    <input type="hidden" name="receitaID" value="<?php echo $receitaID; ?>">
                    </form>
                    <br>
                    <a href="editarreceita.php?id=<?php echo $receitaID; ?>" class="btn btn-primary me-2">Editar receita</a>
                    <a href="apagarreceita.php?id=<?php echo $receitaID; ?>" class="btn btn-danger me-2">Apagar receita</a>
                    <a href="receitas.php" class="btn btn-secondary">Voltar para as Minhas Receitas</a>
                </div>
            </div>
        <?php endif; ?>
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
