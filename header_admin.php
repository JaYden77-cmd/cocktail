<?php
// includes/header_admin.php
session_start();

if (!isset($_SESSION["admin_connecte"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin — <?= $page_title ?> · COCKTAIL'S</title>
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
<div class="admin-wrap">

  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="dashboard.php">COCK<span>TAIL'S</span></a>
      <p>Administration</p>
    </div>
    <ul class="sidebar-nav">
      <li>
        <a href="dashboard.php" class="<?= $active_nav === 'dashboard' ? 'active' : '' ?>">
          <span class="ico">📊</span><span>Tableau de bord</span>
        </a>
      </li>
      <li>
        <a href="reservations.php" class="<?= $active_nav === 'reservations' ? 'active' : '' ?>">
          <span class="ico">📋</span><span>Réservations</span>
        </a>
      </li>
      <li>
        <a href="clients.php" class="<?= $active_nav === 'clients' ? 'active' : '' ?>">
          <span class="ico">👥</span><span>Clients</span>
        </a>
      </li>
      <li>
        <a href="services.php" class="<?= $active_nav === 'services' ? 'active' : '' ?>">
          <span class="ico">🌊</span><span>Services</span>
        </a>
      </li>
      <li>
        <a href="cocktails.php" class="<?= $active_nav === 'cocktails' ? 'active' : '' ?>">
          <span class="ico">🍹</span><span>Cocktails</span>
        </a>
      </li>
    </ul>
    <div class="sidebar-footer">
      <a href="../index.php">← Voir le site</a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <h1><?= $page_title ?></h1>
      <div class="topbar-right">
        <span>👤 <?= $_SESSION["admin_login"] ?></span>
        <a href="logout.php" class="btn-logout">Déconnexion</a>
      </div>
    </div>
    <div class="page-content">