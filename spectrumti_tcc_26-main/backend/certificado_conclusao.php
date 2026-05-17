<?php

session_start();

/*
========================================
VERIFICAR LOGIN
========================================
*/

if (!isset($_SESSION['user_id'])) {
    die("Usuário não está logado.");
}

/*
========================================
CONEXÃO
========================================
*/

$host = "localhost";
$user = "gabrielkafferDS";
$password = "gabrielkafferDS123@";
$database = "spectrum";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

/*
========================================
PEGAR DADOS
========================================
*/

$id_user = $_SESSION['user_id'];

$id_trilha = isset($_GET['id_trilha'])
    ? intval($_GET['id_trilha'])
    : 0;

if ($id_trilha <= 0) {
    die("ID da trilha inválido.");
}

/*
========================================
BUSCAR USUÁRIO
========================================
*/

$sql_usuario = "
SELECT nome
FROM usuarios
WHERE id = ?
";

$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $id_user);
$stmt_usuario->execute();

$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

if (!$usuario) {
    die("Usuário não encontrado.");
}

/*
========================================
BUSCAR TRILHA + PROGRESSO
========================================
*/

$sql_trilha = "
SELECT
    t.nome AS nome_trilha,
    pt.data_inicio,
    pt.data_conclusao

FROM trilha t

INNER JOIN progresso_trilha pt
ON pt.id_trilha = t.id_trilha

WHERE t.id_trilha = ?
AND pt.id_user = ?
";

$stmt_trilha = $conn->prepare($sql_trilha);
$stmt_trilha->bind_param("ii", $id_trilha, $id_user);
$stmt_trilha->execute();

$result_trilha = $stmt_trilha->get_result();
$trilha = $result_trilha->fetch_assoc();

if (!$trilha) {
    die("Trilha não encontrada.");
}

/*
========================================
SOMAR CARGA HORÁRIA
========================================
*/

$sql_horas = "
SELECT SUM(duracao) AS total_horas
FROM cursos
WHERE id_trilha = ?
";

$stmt_horas = $conn->prepare($sql_horas);
$stmt_horas->bind_param("i", $id_trilha);
$stmt_horas->execute();

$result_horas = $stmt_horas->get_result();
$horas = $result_horas->fetch_assoc();

/*
========================================
VARIÁVEIS
========================================
*/

$nome_usuario = $usuario['nome'];

$nome_trilha = $trilha['nome_trilha'];

$data_inicio = !empty($trilha['data_inicio'])
    ? date("d/m/Y", strtotime($trilha['data_inicio']))
    : "Não informado";

$data_conclusao = !empty($trilha['data_conclusao'])
    ? date("d/m/Y", strtotime($trilha['data_conclusao']))
    : "Não informado";

$total_horas = $horas['total_horas'] ?? 0;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Certificado de Conclusão - SpectrumTI
    </title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel:wght@500;700;800&family=Montserrat:wght@300;400;600;700&display=swap">

    <link rel="stylesheet"
        href="../css/certificado_conclusao.css">
</head>

<body>

    <header class="header">

        <div class="header-left">

            <div class="logo-container">
                <img src="../Imagens/logo.png" alt="Logo" class="logo">
                <span class="school-name">SpectrumTI</span>
            </div>

            <nav class="nav-menu">
                <a href="../html/trilha_cursos.html">Minha Página</a>
                <a href="../backend/meus_certificados.php">Meus Certificados</a>
                <a href="#">Ensine na Spectrum</a>
            </nav>

        </div>

        <div class="user-profile">

            <div class="user-info">
                <span>Olá,</span>

                <strong id="user-name">
                    <?= htmlspecialchars($nome_usuario) ?>
                </strong>
            </div>

            <div id="user-icon" class="user-icon">
                👤
            </div>

            <div id="user-dropdown" class="user-dropdown">
                <a href="../html/perfil.html">Perfil</a>
                <a href="../backend/logout.php">Sair</a>
            </div>

        </div>

    </header>

    <div class="certificado-wrapper">

        <div class="certificate-container">

            <div class="wave-top-blue-curve"></div>
            <div class="wave-top-blue"></div>
            <div class="gold-curve"></div>

            <div class="vertical-ribbon"></div>

            <div class="seal-ribbon-down-1"></div>
            <div class="seal-ribbon-down-2"></div>

            <div class="gold-seal">
                <span class="seal-text-top">SPECTRUM</span>
                <span class="seal-text-main">OFICIAL</span>
                <span class="seal-text-sub">CERTIFICADO</span>
            </div>

            <div class="certificate-header">

                <h1 class="main-title">
                    CERTIFICADO
                </h1>

                <p class="sub-title">
                    de conclusão
                </p>

            </div>

            <div class="certificate-body">

                <p class="presentation-text">
                    Este certificado é orgulhosamente apresentado a
                </p>

                <div class="certificate-user-name">
                    <?= htmlspecialchars($nome_usuario) ?>
                </div>

                <p class="course-text">
                    por ter concluído com êxito e dedicação todos os módulos e requisitos práticos da trilha de especialização profissional em
                </p>

                <div class="course-name">
                    <?= htmlspecialchars($nome_trilha) ?>
                </div>

                <div class="meta-info-group">

                    <div class="meta-item">
                        Carga Horária:
                        <strong>
                            <?= $total_horas ?> horas
                        </strong>
                    </div>

                    <div class="meta-item">
                        Início:
                        <strong>
                            <?= $data_inicio ?>
                        </strong>
                    </div>

                    <div class="meta-item">
                        Término:
                        <strong>
                            <?= $data_conclusao ?>
                        </strong>
                    </div>

                </div>

            </div>

            <div class="certificate-footer">

                <div class="signature-block">

                    <div class="assinatura-diretor">
                        Gabriel Kaffer
                    </div>

                    <div class="signature-line">
                        Diretor Acadêmico
                    </div>

                </div>

                <div class="footer-badges">

                    <svg class="mini-badge"
                        viewBox="0 0 50 50"
                        fill="#cc9933">

                        <path d="M25 5C13.95 5 5 13.95 5 25s8.95 20 20 20 20-8.95 20-20S36.05 5 25 5zm0 36c-8.82 0-16-7.18-16-16S16.18 9 25 9s16 7.18 16 16-7.18 16-16 16z"/>

                        <path d="M25 13 l3 7 h7 l-5 5 2 7 -7 -4 -7 4 2 -7 -5 -5 h7 z"/>

                    </svg>

                    <svg class="mini-badge"
                        viewBox="0 0 50 50"
                        fill="#14243b">

                        <path d="M25 4C13.4 4 4 13.4 4 25s9.4 21 21 21 21-9.4 21-21S36.6 4 25 4zm0 4c9.4 0 17 7.6 17 17s-7.6 17-17 17S8 34.4 8 25 15.6 8 25 8z"/>

                    </svg>

                </div>

            </div>

        </div>

    </div>

</body>
</html>