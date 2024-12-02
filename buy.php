<?php session_start();
	require_once("utils/dbconnection.php"); 

	// Si s'ha clicat el botó finalBuyButton
    if(isset($_GET['comprar']))
	{
		// Recuperem la sessió del client
		$clientId = $_SESSION['clientId'];
		
		// Insertem una nova comanda a la base de dades
		$data = date("Y-m-d H:i");
		$comanda = mysqli_query($connection,"INSERT INTO comandes(client,data,total) VALUES('$clientId','$data','".$_SESSION['total_basket_products']."')");
		
		// Recuperem la id de la comanda insertada
		$comandaId = mysqli_insert_id($connection);	
		
		// Insertem les dades del producte a la relació de les línies de comandes per a cada producte que tinguem a la cistella
		foreach($_SESSION['basket'] as $product) {
		$amount = $product['amount'];
		$productId = $product['productId'];
    	$linies_comandes = mysqli_query($connection,"INSERT INTO linies_comandes(comanda,producte,quantitat) VALUES('$comandaId','$productId','$amount')");
    }
		// Finalment, buidem la cistella i la id del client.
		unset($_SESSION['basket']);	
		unset($_SESSION['clientId']);
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
		<title>Compra - Tienda online</title>
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
        <h1>Resum Cistella</h1>	
			<?php 
				// Generem una taula, les files de la cual seran els productes amb la seva informació
				echo '<table class="basket_table">
					<thead><tr><th>Productes</th><th>Quantitat</th><th>Preu</th></tr></thead><tbody>';
				foreach($_SESSION['basket'] as $producto) {
					$amount = $producto['amount'];
					$productId = $producto['productId'];
					// Recuperem els atributs de la base de dades de cada producte
	        		$productData = mysqli_query($connection,"SELECT * FROM productes WHERE id='".$productId."'");
					$productRow = mysqli_fetch_array($productData, MYSQLI_ASSOC);
					// Mostrem les files amb els productes.
					echo '<tr><td>'.$productRow['nom'].'</td><td>'.$amount.'</td><td>'.$productRow['preu'] * $amount.' €</td></tr>';
	        }
			echo '</tbody></table>';
	        echo '<p><b>Total: </b>'.$_SESSION['total_basket_products'].'&euro;</p>';
	      ?>
		  <!-- Amb el botó finalBuyButton, gestionat amb javascript, executarem la lògica de php per afegir la compra a la base de dades -->
		  <button id="finalBuyButton">Comprar</button>
        </main>
        <footer>
		Comerç Electrònic - 2024 | <a href="admin.php">Panell Administració</a>
        </footer>
	</body>
	<script>
		// Recuperem el botó finalBuyButton
		const finalBuyButton = document.querySelector('#finalBuyButton')
		// Quan es faci click al botó, envia una petició GET a buy.php amb el paràmetre comprar. Després redirecciona a l'usuari a la pàgina d'inici
		finalBuyButton.addEventListener('click', () => {
			fetch('buy.php?comprar')
			.then(() => alert("COMPRA REALITZADA CORRECTAMENT!"))
			.then(() => window.location.href = 'index.php')
		})
	</script>
</html>
<?php
	mysqli_free_result($categories);
	mysqli_close($connection);
?>