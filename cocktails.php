<?php
// admin/cocktails.php — Gestion des formules cocktails
require "includes/connexion.php";

$page_title = "Cocktails";
$active_nav = "cocktails";
$message    = "";

// ── Supprimer un cocktail ──
if (isset($_GET["supprimer"])) {
    $id  = mysqli_real_escape_string($conn, $_GET["supprimer"]);
    $sql = "DELETE FROM cocktails WHERE id_cocktail = '$id'";
    mysqli_query($conn, $sql);
    $message = "ok:Cocktail supprimé.";
}

// ── Modifier le prix d'un cocktail ──
if (isset($_POST["modifier_prix"])) {
    $id    = mysqli_real_escape_string($conn, $_POST["id"]);
    $prix  = mysqli_real_escape_string($conn, $_POST["prix"]);
    $sql   = "UPDATE cocktails SET prix = '$prix' WHERE id_cocktail = '$id'";
    mysqli_query($conn, $sql);
    $message = "ok:Prix mis à jour.";
}

// ── Ajouter un cocktail ──
if (isset($_POST["ajouter"])) {
    $nom        = mysqli_real_escape_string($conn, $_POST["nom"]);
    $sous_titre = mysqli_real_escape_string($conn, $_POST["sous_titre"]);
    $description= mysqli_real_escape_string($conn, $_POST["description"]);
    $prix       = mysqli_real_escape_string($conn, $_POST["prix"]);
    $emoji      = mysqli_real_escape_string($conn, $_POST["emoji"]);
    $contenu    = mysqli_real_escape_string($conn, $_POST["contenu"]);

    $sql = "INSERT INTO cocktails (nom, sous_titre, description, prix, emoji, contenu)
            VALUES ('$nom', '$sous_titre', '$description', '$prix', '$emoji', '$contenu')";
    mysqli_query($conn, $sql);
    $message = "ok:Cocktail ajouté avec succès !";
}

require "includes/header_admin.php";

if ($message) {
    $type  = strpos($message, "ok:") === 0 ? "ok" : "err";
    $texte = substr($message, 3);
    echo "<div class='msg msg-$type'>$texte</div>";
}

// Récupérer tous les cocktails
$sql = "SELECT * FROM cocktails ORDER BY id_cocktail ASC";
$res = mysqli_query($conn, $sql);

// Nombre de réservations par cocktail (pour info)
?>

<!-- ── Liste des cocktails ── -->
<div class="bloc">
  <div class="bloc-header">
    <h2>🍹 Liste des formules cocktails</h2>
    <span style="font-size:0.82rem;color:var(--mid);"><?= mysqli_num_rows($res) ?> formule(s)</span>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Emoji</th>
        <th>Nom</th>
        <th>Sous-titre</th>
        <th>Contenu</th>
        <th>Prix</th>
        <th>Réservations</th>
        <th>Modifier prix</th>
        <th>Supprimer</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($ck = mysqli_fetch_assoc($res)) {
          // Compte les réservations de ce cocktail
          $nb = mysqli_fetch_assoc(mysqli_query($conn,
              "SELECT COUNT(*) AS nb FROM reservations WHERE cocktail_id = '" . $ck["id_cocktail"] . "'"
          ))["nb"];
      ?>
      <tr>
        <td style="color:var(--mid);"><?= $ck["id_cocktail"] ?></td>
        <td style="font-size:1.4rem;"><?= $ck["emoji"] ?></td>
        <td><strong><?= $ck["nom"] ?></strong></td>
        <td style="font-size:0.82rem;color:var(--mid);"><?= $ck["sous_titre"] ?></td>
        <td style="max-width:200px;font-size:0.78rem;color:var(--mid);"><?= $ck["contenu"] ?></td>
        <td><strong><?= $ck["prix"] ?>€</strong></td>
        <td>
          <span class="badge-fruit">🍹 <?= $nb ?></span>
        </td>
        <td>
          <!-- Formulaire inline modifier le prix -->
          <form method="POST" action="cocktails.php" style="display:flex;gap:0.4rem;align-items:center;">
            <input type="hidden" name="id" value="<?= $ck["id_cocktail"] ?>" />
            <input type="number" name="prix" value="<?= $ck["prix"] ?>"
                   step="0.01" min="0"
                   style="width:70px;padding:0.3rem 0.5rem;border:1.5px solid #cde8ed;border-radius:0.4rem;font-size:0.82rem;" />
            <button type="submit" name="modifier_prix" class="btn btn-mango btn-sm">OK</button>
          </form>
        </td>
        <td>
          <a href="cocktails.php?supprimer=<?= $ck["id_cocktail"] ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Supprimer ce cocktail ?')">
            Supprimer
          </a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<!-- ── Réservations par cocktail ── -->
<div class="bloc">
  <div class="bloc-header">
    <h2>📅 Réservations par cocktail</h2>
  </div>
  <?php
  // On liste toutes les réservations de type cocktail avec le nom du client et du cocktail
  $sql_resa = "SELECT r.id_reservation, r.date_resa, r.nb_personnes,
                      c.nom, c.prenom,
                      ck.nom AS cocktail, ck.emoji, ck.prix
               FROM reservations r
               JOIN clients  c  ON r.client_id   = c.id_client
               JOIN cocktails ck ON r.cocktail_id = ck.id_cocktail
               WHERE r.type_resa = 'cocktail'
               ORDER BY r.date_resa DESC";
  $res_resa = mysqli_query($conn, $sql_resa);

  if (!$res_resa || mysqli_num_rows($res_resa) === 0) { ?>
    <p style="color:var(--mid);font-size:0.9rem;">Aucune réservation de cocktail pour le moment.</p>
  <?php } else { ?>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Client</th>
        <th>Cocktail</th>
        <th>Personnes</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($r = mysqli_fetch_assoc($res_resa)) { ?>
      <tr>
        <td style="color:var(--mid);">#<?= $r["id_reservation"] ?></td>
        <td><?= date("d/m/Y", strtotime($r["date_resa"])) ?></td>
        <td><strong><?= $r["prenom"] . " " . $r["nom"] ?></strong></td>
        <td><span class="badge-fruit"><?= $r["emoji"] ?> <?= $r["cocktail"] ?></span></td>
        <td><?= $r["nb_personnes"] ?></td>
        <td><strong><?= number_format($r["prix"] * $r["nb_personnes"], 2, ',', ' ') ?>€</strong></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
</div>

<!-- ── Ajouter un cocktail ── -->
<div class="bloc">
  <div class="bloc-header">
    <h2>➕ Ajouter une formule cocktail</h2>
  </div>
  <form method="POST" action="cocktails.php">
    <div class="form-grid">

      <div class="field">
        <label>Nom du cocktail</label>
        <input type="text" name="nom" placeholder="Ex : Sunset Papaye" required />
      </div>

      <div class="field">
        <label>Emoji</label>
        <select name="emoji" required>
          <option value="">— Choisir —</option>
          <option value="🍹">🍹 Cocktail tropical</option>
          <option value="🌊">🌊 Vague</option>
          <option value="🫙">🫙 Bocal</option>
          <option value="🍑">🍑 Pêche</option>
          <option value="🌅">🌅 Coucher de soleil</option>
          <option value="💙">💙 Blue</option>
          <option value="🍸">🍸 Cocktail classique</option>
          <option value="🌺">🌺 Fleur</option>
        </select>
      </div>

      <div class="field">
        <label>Sous-titre</label>
        <input type="text" name="sous_titre" placeholder="Ex : Douceur tropicale" />
      </div>

      <div class="field">
        <label>Prix (€)</label>
        <input type="number" name="prix" step="0.01" min="0" placeholder="75.00" required />
      </div>

      <div class="field full">
        <label>Contenu (activités incluses séparées par " + ")</label>
        <input type="text" name="contenu"
               placeholder="Ex : 🥭 Surf Mangue (demi-journée) + 🥥 Voile Noix de Coco (demi-journée)" />
      </div>

      <div class="field full">
        <label>Description</label>
        <textarea name="description" placeholder="Décrivez la formule…"></textarea>
      </div>

      <div class="field full">
        <button type="submit" name="ajouter" class="btn btn-success">Ajouter la formule</button>
      </div>

    </div>
  </form>
</div>

<?php require "includes/footer_admin.php"; ?>