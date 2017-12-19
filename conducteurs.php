<?php
include ('inc/init.inc.php');

if (isset($_GET['sans-vehicule'])){
    // cas de  la demande de conducteurs sans véhicule
    $requete = "SELECT * FROM conducteur WHERE id_conducteur NOT IN (SELECT id_conducteur FROM association_vehicule_conducteur)";
} else{
    $requete = "SELECT * FROM conducteur";
}

$req = $pdo -> query ($requete);
$conducteurs = $req -> fetchAll(PDO::FETCH_ASSOC);
$nb = $req -> rowCount();

// initilistion de message d'erreur
$erreurprenom = '';
$erreurnom = '';

// traitement d'une demande de suppresion ou de modification
if (isset($_GET['action']) && isset($_GET['id'])) {

    // cas d'une suppresion
    if ($_GET['action'] == "suppr"){
        $req = $pdo -> prepare ("DELETE FROM `conducteur` WHERE id_conducteur = :id");
        $req -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $req -> execute();
        header('location: conducteurs.php');
    }

    // cas de la modification d'un conducteur
    if ($_GET['action'] == "modif"){
        $req = $pdo -> prepare ("SELECT * FROM conducteur WHERE id_conducteur = :id");
        $req -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $req -> execute();
        $conducteur = $req -> fetch(PDO::FETCH_ASSOC);
        extract($conducteur);
    }
}

// traitement du formulaire
if (!empty($_POST)) {
    // on éclate le $_POST en tableau qui permet d'accéder directement aux champs par des variables
    extract($_POST);

    // cas d'un ajout
    if (empty($id_conducteur)){
        if (!empty($prenom) && !empty($nom)){
            $req = $pdo -> prepare ("INSERT INTO conducteur(prenom, nom) VALUES (:prenom, :nom)");
            $req -> bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $req -> bindParam(':nom', $nom, PDO::PARAM_STR);
            $req -> execute();
            header('location: conducteurs.php');

        } else {
            $erreurprenom = (empty($prenom)) ? 'Indiquez un prénom' : null;
            $erreurnom = (empty($nom)) ? 'Indiquez un nom' : null;
        }
    }
    // cas d'une modification
    else {
        if (!empty($prenom) && !empty($nom)){

            $req = $pdo -> prepare ("UPDATE conducteur SET prenom = :prenom, nom = :nom WHERE id_conducteur = :id");
            $req -> bindParam(':id', $id_conducteur, PDO::PARAM_INT);
            $req -> bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $req -> bindParam(':nom', $nom, PDO::PARAM_STR);
            $req -> execute();
            header('location: conducteurs.php');

        } else {
            $erreurprenom = (empty($prenom)) ? 'Indiquez un prénom' : null;
            $erreurnom = (empty($nom)) ? 'Indiquez un nom' : null;
        }
    }

}

include ('inc/head.inc.php');
?>
<main class="container">
    <a href="?sans-vehicule=1">
        <button  class="btn btn-info">sans véhicule</button>
    </a>

    <h1 class="text-center">Liste des <?= $nb ?> conducteurs <?= (isset($_GET['sans-vehicule']))?" sans véhicule":""; ?></h1>

    <!-- Affichage de la Liste des conducteurs -->
    <table class="table table-striped">
        <tr>
            <th>id_conducteur</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th class="text-center">Modification</th>
            <th class="text-center">Suppression</th>
            <th></th>
        </tr>

        <?php foreach ($conducteurs as $conducteur) : ?>
            <tr>
                <td><?= $conducteur['id_conducteur'] ?></td>
                <td><?= $conducteur['prenom'] ?></td>
                <td><?= $conducteur['nom'] ?></td>

                <td class="text-center">
                    <a href="?action=modif&id=<?= $conducteur['id_conducteur'] ?>">
                        <button type="button" class="btn btn-info">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                        </button>
                    </a>
                </td>
                <td class="text-center">
                    <a href="?action=suppr&id=<?= $conducteur['id_conducteur'] ?>">
                        <button  class="btn btn-danger">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        </button>
                    </a>
                </td>
                <td class="text-center">
                    <a href="vehicules.php?id=<?= $conducteur['id_conducteur'] ?>">
                        <button  class="btn btn-info">Ses véhicules</button>
                    </a>

                </td>
            </tr>
        <?php endforeach; ?>

    </table>

    <!-- formulaire de saisie pour un ajout  -->
    <form method="post">
        <input type="hidden" name="id_conducteur" value="<?= isset($id_conducteur)?$id_conducteur:0 ?>">

        <div class="form-group">
            <?php if (!empty($erreurprenom)) : ?>
                <p class="alert alert-danger"><?= $erreurprenom ?></p>
            <?php endif; ?>
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" class="form-control" id="prenom" value="<?= (isset($prenom))?$prenom:'' ?>">
        </div>

        <div class="form-group">
            <?php if (!empty($erreurnom)) : ?>
                <p class="alert alert-danger"><?= $erreurnom ?></p>
            <?php endif; ?>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" class="form-control" id="nom" value="<?= (isset($nom))?$nom:'' ?>">
        </div>

        <?php $action=(isset($id_conducteur)?"Modifier le":"Ajouter un ") ?>
        <button type="submit" class="btn btn-submit"><?= $action ?>conducteur</button>
    </form>
</main>

<?php
include ('inc/footer.inc.php');
?>
