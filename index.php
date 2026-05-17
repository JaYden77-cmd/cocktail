<?php
// index.php — COCKTAIL'S
require "includes/connexion.php";

$message = "";
$erreur  = "";

if (isset($_POST["reserver"])) {
    $nom        = $_POST["nom"];
    $prenom     = $_POST["prenom"];
    $email      = $_POST["email"];
    $telephone  = $_POST["telephone"];
    $date_resa  = $_POST["date_resa"];
    $nb_pers    = $_POST["nb_personnes"];
    $type_resa  = $_POST["type_resa"];  // 'activite' ou 'cocktail'

    // 1. Insérer le client
    $sql = "INSERT INTO clients (nom, prenom, email, telephone) VALUES ('$nom', '$prenom', '$email', '$telephone')";
    mysqli_query($conn, $sql);
    $client_id = mysqli_insert_id($conn);

    if ($type_resa === "cocktail") {
        // ── Réservation d'un cocktail ──
        $cocktail_id = $_POST["cocktail_id"];

        // Récupère le nom du cocktail pour la vérification
        $res_ck_info = mysqli_query($conn, "SELECT nom FROM cocktails WHERE id_cocktail = '$cocktail_id'");
        $ck_info     = mysqli_fetch_assoc($res_ck_info);

        // Vérification : Péché Mignon nécessite au moins 3 personnes
        if ($ck_info["nom"] === "Péché Mignon" && $nb_pers < 3) {
            $erreur = "Le cocktail 🍑 Péché Mignon est une formule groupe : il requiert un minimum de 3 personnes. Vous avez indiqué " . $nb_pers . " personne(s).";
        } else {
            $sql = "INSERT INTO reservations (client_id, activite_id, date_resa, nb_personnes, type_resa, cocktail_id)
                    VALUES ('$client_id', NULL, '$date_resa', '$nb_pers', 'cocktail', '$cocktail_id')";
            mysqli_query($conn, $sql);
            $message = $prenom;
        }

    } else {
        // ── Réservation d'une activité seule ──
        $activite_id = $_POST["activite_id"];

        // Vérification : maximum 6 personnes pour une activité seule
        if ($nb_pers > 6) {
            $erreur = "Les activités seules sont limitées à 6 personnes maximum. Pour un groupe plus large, pensez à nos formules Cocktails !";
        } else {
            // Vérification doublon
            $sql_check = "SELECT COUNT(*) AS nb FROM reservations
                          WHERE activite_id = '$activite_id' AND date_resa = '$date_resa'";
            $nb_exist  = mysqli_fetch_assoc(mysqli_query($conn, $sql_check))["nb"];

            if ($nb_exist > 0) {
                $res_nom  = mysqli_query($conn, "SELECT fruit, nom FROM activites WHERE id_activite = '$activite_id'");
                $act_info = mysqli_fetch_assoc($res_nom);
                $erreur   = "L'activité " . $act_info["fruit"] . " " . $act_info["nom"] . " est déjà réservée le " . date("d/m/Y", strtotime($date_resa)) . ". Veuillez choisir une autre date.";
            } else {
                $sql = "INSERT INTO reservations (client_id, activite_id, date_resa, nb_personnes, type_resa, cocktail_id)
                        VALUES ('$client_id', '$activite_id', '$date_resa', '$nb_pers', 'activite', NULL)";
                mysqli_query($conn, $sql);
                $message = $prenom;
            }
        }
    }
}

// Récupération des activités et cocktails pour les sections et le formulaire
$res_activites = mysqli_query($conn, "SELECT * FROM activites ORDER BY id_activite ASC");
$res_cocktails = mysqli_query($conn, "SELECT * FROM cocktails ORDER BY id_cocktail ASC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>COCKTAIL'S — Location d'Activités Nautiques</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/cocktails.css" />
</head>
<body>

  <nav id="navbar">
    <a href="#accueil" class="nav-logo">COCKTAIL'S</a>
    <ul class="nav-links">
      <li><a href="#activites">Activités</a></li>
      <li><a href="#cocktails">Cocktails</a></li>
      <li><a href="#destinations">Destinations</a></li>
      <li><a href="#reserver">Réserver</a></li>
    </ul>
    <a href="#reserver" class="nav-cta">Réserver</a>
  </nav>

  <section id="accueil">
    <div class="hero-fallback"></div>
    <div class="hero-fruits" id="heroFruits"></div>
    <div class="hero-vignette"></div>
    <div class="hero-content">
      <h1 class="hero-title">
        COCK<span class="accent">TAIL'S</span>
        <span class="line2">l'océan a le goût des fruits exotiques</span>
      </h1>
      <p class="hero-desc">Quel délice fruité des îles choisirez vous aujourd'hui ?</p>
      <div class="hero-btns">
        <a href="#activites" class="btn-hero-primary">Découvrir les activités</a>
        <a href="#reserver"  class="btn-hero-outline">Réserver maintenant</a>
      </div>
    </div>
    <div class="scroll-hint"><span>Scroll</span><div class="arrow"></div></div>
    <div class="hero-wave"></div>
  </section>

  <!-- ── Intro activités ── -->
  <div class="section-intro reveal" id="activites">
    <div class="label-sup">Notre catalogue</div>
    <h2>Nos fruits, votre dégustation</h2>
    <p>Chaque expérience nautique porte le nom et les couleurs d'un fruit exotique.</p>
  </div>

  <!-- ── Sections activités ── -->
  <!-- SURF MANGUE -->
  <div class="activite-section reveal">
    <div class="act-visuel act-mango" data-emoji="🥭">
      <video class="act-video" muted loop playsinline preload="none">
        <source src="surf.mp4" type="video/mp4">
      </video>
      <div class="act-emoji">🥭</div>
      <div class="act-prix" style="color:#7a3800;">35€</div>
      <div class="act-duree" style="color:#7a3800;">⏱ Demi-journée</div>
    </div>
    <div class="act-texte">
      <div class="act-tag tag-mango">Surf · Tous niveaux</div>
      <div class="act-nom">Surf Mangue</div>
      <p class="act-desc">Planches aux teintes orangé-doré façon mangue mûre. Location à la demi-journée, instructeur disponible pour les débutants. Matériel inclus.</p>
      <a href="#reserver" class="btn-act btn-mango">Réserver <span class="arr">→</span></a>
    </div>
  </div>

  <!-- PARCOURS PASTÈQUE -->
  <div class="activite-section reveal">
    <div class="act-visuel act-pasteque" data-emoji="🍉">
      <video class="act-video" muted loop playsinline preload="none">
        <source src="parkour.mp4" type="video/mp4">
      </video>
      <div class="act-emoji">🍉</div>
      <div class="act-prix" style="color:#6b0035;">28€</div>
      <div class="act-duree" style="color:#6b0035;">⏱ 1 heure</div>
    </div>
    <div class="act-texte">
      <div class="act-tag tag-pasteque">Parcours · Solo / Duo / Trio</div>
      <div class="act-nom">Parcours Pastèque</div>
      <p class="act-desc">Une série d'épreuves nautiques sur construction gonflable. Parfait pour se rafraîchir entre amis ou en famille !</p>
      <a href="#reserver" class="btn-act btn-pasteque">Réserver <span class="arr">→</span></a>
    </div>
  </div>

  <!-- KAYAK KIWI -->
  <div class="activite-section reveal">
    <div class="act-visuel act-kiwi" data-emoji="🥝">
      <video class="act-video" muted loop playsinline preload="none">
        <source src="kayak.mp4" type="video/mp4">
      </video>
      <div class="act-emoji">🥝</div>
      <div class="act-prix" style="color:#244d10;">22€</div>
      <div class="act-duree" style="color:#244d10;">⏱ 1 heure</div>
    </div>
    <div class="act-texte">
      <div class="act-tag tag-kiwi">Kayak · Solo / Duo</div>
      <div class="act-nom">Kayak Kiwi</div>
      <p class="act-desc">Kayaks vert acidulé ultra-légers. Idéal pour longer les mangroves et observer la faune côtière. Briefing sécurité inclus.</p>
      <a href="#reserver" class="btn-act btn-kiwi">Réserver <span class="arr">→</span></a>
    </div>
  </div>

  <!-- PLONGÉE PASSION -->
  <div class="activite-section reveal">
    <div class="act-visuel act-passion" data-emoji="🍈">
      <video class="act-video" muted loop playsinline preload="none">
        <source src="plongee.mp4" type="video/mp4">
      </video>
      <div class="act-emoji">🍈</div>
      <div class="act-prix" style="color:#3a0050;">45€</div>
      <div class="act-duree" style="color:#3a0050;">⏱ Session guidée</div>
    </div>
    <div class="act-texte">
      <div class="act-tag tag-passion">Snorkeling · Guidé</div>
      <div class="act-nom">Plongée Fruit de la Passion</div>
      <p class="act-desc">Masques et tubas aux reflets jaune doré et violet intense. Exploration des fonds coralliens avec un guide naturaliste certifié.</p>
      <a href="#reserver" class="btn-act btn-passion">Réserver <span class="arr">→</span></a>
    </div>
  </div>

  <!-- JET-SKI PÊCHE -->
  <div class="activite-section reveal">
    <div class="act-visuel act-peche" data-emoji="🍑">
      <video class="act-video" muted loop playsinline preload="none">
        <source src="jetski.mp4" type="video/mp4">
      </video>
      <div class="act-emoji">🍑</div>
      <div class="act-prix" style="color:#7a1a00;">80€</div>
      <div class="act-duree" style="color:#7a1a00;">⏱ 30 minutes</div>
    </div>
    <div class="act-texte">
      <div class="act-tag tag-peche">Jet-ski · Adrénaline</div>
      <div class="act-nom">Jet-ski Pêche</div>
      <p class="act-desc">Engins pêche et abricot pour des sensations intenses. Baptême ou location libre avec briefing sécurité obligatoire. À partir de 16 ans.</p>
      <a href="#reserver" class="btn-act btn-peche">Réserver <span class="arr">→</span></a>
    </div>
  </div>

  <!-- VOILE COCO -->
  <div class="activite-section reveal">
    <div class="act-visuel act-coco" data-emoji="🥥">
      <video class="act-video" muted loop playsinline preload="none">
        <source src="voile.mp4" type="video/mp4">
      </video>
      <div class="act-emoji">🥥</div>
      <div class="act-prix" style="color:#3d2800;">60€</div>
      <div class="act-duree" style="color:#3d2800;">⏱ Demi-journée</div>
    </div>
    <div class="act-texte">
      <div class="act-tag tag-coco">Voile · Initiation</div>
      <div class="act-nom">Voile Noix de Coco</div>
      <p class="act-desc">Catamarans aux voiles ivoire et brun naturel. Initiation à la voile en eaux calmes, sensations zen garanties. Instructeur à bord.</p>
      <a href="#reserver" class="btn-act btn-coco">Réserver <span class="arr">→</span></a>
    </div>
  </div>

  <!-- BOUÉE ANANAS -->
  <div class="activite-section reveal">
    <div class="act-visuel act-ananas" data-emoji="🍍">
      <video class="act-video" muted loop playsinline preload="none">
        <source src="boue.mp4" type="video/mp4">
      </video>
      <div class="act-emoji">🍍</div>
      <div class="act-prix" style="color:#4a2800;">40€</div>
      <div class="act-duree" style="color:#4a2800;">⏱ 30 minutes</div>
    </div>
    <div class="act-texte">
      <div class="act-tag tag-ananas">Bouée tractée · Sensations</div>
      <div class="act-nom">Bouée Tractée Ananas</div>
      <p class="act-desc">Accrochez-vous à notre bouée aux couleurs ananas et laissez-vous tracter à toute vitesse ! Sensations fortes garanties. Casque et gilet fournis.</p>
      <a href="#reserver" class="btn-act btn-ananas">Réserver <span class="arr">→</span></a>
    </div>
  </div>

  <!-- ══════════════════════════════════════
       SECTION COCKTAILS — centrée
  ══════════════════════════════════════ -->
  <section id="cocktails">

    <div class="label-sup">Nos formules</div>
    <h2>LES COCK<span>TAILS</span></h2>
    <p class="section-desc">Composez votre expérience comme un vrai cocktail tropical : choisissez une formule, laissez-vous porter par les saveurs de la mer.</p>

    <div class="cocktails-grid">

      <!-- Cocktail 1 — Piña Colada -->
      <div class="cocktail-card reveal" data-glass="🍹">
        <div class="cocktail-prix">88€</div>
        <div class="cocktail-header">
          <div class="cocktail-glass">🍹</div>
          <div>
            <div class="cocktail-name">Piña Colada</div>
            <div class="cocktail-sous-titre">Douceur tropicale</div>
          </div>
        </div>
        <ul class="cocktail-ingredients">
          <li><span class="ing-emoji">🥥</span> Voile Noix de Coco — demi-journée</li>
          <li><span class="ing-emoji">🍍</span> Bouée Tractée Ananas — 30 min</li>
        </ul>
        <p class="cocktail-note">La douceur du vent dans les voiles, suivie de l'adrénaline de la bouée. Le combo zen + frisson parfait.</p>
        <a href="#reserver" class="cocktail-btn">Réserver ce cocktail →</a>
      </div>

      <!-- Cocktail 2 — Bora Bora -->
      <div class="cocktail-card reveal" data-glass="🌊">
        <div class="cocktail-prix">75€</div>
        <div class="cocktail-header">
          <div class="cocktail-glass">🌊</div>
          <div>
            <div class="cocktail-name">Bora Bora</div>
            <div class="cocktail-sous-titre">Évasion pure <span class="badge-pop">Populaire</span></div>
          </div>
        </div>
        <ul class="cocktail-ingredients">
          <li><span class="ing-emoji">🍍</span> Bouée Tractée Ananas — 30 min</li>
          <li><span class="ing-emoji">🥭</span> Surf Mangue — demi-journée</li>
        </ul>
        <p class="cocktail-note">Commencez fort avec la bouée, puis apprivoisez les vagues en surf. Une journée digne des plages de Polynésie.</p>
        <a href="#reserver" class="cocktail-btn">Réserver ce cocktail →</a>
      </div>

      <!-- Cocktail 3 — Mojito Passion -->
      <div class="cocktail-card reveal" data-glass="🫙">
        <div class="cocktail-prix">65€</div>
        <div class="cocktail-header">
          <div class="cocktail-glass">🫙</div>
          <div>
            <div class="cocktail-name">Mojito Passion</div>
            <div class="cocktail-sous-titre">Culture & nature</div>
          </div>
        </div>
        <ul class="cocktail-ingredients">
          <li><span class="ing-emoji">🍈</span> Plongée Fruit de la Passion — session guidée</li>
          <li><span class="ing-emoji">🍸</span> Mojito offert au bar du port</li>
        </ul>
        <p class="cocktail-note">Explorez les fonds marins avec un guide, puis ressourcez-vous avec un vrai mojito frais. L'expérience complète du navigateur.</p>
        <a href="#reserver" class="cocktail-btn">Réserver ce cocktail →</a>
      </div>

      <!-- Cocktail 4 — Péché Mignon -->
      <div class="cocktail-card reveal" data-glass="🍑">
        <div class="cocktail-prix">80€ / pers.</div>
        <div class="cocktail-header">
          <div class="cocktail-glass">🍑</div>
          <div>
            <div class="cocktail-name">Péché Mignon</div>
            <div class="cocktail-sous-titre">Adrénaline en groupe</div>
          </div>
        </div>
        <ul class="cocktail-ingredients">
          <li><span class="ing-emoji">🍑</span> Jet-ski Pêche — 30 min chacun</li>
          <li><span class="ing-emoji">👥</span> Minimum 3 personnes requis</li>
        </ul>
        <p class="cocktail-note">La formule jet-ski en groupe : plus vous êtes nombreux, plus l'ambiance est électrique. Tarif individuel, sensations collectives.</p>
        <a href="#reserver" class="cocktail-btn">Réserver ce cocktail →</a>
      </div>

      <!-- Cocktail 5 — Tiki Sunrise -->
      <div class="cocktail-card reveal" data-glass="🌅">
        <div class="cocktail-prix">57€</div>
        <div class="cocktail-header">
          <div class="cocktail-glass">🌅</div>
          <div>
            <div class="cocktail-name">Tiki Sunrise</div>
            <div class="cocktail-sous-titre">Douceur du matin</div>
          </div>
        </div>
        <ul class="cocktail-ingredients">
          <li><span class="ing-emoji">🥝</span> Kayak Kiwi — 1 heure</li>
          <li><span class="ing-emoji">🍉</span> Parcours Pastèque — 1 heure</li>
        </ul>
        <p class="cocktail-note">Commencez par glisser silencieusement en kayak, puis enchaînez sur le parcours gonflable pour finir en rires et éclaboussures.</p>
        <a href="#reserver" class="cocktail-btn">Réserver ce cocktail →</a>
      </div>

      <!-- Cocktail 6 — Blue Lagoon -->
      <div class="cocktail-card reveal" data-glass="💙">
        <div class="cocktail-prix">102€</div>
        <div class="cocktail-header">
          <div class="cocktail-glass">💙</div>
          <div>
            <div class="cocktail-name">Blue Lagoon</div>
            <div class="cocktail-sous-titre">Le grand frisson</div>
          </div>
        </div>
        <ul class="cocktail-ingredients">
          <li><span class="ing-emoji">🍑</span> Jet-ski Pêche — 30 min</li>
          <li><span class="ing-emoji">🍈</span> Plongée Fruit de la Passion — session guidée</li>
          <li><span class="ing-emoji">🥭</span> Surf Mangue — demi-journée</li>
        </ul>
        <p class="cocktail-note">La journée ultime pour les aventuriers : vitesse, profondeurs, et maîtrise des vagues. Trois activités, une seule journée inoubliable.</p>
        <a href="#reserver" class="cocktail-btn">Réserver ce cocktail →</a>
      </div>

    </div>
  </section>


  <!-- ══════════════════════════════════════
       SECTION DESTINATIONS
  ══════════════════════════════════════ -->
  <section id="destinations">

    <div class="dest-intro reveal">
      <div class="label-sup">Nos destinations</div>
      <h2>NOS <span>ÎLES</span></h2>
      <p class="dest-desc">COCKTAIL'S est implanté sur les plus belles îles tropicales du globe. Chaque base de loisirs offre un cadre unique, des eaux cristallines et une équipe locale passionnée.</p>
    </div>

    <div class="dest-wrap reveal">

      <!-- Carte du monde SVG -->
      <div class="dest-map">
        <svg viewBox="0 0 900 450" xmlns="http://www.w3.org/2000/svg" class="world-svg">

          <!-- Fond océan -->
          <rect width="900" height="450" fill="#0a3d45" rx="16"/>

          <!-- Grille de latitude / longitude -->
          <g stroke="rgba(23,184,200,0.08)" stroke-width="0.8">
            <line x1="0" y1="112" x2="900" y2="112"/>
            <line x1="0" y1="225" x2="900" y2="225"/>
            <line x1="0" y1="337" x2="900" y2="337"/>
            <line x1="150" y1="0" x2="150" y2="450"/>
            <line x1="300" y1="0" x2="300" y2="450"/>
            <line x1="450" y1="0" x2="450" y2="450"/>
            <line x1="600" y1="0" x2="600" y2="450"/>
            <line x1="750" y1="0" x2="750" y2="450"/>
          </g>

          <!-- ── Continents simplifiés ── -->

          <!-- Amérique du Nord -->
          <path d="M80,60 L180,55 L195,80 L185,110 L170,130 L155,160 L140,175 L125,165 L110,150 L90,140 L75,120 L65,95 Z"
                fill="#0d6e7a" opacity="0.7"/>
          <!-- Amérique Centrale -->
          <path d="M140,175 L165,185 L160,205 L145,210 L135,195 Z"
                fill="#0d6e7a" opacity="0.7"/>
          <!-- Amérique du Sud -->
          <path d="M145,210 L185,205 L205,230 L210,265 L200,300 L185,330 L165,340 L148,320 L135,290 L130,255 L135,230 Z"
                fill="#0d6e7a" opacity="0.7"/>

          <!-- Europe -->
          <path d="M390,55 L440,50 L455,65 L450,85 L430,95 L415,90 L400,80 L388,70 Z"
                fill="#0d6e7a" opacity="0.7"/>
          <!-- Afrique -->
          <path d="M400,100 L455,95 L475,120 L480,160 L470,210 L450,255 L425,270 L400,255 L382,220 L375,175 L380,135 Z"
                fill="#0d6e7a" opacity="0.7"/>

          <!-- Asie -->
          <path d="M460,45 L620,40 L680,55 L700,80 L695,110 L670,130 L640,140 L600,145 L560,140 L520,130 L490,115 L465,95 L455,70 Z"
                fill="#0d6e7a" opacity="0.7"/>
          <!-- Asie du sud-est -->
          <path d="M640,140 L680,150 L695,175 L685,200 L665,210 L645,200 L630,180 L628,158 Z"
                fill="#0d6e7a" opacity="0.7"/>

          <!-- Australie -->
          <path d="M680,240 L750,235 L775,255 L770,295 L745,315 L710,310 L690,290 L678,265 Z"
                fill="#0d6e7a" opacity="0.7"/>

          <!-- ── Points de destination ── -->

          <!-- Martinique  lon=-61, lat=14.6  → x≈222, y≈255 -->
          <g class="dest-pin" transform="translate(222,255)">
            <circle r="10" fill="#ff8c00" opacity="0.25" class="ping"/>
            <circle r="5"  fill="#ff8c00"/>
            <line x1="0" y1="-5" x2="0" y2="-28" stroke="#ff8c00" stroke-width="1.5" stroke-dasharray="3,2"/>
          </g>

          <!-- Guadeloupe  lon=-61.5, lat=16.2 → x≈220, y≈248 -->
          <g class="dest-pin" transform="translate(215,243)">
            <circle r="10" fill="#17b8c8" opacity="0.25" class="ping"/>
            <circle r="5"  fill="#17b8c8"/>
            <line x1="0" y1="-5" x2="0" y2="-28" stroke="#17b8c8" stroke-width="1.5" stroke-dasharray="3,2"/>
          </g>

          <!-- Philippines  lon=122, lat=12  → x≈656, y≈260 -->
          <g class="dest-pin" transform="translate(660,258)">
            <circle r="10" fill="#ff8c00" opacity="0.25" class="ping"/>
            <circle r="5"  fill="#ff8c00"/>
            <line x1="0" y1="-5" x2="0" y2="-28" stroke="#ff8c00" stroke-width="1.5" stroke-dasharray="3,2"/>
          </g>

          <!-- Réunion  lon=55.5, lat=-21  → x≈482, y≈318 -->
          <g class="dest-pin" transform="translate(482,318)">
            <circle r="10" fill="#5db82e" opacity="0.25" class="ping"/>
            <circle r="5"  fill="#5db82e"/>
            <line x1="0" y1="-5" x2="0" y2="-28" stroke="#5db82e" stroke-width="1.5" stroke-dasharray="3,2"/>
          </g>

          <!-- Polynésie  lon=-149, lat=-17  → x≈96, y≈303 -->
          <g class="dest-pin" transform="translate(96,303)">
            <circle r="10" fill="#e0257a" opacity="0.25" class="ping"/>
            <circle r="5"  fill="#e0257a"/>
            <line x1="0" y1="-5" x2="0" y2="-28" stroke="#e0257a" stroke-width="1.5" stroke-dasharray="3,2"/>
          </g>

          <!-- Maldives  lon=73, lat=3  → x≈538, y≈272 -->
          <g class="dest-pin" transform="translate(538,272)">
            <circle r="10" fill="#17b8c8" opacity="0.25" class="ping"/>
            <circle r="5"  fill="#17b8c8"/>
            <line x1="0" y1="-5" x2="0" y2="-28" stroke="#17b8c8" stroke-width="1.5" stroke-dasharray="3,2"/>
          </g>

          <!-- Labels discrets -->
          <text x="222" y="222" text-anchor="middle" font-size="9" fill="rgba(255,255,255,0.6)" font-family="sans-serif">Martinique</text>
          <text x="208" y="210" text-anchor="middle" font-size="9" fill="rgba(255,255,255,0.6)" font-family="sans-serif">Guadeloupe</text>
          <text x="660" y="225" text-anchor="middle" font-size="9" fill="rgba(255,255,255,0.6)" font-family="sans-serif">Philippines</text>
          <text x="482" y="285" text-anchor="middle" font-size="9" fill="rgba(255,255,255,0.6)" font-family="sans-serif">La Réunion</text>
          <text x="96"  y="270" text-anchor="middle" font-size="9" fill="rgba(255,255,255,0.6)" font-family="sans-serif">Polynésie</text>
          <text x="538" y="239" text-anchor="middle" font-size="9" fill="rgba(255,255,255,0.6)" font-family="sans-serif">Maldives</text>

          <!-- Légende -->
          <text x="16" y="435" font-size="10" fill="rgba(200,247,251,0.35)" font-family="sans-serif">© COCKTAIL'S — Bases de loisirs</text>
        </svg>

        <!-- Animation CSS des pings -->
        <style>
          .ping {
            transform-origin: center;
            animation: pingAnim 2s ease-out infinite;
          }
          @keyframes pingAnim {
            0%   { transform: scale(1);   opacity: 0.25; }
            70%  { transform: scale(2.2); opacity: 0; }
            100% { transform: scale(1);   opacity: 0; }
          }
          .dest-pin:nth-child(2) .ping { animation-delay: 0.4s; }
          .dest-pin:nth-child(3) .ping { animation-delay: 0.8s; }
          .dest-pin:nth-child(4) .ping { animation-delay: 1.2s; }
          .dest-pin:nth-child(5) .ping { animation-delay: 1.6s; }
          .dest-pin:nth-child(6) .ping { animation-delay: 2.0s; }
        </style>
      </div>

      <!-- Fiches destinations -->
      <div class="dest-list">

        <div class="dest-card">
          <div class="dest-flag">🇲🇶</div>
          <div class="dest-info">
            <div class="dest-nom">Martinique</div>
            <div class="dest-loc">📍 Le Marin, Antilles françaises</div>
            <p class="dest-txt">Baies turquoise, forêts tropicales et couchers de soleil de légende. Notre base du Marin accueille surf, kayak et plongée dans des eaux à 28°C.</p>
          </div>
        </div>

        <div class="dest-card">
          <div class="dest-flag">🇬🇵</div>
          <div class="dest-info">
            <div class="dest-nom">Guadeloupe</div>
            <div class="dest-loc">📍 Sainte-Anne, Grande-Terre</div>
            <p class="dest-txt">Entre mer des Caraïbes et Atlantique, la Guadeloupe offre des conditions de surf et de voile exceptionnelles toute l'année.</p>
          </div>
        </div>

        <div class="dest-card">
          <div class="dest-flag">🇵🇭</div>
          <div class="dest-info">
            <div class="dest-nom">Philippines</div>
            <div class="dest-loc">📍 El Nido, Palawan</div>
            <p class="dest-txt">Lagons secrets, falaises calcaires et récifs coralliens parmi les plus riches du monde. Le paradis absolu pour la plongée et le kayak.</p>
          </div>
        </div>

        <div class="dest-card">
          <div class="dest-flag">🇷🇪</div>
          <div class="dest-info">
            <div class="dest-nom">La Réunion</div>
            <div class="dest-loc">📍 Saint-Leu, côte ouest</div>
            <p class="dest-txt">Spot de surf mondialement reconnu, eaux protégées par un lagon et paysages volcaniques à couper le souffle pour un cadre unique.</p>
          </div>
        </div>

        <div class="dest-card">
          <div class="dest-flag">🇵🇫</div>
          <div class="dest-info">
            <div class="dest-nom">Polynésie française</div>
            <div class="dest-loc">📍 Moorea, îles de la Société</div>
            <p class="dest-txt">Eaux limpides, tombants coralliens et ambiance bora-bora garantie. Notre base polynésienne est le terrain de jeu idéal pour toutes nos formules cocktails.</p>
          </div>
        </div>

        <div class="dest-card">
          <div class="dest-flag">🇲🇻</div>
          <div class="dest-info">
            <div class="dest-nom">Maldives</div>
            <div class="dest-loc">📍 Atoll de Malé Nord</div>
            <p class="dest-txt">L'ultime destination tropicale : sable blanc, eaux cristallines et faune marine exceptionnelle. Nos formules Blue Lagoon et Mojito Passion y prennent tout leur sens.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Style destinations -->
  <style>
    #destinations {
      background: var(--sand);
      padding: 6rem 5vw;
    }
    .dest-intro {
      text-align: center;
      margin-bottom: 3rem;
    }
    .dest-intro h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(2.2rem, 5vw, 4rem);
      letter-spacing: 0.05em;
      color: var(--ocean);
      line-height: 1;
      margin-bottom: 0.8rem;
    }
    .dest-intro h2 span { color: var(--mango); }
    .dest-desc {
      font-size: 1rem; font-weight: 300;
      color: var(--mid); max-width: 560px;
      margin: 0 auto; line-height: 1.7;
    }
    .dest-wrap {
      display: grid;
      grid-template-columns: 1fr 420px;
      gap: 3rem;
      align-items: start;
    }
    /* Carte SVG */
    .dest-map { position: sticky; top: 80px; }
    .world-svg {
      width: 100%; border-radius: 1.2rem;
      box-shadow: 0 12px 40px rgba(10,61,69,0.2);
    }
    /* Liste des destinations */
    .dest-list {
      display: flex; flex-direction: column; gap: 1rem;
    }
    .dest-card {
      display: flex; align-items: flex-start; gap: 1rem;
      background: white;
      border-radius: 1rem;
      padding: 1.2rem 1.4rem;
      box-shadow: 0 3px 16px rgba(10,61,69,0.07);
      border-left: 4px solid var(--ocean-light);
      transition: transform 0.25s, box-shadow 0.25s;
    }
    .dest-card:hover {
      transform: translateX(6px);
      box-shadow: 0 6px 24px rgba(10,61,69,0.12);
    }
    .dest-flag { font-size: 2.2rem; flex-shrink: 0; line-height: 1; }
    .dest-nom {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.2rem; letter-spacing: 0.05em;
      color: var(--ocean); margin-bottom: 0.2rem;
    }
    .dest-loc {
      font-size: 0.75rem; color: var(--mango);
      font-weight: 500; letter-spacing: 0.06em;
      margin-bottom: 0.4rem;
    }
    .dest-txt {
      font-size: 0.85rem; color: var(--mid);
      line-height: 1.55; font-weight: 300;
    }
    @media (max-width: 900px) {
      .dest-wrap { grid-template-columns: 1fr; }
      .dest-map { position: static; }
    }
  </style>

  <!-- ══════════════════════════════════════
       FORMULAIRE RÉSERVATION
  ══════════════════════════════════════ -->
  <section id="reserver">
    <div class="label-sup">Planifiez votre sortie</div>
    <h2>RÉSERVER UNE <span>ACTIVITÉ</span></h2>
    <p class="sous-titre">Confirmation sous 2h — annulation gratuite 48h avant</p>

    <?php if ($erreur): ?>
    <div class="resa-confirm" style="border-color:rgba(255,80,60,0.5);background:rgba(255,80,60,0.1);">
      <div style="font-size:2.5rem;margin-bottom:1rem;">⚠️</div>
      <p style="font-size:1rem;font-weight:500;color:white;"><?= $erreur ?></p>
    </div>
    <?php endif; ?>

    <?php if ($message): ?>
    <div class="resa-confirm">
      <div style="font-size:3rem;margin-bottom:1rem;">🌊</div>
      <p style="font-size:1.2rem;font-weight:500;margin-bottom:0.5rem;">
        Merci <strong><?= htmlspecialchars($message) ?></strong> !
      </p>
      <p style="font-size:0.95rem;color:rgba(200,247,251,0.8);">
        Votre demande a bien été prise en compte. On vous répond sous 2h !
      </p>
    </div>
    <?php endif; ?>

    <form class="resa-form reveal" method="POST" action="index.php#reserver" id="resaForm">

      <div class="field">
        <label>Prénom</label>
        <input type="text" name="prenom" placeholder="Marie" required />
      </div>
      <div class="field">
        <label>Nom</label>
        <input type="text" name="nom" placeholder="Dupont" required />
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" placeholder="marie@exemple.fr" required />
      </div>
      <div class="field">
        <label>Téléphone</label>
        <input type="text" name="telephone" placeholder="06 12 34 56 78" />
      </div>

      <!-- Choix du type de réservation -->
      <div class="field full">
        <label>Type de réservation</label>
        <select name="type_resa" id="type_resa" onchange="toggleChoix()" required>
          <option value="activite">🌊 Activité seule</option>
          <option value="cocktail">🍹 Formule Cocktail</option>
        </select>
      </div>

      <!-- Sélecteur activité (affiché par défaut) -->
      <div class="field full" id="bloc_activite">
        <label>Activité</label>
        <select name="activite_id" id="activite_id">
          <option value="">— Choisir une activité —</option>
          <?php
          $res_act2 = mysqli_query($conn, "SELECT id_activite, fruit, nom, prix FROM activites ORDER BY id_activite ASC");
          while ($act = mysqli_fetch_assoc($res_act2)) {
              echo "<option value='" . $act["id_activite"] . "'>"
                 . $act["fruit"] . " " . $act["nom"]
                 . " — " . $act["prix"] . "€</option>";
          }
          ?>
        </select>
      </div>

      <!-- Sélecteur cocktail (masqué par défaut) -->
      <div class="field full" id="bloc_cocktail" style="display:none;">
        <label>Formule Cocktail</label>
        <select name="cocktail_id" id="cocktail_id" onchange="verifierPersonnes()">
          <option value="">— Choisir une formule —</option>
          <?php
          $res_ck2 = mysqli_query($conn, "SELECT id_cocktail, emoji, nom, prix FROM cocktails ORDER BY id_cocktail ASC");
          while ($ck2 = mysqli_fetch_assoc($res_ck2)) {
              echo "<option value='" . $ck2["id_cocktail"] . "'>"
                 . $ck2["emoji"] . " " . $ck2["nom"]
                 . " — " . $ck2["prix"] . "€</option>";
          }
          ?>
        </select>
      </div>

      <div class="field">
        <label>Date souhaitée</label>
        <input type="date" name="date_resa" required min="<?= date('Y-m-d') ?>" />
      </div>
      <div class="field">
        <label>Nombre de personnes</label>
        <input type="number" name="nb_personnes" id="nb_personnes" value="1" min="1" max="6" required oninput="verifierPersonnes()" />
        <small id="nb_hint" style="font-size:0.78rem;color:rgba(200,247,251,0.6);margin-top:0.3rem;">Maximum 6 personnes pour une activité seule.</small>
      </div>

      <button type="submit" name="reserver" class="resa-submit">
        Confirmer la réservation 🌊
      </button>
    </form>
  </section>

  <footer>
    <div class="footer-logo">COCK<span>TAIL'S</span></div>
    <div class="footer-links">
      <a href="#activites">Activités</a>
      <a href="#cocktails">Cocktails</a>
      <a href="#destinations">Destinations</a>
      <a href="#reserver">Réserver</a>
      <a href="#">Mentions légales</a>
    </div>
    <p>© 2025 COCKTAIL'S — SAE203 MMI</p>
    <a href="admin/login.php" title="Administration"
       style="font-size:1.8rem; color:rgba(200,247,251,0.55); text-decoration:none; margin-top:0.8rem; display:inline-block; transition:transform 0.4s, color 0.3s;"
       onmouseover="this.style.color='#ff8c00'; this.style.transform='rotate(90deg)';"
       onmouseout="this.style.color='rgba(200,247,251,0.55)'; this.style.transform='rotate(0deg)';">⚙️</a>
  </footer>

  <script>
    // Affiche/masque les sélecteurs + adapte le max de personnes selon le type
    function toggleChoix() {
      const type  = document.getElementById("type_resa").value;
      const nb    = document.getElementById("nb_personnes");
      const hint  = document.getElementById("nb_hint");

      document.getElementById("bloc_activite").style.display = type === "activite" ? "" : "none";
      document.getElementById("bloc_cocktail").style.display = type === "cocktail" ? "" : "none";

      if (type === "activite") {
        // Activité seule : max 6 personnes
        nb.max = 6;
        if (parseInt(nb.value) > 6) nb.value = 6;
        hint.textContent = "Maximum 6 personnes pour une activité seule.";
        hint.style.color = "rgba(200,247,251,0.6)";
        document.getElementById("cocktail_id").value = "";
      } else {
        // Cocktail : max 20, mais on vérifie Péché Mignon
        nb.max = 20;
        hint.textContent = "Pas de limite pour les formules cocktails. Péché Mignon : minimum 3 personnes.";
        hint.style.color = "rgba(200,247,251,0.6)";
        document.getElementById("activite_id").value = "";
      }
    }

    // Vérification en temps réel quand on change le nombre ou le cocktail
    function verifierPersonnes() {
      const type       = document.getElementById("type_resa").value;
      const nb         = parseInt(document.getElementById("nb_personnes").value);
      const hint       = document.getElementById("nb_hint");
      const cocktailSel = document.getElementById("cocktail_id");
      const nomCocktail = cocktailSel.options[cocktailSel.selectedIndex]?.text || "";

      if (type === "activite" && nb > 6) {
        hint.textContent = "⚠️ Maximum 6 personnes pour une activité seule !";
        hint.style.color = "#ff8c70";
      } else if (type === "cocktail" && nomCocktail.includes("Péché Mignon") && nb < 3) {
        hint.textContent = "⚠️ Le Péché Mignon requiert au moins 3 personnes !";
        hint.style.color = "#ff8c70";
      } else if (type === "activite") {
        hint.textContent = "Maximum 6 personnes pour une activité seule.";
        hint.style.color = "rgba(200,247,251,0.6)";
      } else {
        hint.textContent = "Pas de limite pour les formules cocktails. Péché Mignon : minimum 3 personnes.";
        hint.style.color = "rgba(200,247,251,0.6)";
      }
    }

    // Fruits flottants hero
    const fruits = ['🥭','🍍','🥥','🍉','🍑','🥝','🍋','🍈','🍊'];
    const heroFruits = document.getElementById('heroFruits');
    for (let i = 0; i < 16; i++) {
      const el = document.createElement('div');
      el.className = 'fruit-float';
      el.textContent = fruits[Math.floor(Math.random() * fruits.length)];
      el.style.left              = Math.random() * 100 + '%';
      el.style.fontSize          = (24 + Math.random() * 28) + 'px';
      el.style.animationDuration = (14 + Math.random() * 16) + 's';
      el.style.animationDelay    = (Math.random() * 18) + 's';
      heroFruits.appendChild(el);
    }

    // Navbar scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.style.background = window.scrollY > 80 ? 'rgba(6,74,98,0.97)' : 'rgba(6,74,98,0.85)';
    });

    // Scroll reveal
    const observer = new IntersectionObserver(
      entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
      { threshold: 0.1 }
    );
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Vidéos hover
    document.querySelectorAll('.act-visuel').forEach(bloc => {
      const video = bloc.querySelector('.act-video');
      if (!video) return;
      bloc.addEventListener('mouseenter', () => video.play().catch(() => {}));
      bloc.addEventListener('mouseleave', () => { video.pause(); video.currentTime = 0; });
    });
  </script>
</body>
</html>