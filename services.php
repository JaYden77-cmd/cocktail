<?php
// admin/services.php — Gestion des activités
require "includes/connexion.php";

$page_title = "Services";
$active_nav = "services";
$message    = "";

// Supprimer un service
if (isset($_GET["supprimer"])) {
    $id  = $_GET["supprimer"];
    $sql = "DELETE FROM activites WHERE id_activite = '$id'";
    mysqli_query($conn, $sql);
    $message = "ok:Service supprimé.";
}

// Modifier le prix
if (isset($_POST["modifier_prix"])) {
    $id           = $_POST["id"];
    $nouveau_prix = $_POST["prix"];
    $sql = "UPDATE activites SET prix = '$nouveau_prix' WHERE id_activite = '$id'";
    mysqli_query($conn, $sql);
    $message = "ok:Prix mis à jour.";
}

// Ajouter un service
if (isset($_POST["ajouter"])) {
    $nom         = $_POST["nom"];
    $description = $_POST["description"];
    $prix        = $_POST["prix"];
    $duree       = $_POST["duree"];
    $fruit       = $_POST["fruit"];
    $sql = "INSERT INTO activites (nom, description, prix, duree, fruit)
            VALUES ('$nom', '$description', '$prix', '$duree', '$fruit')";
    mysqli_query($conn, $sql);
    $message = "ok:Service ajouté avec succès !";
}

require "includes/header_admin.php";

if ($message) {
    $type  = strpos($message, "ok:") === 0 ? "ok" : "err";
    $texte = substr($message, 3);
    echo "<div class='msg msg-$type'>$texte</div>";
}

$sql = "SELECT * FROM activites ORDER BY id_activite ASC";
$res = mysqli_query($conn, $sql);
?>

<!-- Liste des services -->
<div class="bloc">
  <div class="bloc-header">
    <h2>🌊 Liste des services</h2>
    <span style="font-size:0.82rem;color:var(--mid);"><?= mysqli_num_rows($res) ?> service(s)</span>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Fruit</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Durée</th>
        <th>Modifier prix</th>
        <th>Supprimer</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($a = mysqli_fetch_assoc($res)) { ?>
      <tr>
        <td style="color:var(--mid);"><?= $a["id_activite"] ?></td>
        <td style="font-size:1.4rem;"><?= $a["fruit"] ?></td>
        <td><?= $a["nom"] ?></td>
        <td style="max-width:200px;font-size:0.82rem;color:var(--mid);"><?= $a["description"] ?></td>
        <td><strong><?= $a["prix"] ?>€</strong></td>
        <td><?= $a["duree"] ?></td>
        <td>
          <form method="POST" action="services.php" style="display:flex;gap:0.4rem;align-items:center;">
            <input type="hidden" name="id" value="<?= $a["id_activite"] ?>" />
            <input type="number" name="prix" value="<?= $a["prix"] ?>"
                   step="0.01" min="0"
                   style="width:70px;padding:0.3rem 0.5rem;border:1.5px solid #cde8ed;border-radius:0.4rem;font-size:0.82rem;" />
            <button type="submit" name="modifier_prix" class="btn btn-mango btn-sm">OK</button>
          </form>
        </td>
        <td>
          <a href="services.php?supprimer=<?= $a["id_activite"] ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Supprimer ce service ?')">
            Supprimer
          </a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<!-- Ajouter un service -->
<div class="bloc">
  <div class="bloc-header">
    <h2>➕ Ajouter un service</h2>
  </div>
  <form method="POST" action="services.php">
    <div class="form-grid">
      <div class="field">
        <label>Nom de l'activité</label>
        <input type="text" name="nom" placeholder="Ex : Wakeboard Ananas" required />
      </div>
      <div class="field">
        <label>Emoji fruit</label>
        <select name="fruit" required>
          <option value="">— Choisir —</option>
          <option value="🥭">🥭 Mangue</option>
          <option value="🍉">🍉 Pastèque</option>
          <option value="🥝">🥝 Kiwi</option>
          <option value="🍈">🍈 Fruit de la Passion</option>
          <option value="🍑">🍑 Pêche</option>
          <option value="🥥">🥥 Noix de Coco</option>
          <option value="🍍">🍍 Ananas</option>
          <option value="🍋">🍋 Citron</option>
          <option value="🍊">🍊 Orange</option>
        </select>
      </div>
      <div class="field">
        <label>Prix (€)</label>
        <input type="number" name="prix" step="0.01" min="0" placeholder="35.00" required />
      </div>
      <div class="field">
        <label>Durée</label>
        <input type="text" name="duree" placeholder="Ex : 1h, demi-journée, 30 min" required />
      </div>
      <div class="field full">
        <label>Description</label>
        <textarea name="description" placeholder="Décrivez l'activité…"></textarea>
      </div>
      <div class="field full">
        <button type="submit" name="ajouter" class="btn btn-success">Ajouter le service</button>
      </div>
    </div>
  </form>
</div>

<?php require "includes/footer_admin.php"; ?>
