<?php
// admin/clients.php — Liste des clients + réservations (activités et cocktails)
require "includes/connexion.php";

$page_title = "Clients";
$active_nav = "clients";
require "includes/header_admin.php";

$sql_clients = "SELECT * FROM clients ORDER BY nom ASC";
$res_clients = mysqli_query($conn, $sql_clients);

$client_choisi = isset($_GET["client_id"]) ? $_GET["client_id"] : "";
$nom_client    = "";
$res_act       = null;
$res_ck        = null;

if ($client_choisi != "") {
    $sql_info   = "SELECT * FROM clients WHERE id_client = '$client_choisi'";
    $info       = mysqli_fetch_assoc(mysqli_query($conn, $sql_info));
    $nom_client = $info["prenom"] . " " . $info["nom"];

    // Réservations activités du client
    $res_act = mysqli_query($conn,
        "SELECT r.date_resa, r.nb_personnes,
                a.nom AS service, a.fruit AS service_emoji, a.prix
         FROM reservations r
         JOIN activites a ON r.activite_id = a.id_activite
         WHERE r.client_id = '$client_choisi'
         AND (r.type_resa = 'activite' OR r.type_resa IS NULL)
         ORDER BY r.date_resa DESC"
    );

    // Réservations cocktails du client
    $res_ck = mysqli_query($conn,
        "SELECT r.date_resa, r.nb_personnes,
                ck.nom AS service, ck.emoji AS service_emoji, ck.prix
         FROM reservations r
         JOIN cocktails ck ON r.cocktail_id = ck.id_cocktail
         WHERE r.client_id = '$client_choisi'
         AND r.type_resa = 'cocktail'
         ORDER BY r.date_resa DESC"
    );
}
?>

<!-- ── Liste des clients ── -->
<div class="bloc">
  <div class="bloc-header">
    <h2>👥 Liste des clients</h2>
    <span style="font-size:0.82rem;color:var(--mid);"><?= mysqli_num_rows($res_clients) ?> client(s)</span>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Réservations</th>
      </tr>
    </thead>
    <tbody>
      <?php
      mysqli_data_seek($res_clients, 0);
      while ($c = mysqli_fetch_assoc($res_clients)) {
          $nb = mysqli_fetch_assoc(mysqli_query($conn,
              "SELECT COUNT(*) AS nb FROM reservations WHERE client_id = '" . $c["id_client"] . "'"
          ))["nb"];
      ?>
      <tr>
        <td style="color:var(--mid);"><?= $c["id_client"] ?></td>
        <td><?= $c["nom"] ?></td>
        <td><?= $c["prenom"] ?></td>
        <td><?= $c["email"] ?></td>
        <td><?= $c["telephone"] ? $c["telephone"] : "—" ?></td>
        <td>
          <a href="clients.php?client_id=<?= $c["id_client"] ?>" class="btn btn-primary btn-sm">
            <?= $nb ?> résa →
          </a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<!-- ── Réservations d'un client ── -->
<div class="bloc">
  <div class="bloc-header">
    <h2>📅 Réservations par client</h2>
  </div>

  <form method="GET" action="clients.php" style="margin-bottom:1.5rem;">
    <div class="filter-bar">
      <label>Choisir un client :</label>
      <select name="client_id" onchange="this.form.submit()">
        <option value="">— Sélectionner —</option>
        <?php
        $res2 = mysqli_query($conn, "SELECT * FROM clients ORDER BY nom ASC");
        while ($c = mysqli_fetch_assoc($res2)) {
            $selected = ($c["id_client"] == $client_choisi) ? "selected" : "";
            echo "<option value='" . $c["id_client"] . "' $selected>"
               . $c["nom"] . " " . $c["prenom"] . "</option>";
        }
        ?>
      </select>
    </div>
  </form>

  <?php if ($client_choisi == "") { ?>
    <p style="color:var(--mid);font-size:0.9rem;">Sélectionnez un client pour voir ses réservations.</p>

  <?php } else { ?>

    <!-- Activités du client -->
    <h2 style="margin-bottom:0.8rem;">🌊 Activités réservées</h2>
    <?php if (!$res_act || mysqli_num_rows($res_act) === 0) { ?>
      <p style="color:var(--mid);font-size:0.9rem;margin-bottom:1.5rem;">Aucune activité pour <strong><?= $nom_client ?></strong>.</p>
    <?php } else { ?>
    <table style="margin-bottom:2rem;">
      <thead>
        <tr>
          <th>Date</th>
          <th>Activité</th>
          <th>Personnes</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($r = mysqli_fetch_assoc($res_act)) { ?>
        <tr>
          <td><?= date("d/m/Y", strtotime($r["date_resa"])) ?></td>
          <td><span class="badge-fruit"><?= $r["service_emoji"] ?> <?= $r["service"] ?></span></td>
          <td><?= $r["nb_personnes"] ?></td>
          <td><strong><?= number_format($r["prix"] * $r["nb_personnes"], 2, ',', ' ') ?>€</strong></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php } ?>

    <!-- Cocktails du client -->
    <h2 style="margin-bottom:0.8rem;">🍹 Cocktails réservés</h2>
    <?php if (!$res_ck || mysqli_num_rows($res_ck) === 0) { ?>
      <p style="color:var(--mid);font-size:0.9rem;">Aucune formule cocktail pour <strong><?= $nom_client ?></strong>.</p>
    <?php } else { ?>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Cocktail</th>
          <th>Personnes</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($r = mysqli_fetch_assoc($res_ck)) { ?>
        <tr>
          <td><?= date("d/m/Y", strtotime($r["date_resa"])) ?></td>
          <td><span class="badge-fruit"><?= $r["service_emoji"] ?> <?= $r["service"] ?></span></td>
          <td><?= $r["nb_personnes"] ?></td>
          <td><strong><?= number_format($r["prix"] * $r["nb_personnes"], 2, ',', ' ') ?>€</strong></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php } ?>

  <?php } ?>
</div>

<?php require "includes/footer_admin.php"; ?>