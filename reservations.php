<?php
// admin/reservations.php — Toutes les réservations (activités + cocktails)
require "includes/connexion.php";

$page_title = "Réservations";
$active_nav = "reservations";
require "includes/header_admin.php";

// ── Réservations de type activité ──
$sql_act = "SELECT r.id_reservation, r.date_resa, r.nb_personnes,
                   c.nom, c.prenom, c.email,
                   a.nom AS service, a.fruit AS service_emoji, a.prix,
                   'activite' AS type_resa
            FROM reservations r
            JOIN clients   c ON r.client_id   = c.id_client
            JOIN activites a ON r.activite_id = a.id_activite
            WHERE r.type_resa = 'activite' OR r.type_resa IS NULL
            ORDER BY r.date_resa DESC";
$res_act = mysqli_query($conn, $sql_act);

// ── Réservations de type cocktail ──
$sql_ck = "SELECT r.id_reservation, r.date_resa, r.nb_personnes,
                  c.nom, c.prenom, c.email,
                  ck.nom AS service, ck.emoji AS service_emoji, ck.prix,
                  'cocktail' AS type_resa
           FROM reservations r
           JOIN clients   c  ON r.client_id   = c.id_client
           JOIN cocktails ck ON r.cocktail_id = ck.id_cocktail
           WHERE r.type_resa = 'cocktail'
           ORDER BY r.date_resa DESC";
$res_ck = mysqli_query($conn, $sql_ck);

$nb_act = $res_act ? mysqli_num_rows($res_act) : 0;
$nb_ck  = $res_ck  ? mysqli_num_rows($res_ck)  : 0;
?>

<!-- ── Réservations activités ── -->
<div class="bloc">
  <div class="bloc-header">
    <h2>🌊 Réservations — Activités seules</h2>
    <span style="font-size:0.82rem;color:var(--mid);"><?= $nb_act ?> réservation(s)</span>
  </div>

  <?php if ($nb_act === 0) { ?>
    <p style="color:var(--mid);font-size:0.9rem;">Aucune réservation d'activité pour le moment.</p>
  <?php } else { ?>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Client</th>
        <th>Email</th>
        <th>Activité</th>
        <th>Personnes</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($r = mysqli_fetch_assoc($res_act)) { ?>
      <tr>
        <td style="color:var(--mid);">#<?= $r["id_reservation"] ?></td>
        <td><?= date("d/m/Y", strtotime($r["date_resa"])) ?></td>
        <td><strong><?= $r["prenom"] . " " . $r["nom"] ?></strong></td>
        <td style="font-size:0.82rem;color:var(--mid);"><?= $r["email"] ?></td>
        <td><span class="badge-fruit"><?= $r["service_emoji"] ?> <?= $r["service"] ?></span></td>
        <td><?= $r["nb_personnes"] ?></td>
        <td><strong><?= number_format($r["prix"] * $r["nb_personnes"], 2, ',', ' ') ?>€</strong></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
</div>

<!-- ── Réservations cocktails ── -->
<div class="bloc">
  <div class="bloc-header">
    <h2>🍹 Réservations — Formules Cocktails</h2>
    <span style="font-size:0.82rem;color:var(--mid);"><?= $nb_ck ?> réservation(s)</span>
  </div>

  <?php if ($nb_ck === 0) { ?>
    <p style="color:var(--mid);font-size:0.9rem;">Aucune réservation de cocktail pour le moment.</p>
  <?php } else { ?>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Client</th>
        <th>Email</th>
        <th>Cocktail</th>
        <th>Personnes</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($r = mysqli_fetch_assoc($res_ck)) { ?>
      <tr>
        <td style="color:var(--mid);">#<?= $r["id_reservation"] ?></td>
        <td><?= date("d/m/Y", strtotime($r["date_resa"])) ?></td>
        <td><strong><?= $r["prenom"] . " " . $r["nom"] ?></strong></td>
        <td style="font-size:0.82rem;color:var(--mid);"><?= $r["email"] ?></td>
        <td><span class="badge-fruit"><?= $r["service_emoji"] ?> <?= $r["service"] ?></span></td>
        <td><?= $r["nb_personnes"] ?></td>
        <td><strong><?= number_format($r["prix"] * $r["nb_personnes"], 2, ',', ' ') ?>€</strong></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
</div>

<?php require "includes/footer_admin.php"; ?>