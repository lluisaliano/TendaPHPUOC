<?php session_start();
	require_once("utils/dbconnection.php"); 

    $_SESSION['total_basket_products'] = 0;

    if (isset($_GET['cleanBasket']) && $_GET['cleanBasket'] == 1) {
        $_SESSION = array(); 
    }

    // Revisem si hem rebut un nou producte per afegir a la cistella
    if(isset($_POST["productId"])) {
        $id = $_POST["productId"];

        // Guardem les dades del nou producte a un array
        $newProduct = array(
            'productId' => $_POST["productId"],
            'amount' => 1);

        // Si tenim productes a la cistella  
        if(!empty($_SESSION['basket']))  {
            // Revisem si ja tenim el producte afegit i si es aixi incrementem la quantitat
            if(isset($_SESSION['basket'][$id]) == $id) 
            {
              $_SESSION['basket'][$id]['amount']++;
            } else {
                // Si no, lafegim
                 $_SESSION['basket'][$id] = $newProduct;
            } 
        } else {
            // Si no tenim productes, creem una nova cistella i afegim el nou producte
            $_SESSION['basket'] = array();
            $_SESSION['basket'][$id] = $newProduct;
        }   
    }

    // Modificar quantitat
    if(isset($_GET["productAmount"]))
	{
		// Recuperar la id del producte a eliminar
		$modifiedProductAmount = $_GET["productAmount"];
        $modifiedProductId = $_GET["modifiedProductId"];
		$_SESSION['basket'][$modifiedProductId]['amount'] = $modifiedProductAmount;
	}

    // Eliminar producte
	if(isset($_GET["deleteProduct"]))
	{
		// Recuperar la id del producte a eliminar
		$deleteProductId = $_GET["deleteProduct"];
		unset($_SESSION['basket'][$deleteProductId]);
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
		<title>Cistella - Tienda online</title>
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
        <h1>CISTELLA</h1>
        <?php
				// Comprovem si la cistella té dades, si no en té mostrem el missatge de cistella buida
				if(isset($_SESSION['basket']) && count($_SESSION['basket']) > 0) {
                    // Si tenim dades, generem una taula, les files de la cual seran els productes amb la seva informació
                    echo '<table class="basket_table">
                    <thead>
                    <tr>
                    <th>Productes</th>
                    <th>Quantitat</th>
                    <th>Preu</th><th>Accions</th>
                    </tr>
                    </thead>
                    <tbody>';
					foreach($_SESSION['basket'] as $producto) {
						$amount = $producto['amount'];
						$productId = $producto['productId'];
						// Recuperem els atributs de la base de dades de cada producte
	        	        $productData = mysqli_query($connection,"SELECT * FROM productes WHERE id='".$productId."'");
						$productRow = mysqli_fetch_array($productData, MYSQLI_ASSOC);
                        // Mostrem les files amb els productes. Cada fila conté un formulari que permet modificar la quantitat i eliminar el producte. Es gestionen amb javascript
                        echo '<tr><form>
                                <td><p>'.$productRow['nom'].'</p></td>
                                <td>
                                    <input class="amountButton" data-product-id="'.$productId.'" type="number" value='.$amount.'>
                                </td>
                                <td>
                                    <p>'.$productRow['preu'] * $amount.' €</p>
                                </td>
                                <td>
                                    <input class="deleteButton" type="button" data-product-id="'.$productId.'" value="Eliminar">
                                </td>    
                             </form><tr>';

                        // Actualitzem el total de la cesta
						$_SESSION['total_basket_products'] += $amount * $productRow['preu'];
	            }
                 echo '</tbody></table>';
                 // Mostrem el total de la cistella
	             echo '<p><b>TOTAL: </b>'.$_SESSION['total_basket_products'].'&euro;</p>';
                 // Mostrem els botons de netejar cistella (Gestionat amb javasciprt) i el botó de comprar
                 echo '<div class="basket_footer">
                        <button id="clean_basket_button">Netejar Cesta</button>
                        <button><a style="color:black;text-decoration:none;" href="checkout.php">Comprar</a></button></div>';      
				}
				else 
				{
					echo "Cistella buida!";
				}	
			?>
        </main>
        <footer>
        Comerç Electrònic - 2024 | <a href="admin.php">Panell Administració</a>
        </footer>
	</body>
    <script>
        // Gestionar l'input de modificar la quantitat
        document.querySelectorAll('.amountButton').forEach((amountButton) => {
            // Quan es modifiqui la quantitat, enviem una petició amb el paràmetre productId i la id del producte
            amountButton.addEventListener('change', (e) => {
                // Recuperem la nova quantitat
                const newAmount = e.target.value;
                // Recuperem l'id del producte guardat en l'atribut especial data
                const productId = amountButton.getAttribute('data-product-id');
                // Si la quantitat es 0 o inferior, borrem el producte, enviant la petició corresponent
                if (newAmount <= 0) {
                    fetch(`basket.php/?deleteProduct=${productId}`).then(() => {location.reload()})
                    return;
                }
                // Enviem la petició, i quan s'hagi enviat, recarreguem la pàgina
                fetch(`basket.php/?productAmount=${newAmount}&modifiedProductId=${productId}`).then(() => {location.reload()})
            })
        })

        // Gestionar el botó d'eliminar un producte 
        // Obtenim tots els botons que borren productes, i per cada botó escoltem a l'event clic
        document.querySelectorAll('.deleteButton').forEach((deleteButton) => {
            // Quan es faci clic al botó, enviem una petició a basket.php amb el paràmetre deleteProduct i la id del producte
            deleteButton.addEventListener('click', (e) => {
            // Recuperem l'id del producte guardat en l'atribut especial data
            const productId = deleteButton.getAttribute('data-product-id');
            // Quan la petició s'hagi enviat, recarreguem la pàgina
            fetch(`basket.php/?deleteProduct=${productId}`).then(() => {location.reload()})
        })
        })
        
        // Gestionar botó de netejar cistella
        const cleanBasketForm = document.querySelector('#clean_basket_button')
        cleanBasketForm.addEventListener('click', (e) => {
            fetch('basket.php?cleanBasket=1', {method: 'POST'}).then(() => {location.reload()})
        })

    </script>
</html>
<?php
	mysqli_free_result($categories);
	mysqli_close($connection);
?>