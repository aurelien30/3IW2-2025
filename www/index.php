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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $exception) {
    echo "Erreur : " . $exception->getMessage();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pwd = $_POST['pwd'] ?? '';
    $pwdConfirm = $_POST['pwdConfirm'] ?? '';

    if ($pwd === '') {
        $errors[] = "Le mot de passe est requis.";
    } else {
        if (mb_strlen($pwd) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if (!preg_match('/[A-Za-z]/', $pwd)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre.";
        }
        if (!preg_match('/[0-9]/', $pwd)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if ($pwd !== $pwdConfirm) $errors[] = "Les mots de passe ne correspondent pas.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM "user" WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = "Cet email est déjà utilisé.";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur lors de la vérification de l'email en base.";
        }
    }

    if (empty($errors)){
        $passwordHash = password_hash ($pwd, PASSWORD_DEFAULT);
        try{
            $stmt = $pdo->prepare('INSERT INTO "user"(firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)');
            $stmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':email' => $email,
            ':password' => $passwordHash
]);

$success = "Inscription réussie !";

        } catch (Exception $e){
             $errors[] = "Erreur lors de l'insertion en base de données.";
        }

    }
} 
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Simple Formulaire</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
    <h1>Formulaire Utilisateur</h1>

    <?php if (!empty($success)): ?>
    <div role="status" aria-live="polite">
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>


    <?php if (!empty($errors)): ?>
        <div role="alert" aria-live="assertive">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <fieldset>
            <legend>Inscription</legend>
            <label for="firstname">Prénom :</label>
            <input type="text" id="firstname" name="firstname" placeholder="Prénom">

            <label for="lastname">Nom :</label>
            <input type="text" id="lastname" name="lastname" placeholder="Nom">

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="pwd">Mot de passe :</label>
            <input type="password" id="pwd" name="pwd" placeholder="Mot de passe" required>

            <label for="pwdConfirm">Confirmer le mot de passe :</label>
            <input type="password" id="pwdConfirm" name="pwdConfirm" placeholder="Confirmer mot de passe" required>

            <button type="submit" name="submit">S'inscrire</button>
        </fieldset>
    </form>
</body>
</html>
