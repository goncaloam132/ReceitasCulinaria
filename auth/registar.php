<?php

include "../db/connection.php";

// Função para registar o utilizador
function registrarUsuario($username, $email, $senha) {
    
    $conn = pdo_connect_mysql();

   
    $hashedSenha = password_hash($senha, PASSWORD_DEFAULT);

    
    $stmt = $conn->prepare("INSERT INTO usuarios (username, email, senha) VALUES (?, ?, ?)");

    
    $stmt->bindParam(1, $username);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $hashedSenha);

 
    $stmt->execute();

  
    $conn = null;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

   
    registrarUsuario($username, $email, $senha);

    
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo</title>

    <!-- Incluir CSS do Bootstrap via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

    <!-- Incluir JS do Bootstrap (já incluindo a dependência do Popper.js) via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>


    <!-- Incluir seu CSS personalizado -->
    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet" href="../main.css">
</head>
<body>

<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="../index.php" class="logo d-flex align-items-center me-auto me-lg-0">
            <h1>ReceitasCulinária<span>.</span></h1>
        </a>
    </div>
</header>

<section class="vh-100">
    <div class="container-fluid h-custom fade-up">
        <div class="row mt-3 d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5 fade-up">
                <img src="../assets/img/receitasculinaria.png" height="450">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form method="post" action="../auth/registar.php">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <p class="lead fw-normal mb-0 me-3">Registar com</p>
                        <button type="button" class="btn btn-primary btn-floating mx-1">
                            <i class="bi bi-facebook"></i>
                        </button>

                        <button type="button" class="btn btn-primary btn-floating mx-1">
                            <i class="bi bi-twitter"></i>
                        </button>

                        <button type="button" class="btn btn-primary btn-floating mx-1">
                            <i class="bi bi-google"></i>
                        </button>

                        <button type="button" class="btn btn-primary btn-floating mx-1">
                        <i class="bi bi-linkedin"></i>
                        </button>
                    </div>

                    <div class="divider d-flex align-items-center my-4">
                        <p class="text-center fw-bold mx-3 mb-0">Or</p>
                    </div>

                    <!-- Username input -->
                    <div class="form-outline mb-2">
                        <input type="text" name="username" class="form-control form-control-lg"
                            placeholder="Username" required />
                        <label class="form-label" for="form3Example3"></label>
                    </div>

                    <div class="form-outline mb-2">
                        <input type="email" name="email" class="form-control form-control-lg"
                            placeholder="Email" required />
                        <label class="form-label" for="form3Example3"></label>
                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-2">
                        <input type="password" name="senha" class="form-control form-control-lg"
                            placeholder="Password" required />
                        <label class="form-label" for="form3Example4"></label>
                    </div>


                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Registar</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Já tem uma conta? <a href="login.php"
                                class="link-primary">Entrar</a></p>
                    </div>
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