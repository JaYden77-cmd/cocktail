<?php
// admin/login.php — Connexion admin COCKTAIL'S
session_start();

if (isset($_SESSION["admin_connecte"])) {
    header("Location: dashboard.php");
    exit;
}

require "includes/connexion.php";

$erreur = "";

if (isset($_POST["connexion"])) {
    $login = $_POST["login"];
    $mdp   = $_POST["mot_de_passe"];

    $sql = "SELECT * FROM admin WHERE login = '$login' AND mot_de_passe = '$mdp'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) === 1) {
        $_SESSION["admin_connecte"] = true;
        $_SESSION["admin_login"]    = $login;
        header("Location: dashboard.php");
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion — COCKTAIL'S Admin</title>
  <link rel="stylesheet" href="admin.css" />
  <style>
    body {
      background: linear-gradient(135deg, var(--ocean) 0%, var(--ocean-mid) 60%, #0d9e8a 100%);
      display: flex; align-items: center; justify-content: center;
      min-height: 100vh;
    }
    .login-box {
      background: var(--white);
      border-radius: 1.2rem;
      padding: 2.8rem 3rem;
      width: 360px;
      box-shadow: 0 24px 60px rgba(0,0,0,0.25);
      text-align: center;
    }
    .login-logo {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2rem; letter-spacing: 0.06em;
      color: var(--ocean); margin-bottom: 0.2rem;
    }
    .login-logo span { color: var(--mango); }
    .login-box p { font-size: 0.82rem; color: var(--mid); margin-bottom: 2rem; }
    .login-box .field { text-align: left; margin-bottom: 1rem; }
    .login-box button {
      width: 100%; margin-top: 0.5rem;
      background: linear-gradient(90deg, var(--mango), var(--papaya));
      color: white; font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem; font-weight: 500;
      padding: 0.85rem; border: none; border-radius: 0.6rem;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(255,100,0,0.35);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .login-box button:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(255,100,0,0.5); }
    .back-link { display: block; margin-top: 1.2rem; font-size: 0.8rem; color: var(--mid); text-decoration: none; }
    .back-link:hover { color: var(--ocean); }
  </style>
</head>
<body>

<div class="login-box">
  <div class="login-logo">COCK<span>TAIL'S</span></div>
  <p>Espace administration</p>

  <?php if ($erreur) { ?>
    <div class="msg msg-err"><?= $erreur ?></div>
  <?php } ?>

  <form method="POST" action="login.php">
    <div class="field">
      <label>Identifiant</label>
      <input type="text" name="login" placeholder="admin" required />
    </div>
    <div class="field">
      <label>Mot de passe</label>
      <input type="password" name="mot_de_passe" placeholder="••••••••" required />
    </div>
    <button type="submit" name="connexion">Se connecter 🌊</button>
  </form>

  <a href="../index.php" class="back-link">← Retour au site</a>
</div>

</body>
</html>
