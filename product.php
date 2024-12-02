<?php session_start();
	require_once("utils/dbconnection.php"); 
	
	// Recuperem les categoríes
	$categories = mysqli_query($connection,"SELECT * FROM categories");

    while ($category = mysqli_fetch_array($categories, MYSQLI_ASSOC))
    {
        $categoriesArray[] = $category;
    }

    // Recuperem el producte, i guardem els seus atributs
	$productId = $_GET['id'];
	
	$selectedProduct = mysqli_query($connection,"SELECT * FROM productes WHERE id=$productId");
	$product = mysqli_fetch_array($selectedProduct, MYSQLI_ASSOC);

    $productName = $product['nom'];
    $productDescription = $product['descripcio'];
    $productPrice = $product['preu'];
    $productCategoryId = $product['categoria'];

    // Recuperem el nom de la categoría del producte
    $productCategory = mysqli_query($connection,"SELECT nom FROM categories WHERE id=$productCategoryId");
    $categoryName = mysqli_fetch_array($productCategory, MYSQLI_ASSOC);
    


?>
<html>
	<head>
        <title><?php echo $productName; ?> - Tenda UOC</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="styles/styles.css">
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
		<main class="main_category">
            <figure class="prouct_item">
            <?php
            echo '<img class="product_image" src="assets/'.mb_strtolower($categoryName['nom'], 'UTF-8').'/'.$productId.'.png" alt="$productName">';
            ?>
                    <figcaption class="product_description">
                    <div>
                    <h1 class="category_title"><?php echo mb_strtoupper($productName, 'UTF-8'); ?></h1>
                    <p><?php echo $productDescription; ?></p>
                    <p><?php echo $productPrice; ?> €</p>
                    </div>
                    <!-- Guardem la id del producte en un input invisible, per després poder-la enviar a la cistella -->
                    <form name="basket_form" id="product_form">
                        <input type="hidden" name="productId" value="<?php echo $productId; ?>">
                        <button type="submit">Afegir a la cesta</button>
                    </form>
                    
                    </figcaption>
            </figure>
        


        </main>
        <footer>
        Comerç Electrònic - 2024 | <a href="admin.php">Panell Administració</a>
        </footer>
	</body>
    <script>
        const form = document.querySelector('#product_form')
        form.addEventListener('submit', (e) => {
            // Evitem que la pàgina es reinicii
            e.preventDefault();

            // Creem un FormData amb les dades del formulari
            const formData = new FormData(form); 

            // Fem un post a basket.php amb les dades del formulari (que contenen la id del producte)
            fetch('basket.php', {
                method: 'POST',
                body: formData
                })
                .then(() => alert("Producte afegit!"))
                .catch(e => alert("No s'ha afegit el producte per l'error: " + e))
        })
    </script>
</html>
<?php
	mysqli_free_result($selectedProduct);
	mysqli_close($connection);
?>