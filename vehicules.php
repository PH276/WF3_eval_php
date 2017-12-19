<?php
include ('inc/init.inc.php');
if (isset($_GET['id'])){
    $requete = "SELECT a.id_vehicule, marque, modele, couleur, immatriculation, prenom, nom
    FROM association_vehicule_conducteur as a
    INNER JOIN conducteur as c ON a.id_conducteur=c.id_conducteur
    INNER JOIN vehicule as v ON a.id_vehicule=v.id_vehicule
    WHERE a.id_conducteur = :id";
$req = $pdo -> prepare($requete);
$req -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$req -> execute();
} elseif (isset($_GET['sans-conducteur'])){
    $requete = "SELECT * FROM vehicule WHERE id_vehicule NOT IN (SELECT id_vehicule FROM association_vehicule_conducteur)";
    $req = $pdo -> query ($requete);
} else{
    $requete = "SELECT * FROM vehicule";
    $req = $pdo -> query ($requete);

}
$vehicules = $req -> fetchAll(PDO::FETCH_ASSOC);
$nb = $req -> rowCount();


// initilistion de message d'erreur
$erreurmarque = '';
$erreurmodele = '';
$erreurcouleur = '';
$erreurimmatriculation = '';


// traitement d'une demande de suppresion ou de modification
if (isset($_GET['action']) && isset($_GET['id'])) {

    // cas d'une suppresion
    if ($_GET['action'] == "suppr"){
        $req = $pdo -> prepare ("DELETE FROM `vehicule` WHERE id_vehicule = :id");
        $req -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $req -> execute();
        header('location: vehicules.php');
    }

    // cas de la modification d'un conducteur
    if ($_GET['action'] == "modif"){
        $req = $pdo -> prepare ("SELECT * FROM vehicule WHERE id_vehicule = :id");
        $req -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $req -> execute();
        $vehicule = $req -> fetch(PDO::FETCH_ASSOC);
        extract($vehicule);
    }
}
// traitement du formulaire
if (!empty($_POST)) {
    // on éclate le $_POST en tableau qui permet d'accéder directement aux champs par des variables
    extract($_POST);

    // cas d'un ajout
    if (empty($id_vehicule)){
        if (!empty($marque) && !empty($modele) && !empty($couleur) && !empty($immatriculation)){
            $req = $pdo -> prepare ("INSERT INTO vehicule(marque, modele, couleur, immatriculation) VALUES (:marque, :modele, :couleur, :immatriculation)");
            $req -> bindParam(':marque', $marque, PDO::PARAM_STR);
            $req -> bindParam(':modele', $modele, PDO::PARAM_STR);
            $req -> bindParam(':couleur', $couleur, PDO::PARAM_STR);
            $req -> bindParam(':immatriculation', $immatriculation, PDO::PARAM_STR);
            $req -> execute();
            header('location: vehicules.php');

        } else {
            $erreurmarque = (empty($erreurmarque)) ? 'Indiquez une marque' : null;
            $erreurmodele = (empty($erreurmodele)) ? 'Indiquez un modèle' : null;
            $erreurcouleur = (empty($erreurcouleur)) ? 'Indiquez une couleur' : null;
            $erreurimmatriculation = (empty($erreurimmatriculation)) ? 'Indiquez une immatriculation' : null;
        }
    }
    // cas d'une modification
    else {
        if (!empty($marque) && !empty($modele) && !empty($couleur) && !empty($immatriculation)){

            $req = $pdo -> prepare ("UPDATE vehicule SET marque = :marque, modele = :modele, couleur = :couleur, immatriculation = :immatriculation WHERE id_vehicule = :id");
            $req -> bindParam(':id', $id_vehicule, PDO::PARAM_INT);
            $req -> bindParam(':marque', $marque, PDO::PARAM_STR);
            $req -> bindParam(':modele', $modele, PDO::PARAM_STR);
            $req -> bindParam(':couleur', $couleur, PDO::PARAM_STR);
            $req -> bindParam(':immatriculation', $immatriculation, PDO::PARAM_STR);
            $req -> execute();
            header('location: vehicules.php');

        } else {
            $erreurmarque = (empty($erreurmarque)) ? 'Indiquez une marque' : null;
            $erreurmodele = (empty($erreurmodele)) ? 'Indiquez un modèle' : null;
            $erreurcouleur = (empty($erreurcouleur)) ? 'Indiquez une couleur' : null;
            $erreurimmatriculation = (empty($erreurimmatriculation)) ? 'Indiquez une immatriculation' : null;
        }
    }
}

include ('inc/head.inc.php');
?>
<main class="container">
    <a href="?tous=1">
        <button  class="btn btn-info">Tous</button>
    </a>
    <a href="?sans-conducteur=1">
        <button  class="btn btn-info">sans conducteur</button>
    </a>


    <h1 class="text-center">Liste des <?= $nb ?> véhicules <?= (isset($_GET['sans-conducteur']))?
    " sans conducteur":((isset($_GET['id']))?" conduits par " . $vehicules[0]['prenom'] . ' ' . $vehicules[0]['nom']:""); ?></h1>

    <!-- Affichage de la Liste des conducteurs -->
    <table class="table table-striped">
        <tr>
            <th>id_véhicule</th>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Couleur</th>
            <th>Immatriculation</th>
            <th class="text-center">Modification</th>
            <th class="text-center">Suppression</th>
        </tr>

        <?php foreach ($vehicules as $vehicule) : ?>
            <tr>
                <td><?= $vehicule['id_vehicule'] ?></td>
                <td><?= $vehicule['marque'] ?></td>
                <td><?= $vehicule['modele'] ?></td>
                <td><?= $vehicule['couleur'] ?></td>
                <td><?= $vehicule['immatriculation'] ?></td>

                <td class="text-center">
                    <a href="?action=modif&id=<?= $vehicule['id_vehicule'] ?>">
                        <button type="button" class="btn btn-info">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                        </button>
                    </a>
                </td>
                <td class="text-center">
                    <a href="?action=suppr&id=<?= $vehicule['id_vehicule'] ?>">
                        <button  class="btn btn-danger">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        </button>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>

    <!-- formulaire de saisie pour un ajout  -->
    <form method="post">
        <input type="hidden" name="id_vehicule" value="<?= isset($id_vehicule)?$id_vehicule:0 ?>">

        <div class="form-group">
            <?php if (!empty($erreurmarque)) : ?>
                <p class="alert alert-danger"><?= $erreurmarque ?></p>
            <?php endif; ?>
            <label for="marque">Marque :</label>
            <input type="text" name="marque" class="form-control" id="marque" value="<?= (isset($marque))?$marque:'' ?>">
        </div>

        <div class="form-group">
            <?php if (!empty($erreurmodele)) : ?>
                <p class="alert alert-danger"><?= $erreurmodele ?></p>
            <?php endif; ?>
            <label for="modele">Modèle :</label>
            <input type="text" name="modele" class="form-control" id="prenom" value="<?= (isset($modele))?$modele:'' ?>">
        </div>

        <div class="form-group">
            <?php if (!empty($erreurcouleur)) : ?>
                <p class="alert alert-danger"><?= $erreurcouleur ?></p>
            <?php endif; ?>
            <label for="couleur">Couleur :</label>
            <input type="text" name="couleur" class="form-control" id="couleur" value="<?= (isset($couleur))?$couleur:'' ?>">
        </div>

        <div class="form-group">
            <?php if (!empty($erreurimmatriculation)) : ?>
                <p class="alert alert-danger"><?= $erreurimmatriculation ?></p>
            <?php endif; ?>
            <label for="nom">Immatriculation :</label>
            <input type="text" name="immatriculation" class="form-control" id="immatriculation" value="<?= (isset($immatriculation))?$immatriculation:'' ?>">
        </div>

        <?php $action=(isset($id_vehicule)?"Modifier le":"Ajouter un ") ?>
        <button type="submit" class="btn btn-submit"><?= $action ?>véhicule</button>
    </form>
</main>

<?php
include ('inc/footer.inc.php');
?>
