<?php
include ('inc/init.inc.php');
$requete = "SELECT id_association, prenom, nom, a.id_conducteur idc, marque, modele, a.id_vehicule idv
FROM association_vehicule_conducteur as a
INNER JOIN conducteur as c ON c.id_conducteur=a.id_conducteur
INNER JOIN vehicule as v ON v.id_vehicule=a.id_vehicule
";
$req = $pdo -> query ($requete);
$associations = $req -> fetchAll(PDO::FETCH_ASSOC);
$nb = $req -> rowCount();


$req = $pdo -> query ("SELECT * FROM vehicule");
$vehicules = $req -> fetchAll(PDO::FETCH_ASSOC);

$req = $pdo -> query ("SELECT * FROM conducteur");
$conducteurs = $req -> fetchAll(PDO::FETCH_ASSOC);
// initilistion de message d'erreur

// traitement d'une demande de suppresion ou de modification
if (isset($_GET['action']) && isset($_GET['id'])) {

    // cas d'une suppresion
    if ($_GET['action'] == "suppr"){
        $req = $pdo -> prepare ("DELETE FROM association_vehicule_conducteur WHERE id_association = :id");
        $req -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $req -> execute();
        header('location: association_vehicule_conducteur.php');
    }

    // cas de la modification d'un conducteur
    if ($_GET['action'] == "modif"){
        $req = $pdo -> prepare ("SELECT * FROM association_vehicule_conducteur WHERE id_association = :id");
        $req -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $req -> execute();
        $association = $req -> fetch(PDO::FETCH_ASSOC);
        extract($association);
    }
}

// traitement du formulaire
if (!empty($_POST)) {
    // on éclate le $_POST en tableau qui permet d'accéder directement aux champs par des variables
    extract($_POST);

    // cas d'un ajout
    if (empty($id_association)){
        $req = $pdo -> prepare ("INSERT INTO association_vehicule_conducteur(id_vehicule, id_conducteur) VALUES (:idv, :idc)");
        $req -> bindParam(':idv', $id_vehicule, PDO::PARAM_INT);
        $req -> bindParam(':idc', $id_conducteur, PDO::PARAM_INT);
        $req -> execute();
        header('location: association_vehicule_conducteur.php');

    }
    // cas d'une modification
    else {

        $req = $pdo -> prepare ("UPDATE association_vehicule_conducteur SET id_vehicule = :idv, id_conducteur = :idc WHERE id_association = :id");
        $req -> bindParam(':id', $id_association, PDO::PARAM_INT);
        $req -> bindParam(':idv', $id_vehicule, PDO::PARAM_INT);
        $req -> bindParam(':idc', $id_conducteur, PDO::PARAM_INT);
        $req -> execute();
        header('location: association_vehicule_conducteur.php');

    }

}

include ('inc/head.inc.php');
?>
<main class="container">
    <h1 class="text-center">Liste des <?= $nb ?> associations</h1>

    <!-- Affichage de la Liste des conducteurs -->
    <table class="table table-striped">
        <tr>
            <th>id_association</th>
            <th>Véhicule</th>
            <th>Conducteur</th>
            <th class="text-center">Modification</th>
            <th class="text-center">Suppression</th>
        </tr>

        <?php foreach ($associations as $association) : ?>
            <tr>
                <td><?= $association['id_association'] ?></td>
                <td><?= $association['prenom'] . ' ' . $association['nom'] . ' ' . $association['idc'] ?></td>
                <td><?= $association['marque'] . ' ' . $association['modele'] . ' ' . $association['idv'] ?></td>

                <td class="text-center">
                    <a href="?action=modif&id=<?= $association['id_association'] ?>">
                        <button type="button" class="btn btn-info">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                        </button>
                    </a>
                </td>
                <td class="text-center">
                    <a href="?action=suppr&id=<?= $association['id_association'] ?>">
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
        <input type="hidden" name="id_conducteur" value="<?= isset($id_association)?$id_association:0 ?>">

        <div class="form-group">
            <label for="vehicule">Véhicule :</label>
            <select class="form-control" name="idv">
                <?php foreach ($vehicules as $vehicule) : ?>
                    <option value="<?= $vehicule['id_vehicule'] ?>"><?= $vehicule['id_vehicule'] . ' ' . $vehicule['marque'] . ' ' . $vehicule['modele'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nom">Conducteur :</label>
            <select class="form-control" name="idc">
            <?php foreach ($conducteurs as $conducteur) : ?>
                <option value="<?= $conducteur['id_conducteur'] ?>"><?= $conducteur['id_conducteur'] . ' ' . $conducteur['prenom'] . ' ' . $conducteur['nom'] ?></option>
            <?php endforeach; ?>
        </select>
        </div>

        <?php $action=(isset($id_conducteur)?"Modifier l'":"Ajouter une ") ?>
        <button type="submit" class="btn btn-submit"><?= $action ?>asociation</button>
    </form>
</main>

<?php
include ('inc/footer.inc.php');
?>
