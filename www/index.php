<?php
/*
Tout le code doit se faire dans ce fichier PHP

Réalisez un formulaire HTML contenant :
- firstname
- lastname
- email
- pwd
- pwdConfirm

Créer une table "user" dans la base de données, regardez le .env à la racine et faites un build de docker
si vous n'arrivez pas à les récupérer pour qu'il les prenne en compte

Lors de la validation du formulaire vous devez :
- Nettoyer les valeurs, exemple trim sur l'email et lowercase (5 points)
- Attention au mot de passe (3 points)
- Attention à l'unicité de l'email (4 points)
- Vérifier les champs sachant que le prénom et le nom sont facultatifs
- Insérer en BDD avec PDO et des requêtes préparées si tout est OK (4 points)
- Sinon afficher les erreurs et remettre les valeurs pertinantes dans les inputs (4 points)

Le design je m'en fiche mais pas la sécurité

Bonus de 3 points si vous arrivez à envoyer un mail via un compte SMTP de votre choix
pour valider l'adresse email en bdd

Pour le : 22 Octobre 2025 - 8h
M'envoyer un lien par mail de votre repo sur y.skrzypczyk@gmail.com
Objet du mail : TP1 - 2IW3 - Nom Prénom
Si vous ne savez pas mettre votre code sur un repo envoyez moi une archive
*/


try {
    $host = 'db';
    $dbname = 'devdb';
    $user = 'devuser';
    $password = 'devpass';

    $dsn = "pgsql:host=$host;port=5432;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // gestion des erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC  
    ]);
} catch (PDOException $exception) {
     $exception->getMessage() . "<br>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP1 - Formulaire Utilisateur</title>
</head>
<h1>FORMULAIRE UTILISATEUR</h1>
<body>
<form method="POST" action="">
    <input type="text" name="firstname" placeholder="Prénom">
    <input type="text" name="lastname" placeholder="Nom">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="pwd" placeholder="Mot de passe">
    <input type="password" name="pwdConfirm" placeholder="Confirmer mot de passe">
    <button type="submit" name="submit">S'inscrire</button>
</form>

</body>
</html>
