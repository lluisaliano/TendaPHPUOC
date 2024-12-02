<?php session_start();
	require_once("utils/dbconnection.php"); 
	
    // Recuperem les categoríes de la base de dades
	$categories = mysqli_query($connection,"SELECT * FROM categories");
    while ($category = mysqli_fetch_array($categories, MYSQLI_ASSOC))
    {
        $categoriesArray[] = $category;
    }
	
?>

<html>
	<head>
		<title>Inici - Tenda UOC</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="styles/styles.css">
	</head>
	
	<body>
        <header>
            <div class='header_container'>
            <h1><a class="header_home_button" href="index.php">TENDA UOC</a></h1>
            <nav>
            <?php
					// Mostrem les categoríes en el menú
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
			<?php
            // Pasarem la categoria clicada com a paràmetre de la URL (category) a la pàgina category.php
				foreach ($categoriesArray as $category)
				{
					echo '
                    <section class="main_category_section">
                    <a class="main_category_button" href="category.php?id='.$category['id'].'">
                    <img class="main_category_image" src="assets/'.$category['nom'].'.png">'.$category['nom'].'
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
	// Liberar resultats
	mysqli_free_result($categories);
	// Tancar connexió
	mysqli_close($connection);
?>