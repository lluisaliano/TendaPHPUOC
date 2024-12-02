<?php session_start();
	require_once("utils/dbconnection.php"); 

    // Si el client s'ha registrat, tindrem el seu nom
    if(isset($_POST['nom'])) {
        // Obtenim les dades del formulari
        $nom = $_POST['nom'];
		$adreça = $_POST['adreça'];
		$email = $_POST['email'];
        $contrasenya = $_POST['contrasenya'];

        // Comprovem si el client ja existeix
        $result = mysqli_query($connection, "SELECT id FROM clients WHERE email = '$email'");

        if (mysqli_num_rows($result) > 0) {
         // Si ja existeix, recuperem la seva id i la guardem a la sessió.
         $row = mysqli_fetch_assoc($result);
         $clientId = $row['id'];
         $_SESSION['clientId'] = $clientId;
        // Finalment, redireccionem a buy.php
		header("Location:buy.php");
        exit;
        }

        // Si el client no exiteix, introduim les seves dades a la base de dades
		$client = mysqli_query($connection,"INSERT INTO clients(nom,adreça,email, contrasenya, tipus_client) VALUES('$nom','$adreça','$email', '$contrasenya', 'registrat')");
		// Després guardem la id del client a la sessió
        $_SESSION['clientId'] = mysqli_insert_id($connection);

        // Finalment, redireccionem a buy.php
		header("Location:buy.php");
        exit;

    } else if(isset($_POST['email'])) {
        // Si no tenim el nom però si el correu, l'usuari no s'ha registrat
        // Obtenim les dades del formulari
        $adreça = $_POST['adreça'];
		$email = $_POST['email'];

        // Insertem les dades del client a la base de dades. 
        // No comprovem que l'usuari existeixi perquè no s'ha registrat. 
        // Teòricament, hauriem de borrar les seves dades després de finalitzar la comanda...
		$client = mysqli_query($connection,"INSERT INTO clients(adreça, email, tipus_client) VALUES('$adreça','$email', 'convidat')");
		// Guardem la id del client a la sessió
		$_SESSION['clientId'] = mysqli_insert_id($connection);

        // Finalment, redireccionem a buy.php
		header("Location:buy.php");
        exit;
    }
	
	// Recuperem les categoríes
	$categories = mysqli_query($connection,"SELECT * FROM categories");

    while ($category = mysqli_fetch_array($categories, MYSQLI_ASSOC))
    {
        $categoriesArray[] = $category;
    }

?>
<html>
	<head>
		<title>Dades - Tienda online</title>
		<link rel="stylesheet" href="styles/styles.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width" />
	</head>
	
	<body>
    <header>
            <div class="header_container">
            <a class="header_home_button bold_text" href="index.php">TENDA UOC</a>
            <nav>
            <?php
                    foreach ($categoriesArray as $category)
					{
						echo '<a class="header_category_button" href="category.php?id='.$category['id'].'">'.$category['nom'].'</a>';
					}
				?>
                <a id="basket_link" href="basket.php"><img class="header_image" src="assets/carrito.svg" /><p id="basket_number">(<?php if(isset($_SESSION['basket'])) { echo count($_SESSION['basket']); } else { echo 0; } ?>)</p></a>
            </nav>
            </div>
        </header>
		<main>
        <h1>Formulari de compra</h1>
                <div id="registerForm">
				<form  name="checkout" action="checkout.php" method="POST">
                    <fieldset>
                        <legend>Com usuari convidat</legend>
                        <label for="registerFormDirection">Adreça:</label>
                        <input id ="registerFormDirection" required name="adreça" type="text" />
                        <label for="registerFormEmail">Email:</label>
                        <input id="registerFormEmail" required name="email" type="text" />
						<button type="submit">Continuar</button>
                    </fieldset>
                </form>    
                <form id="registerForm" name="checkout" action="checkout.php" method="POST">    
                    <fieldset>
                        <legend>Com usuari registrat</legend>
						<label for="registerFormName">Nom Complert:</label>
                        <input id="registerFormName" required name="nom" type="text"/>
                        <label for="registerFormPassword">Contaseña:</label>
                        <input id="registerFormPassword" required name="contrasenya" type="text"/>
                        <label for="registerFormDirection">Adreça:</label>
                        <input id ="registerFormDirection" required name="adreça" type="text" />
                        <label for="registerFormEmail">Email:</label>
                        <input id="registerFormEmail" required name="email" type="text" />
						<button type="submit">Continuar</button>
                    </fieldset>
				</form>
                </div>

        </main>
        <footer>
        Comerç Electrònic - 2024 | <a href="admin.php">Panell Administració</a>
        </footer>
	</body>
</html>
<?php
	mysqli_free_result($categories);
	mysqli_close($connection);
?>