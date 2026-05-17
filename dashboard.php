<?php
// admin/dashboard.php — Tableau de bord COCKTAIL'S
require "includes/connexion.php";

$page_title = "Tableau de bord";
$active_nav = "dashboard";
require "includes/header_admin.php";

// Chiffre d'affaires du mois en cours
$sql_ca  = "SELECT SUM(a.prix * r.nb_personnes) AS ca_mois
            FROM reservations r
            JOIN activites a ON r.activite_id = a.id_activite
            WHERE MONTH(r.date_resa) = MONTH(CURDATE())
            AND YEAR(r.date_resa) = YEAR(CURDATE())";
$res_ca  = mysqli_query($conn, $sql_ca);
$row_ca  = mysqli_fetch_assoc($res_ca);
$ca_mois = $row_ca["ca_mois"] ? number_format($row_ca["ca_mois"], 2, ',', ' ') : "0,00";

// Nombre de réservations aujourd'hui
$res_auj = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM reservations WHERE date_resa = CURDATE()");
$nb_auj  = mysqli_fetch_assoc($res_auj)["nb"];

// Nombre total de clients
$res_cl = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM clients");
$nb_cl  = mysqli_fetch_assoc($res_cl)["nb"];

// Activité la plus réservée
$sql_pop = "SELECT a.nom, a.fruit, COUNT(r.id_reservation) AS nb_resa
            FROM activites a
            JOIN reservations r ON r.activite_id = a.id_activite
            GROUP BY a.id_activite
            ORDER BY nb_resa DESC
            LIMIT 1";
$res_pop = mysqli_query($conn, $sql_pop);
$pop     = mysqli_fetch_assoc($res_pop);
$pop_nom = $pop ? $pop["fruit"] . " " . $pop["nom"] : "—";

// Réservations du jour
$sql_today = "SELECT r.nb_personnes, r.date_resa,
                     c.nom, c.prenom,
                     a.nom AS activite, a.fruit, a.prix
              FROM reservations r
              JOIN clients    c ON r.client_id   = c.id_client
              JOIN activites  a ON r.activite_id = a.id_activite
              WHERE r.date_resa = CURDATE()
              ORDER BY c.nom ASC";
$res_today = mysqli_query($conn, $sql_today);
?>

<div class="stats-grid">

  <div class="stat-card orange">
    <div class="num"><?= $ca_mois ?>€</div>
    <div class="lbl">Chiffre d'affaires du mois</div>
  </div>

  <div class="stat-card red">
    <div class="num"><?= $nb_auj ?></div>
    <div class="lbl">Réservations aujourd'hui</div>
  </div>

  <div class="stat-card">
    <div class="num"><?= $nb_cl ?></div>
    <div class="lbl">Clients enregistrés</div>
  </div>

  <div class="stat-card green">
    <div class="num" style="font-size:1.1rem;line-height:1.3;"><?= $pop_nom ?></div>
    <div class="lbl">Activité la plus réservée</div>
  </div>

</div>

<div class="bloc">
  <div class="bloc-header">
    <h2>📋 Réservations du jour</h2>
    <a href="reservations.php" class="btn btn-primary btn-sm">Voir tout</a>
  </div>

  <?php if (!$res_today || mysqli_num_rows($res_today) === 0) { ?>
    <p style="color:var(--mid);font-size:0.9rem;">Aucune réservation pour aujourd'hui.</p>
  <?php } else { ?>
  <table>
    <thead>
      <tr>
        <th>Client</th>
        <th>Activité</th>
        <th>Personnes</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($r = mysqli_fetch_assoc($res_today)) { ?>
      <tr>
        <td><?= $r["prenom"] . " " . $r["nom"] ?></td>
        <td><span class="badge-fruit"><?= $r["fruit"] ?> <?= $r["activite"] ?></span></td>
        <td><?= $r["nb_personnes"] ?></td>
        <td><strong><?= number_format($r["prix"] * $r["nb_personnes"], 2, ',', ' ') ?>€</strong></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
</div>

<?php require "includes/footer_admin.php"; ?>
