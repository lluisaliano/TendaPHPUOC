<?php session_start();
	require_once("utils/dbconnection.php"); 
	
	// Recuperem les categoríes
	$categories = mysqli_query($connection,"SELECT * FROM categories");

    while ($category = mysqli_fetch_array($categories, MYSQLI_ASSOC))
    {
        $categoriesArray[] = $category;
    }

    // Recuperem la categoria segons el seu id i guardem el nom
	$categoryId = $_GET['id'];
	
	$selectedCategory = mysqli_query($connection,"SELECT * FROM categories WHERE id=$categoryId");
	$category = mysqli_fetch_array($selectedCategory, MYSQLI_ASSOC);
	$categoryName = $category['nom'];
	
	// Recuperem, a partir de la categoría, els productes d'aquesta
	$products = mysqli_query($connection,"SELECT * FROM productes WHERE categoria=$categoryId");
?>
<html>
	<head>
		<title><?php echo $categoryName; ?> - Tienda online</title>
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
		<main class="main_category">
        <h1 class="category_title"><?php echo mb_strtoupper($categoryName, 'UTF-8'); ?></h1>
        <?php
				// Pasem la id del producte com a paràmetre de la URL
				while ($product = mysqli_fetch_array($products, MYSQLI_ASSOC))
				{
					echo '
                    <section class="main_product_section">
                    <a class="main_product_button" href="product.php?id='.$product['id'].'">
                    <img class="main_product_image" src="assets/'.mb_strtolower($categoryName, 'UTF-8').'/'.$product['id'].'.png"/>
                    <p>'.$product['nom'].'</p>
                    </a>
                    </section>';
				}
			?>
        </main>
        <footer>
		Comerç Electrònic - 2024 | <a href="admin.php">Panell Administració</a>
        </footer>
	</body>
</html>
<?php
	mysqli_free_result($selectedCategory);
	mysqli_free_result($products);

	mysqli_close($connection);
?>