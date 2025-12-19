<?php
require_once 'php/db_config.php';

// Fetch all books from database
$sql = "SELECT * FROM books ORDER BY category, title";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - StoryStack</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>StoryStack</h1>
            <nav>
                <a href="index.html">Home</a>
                <a href="products.php">Browse Books</a>
                <a href="cart.html">Cart (<span id="cart-count">0</span>)</a>
            </nav>
        </header>

        <main>
            <h2>Our Book Collection</h2>
            
            <div class="products-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($book = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo '<h3>' . htmlspecialchars($book['title']) . '</h3>';
                        echo '<span class="product-category">' . htmlspecialchars($book['category']) . '</span>';
                        echo '<div class="product-price">$' . number_format($book['price'], 2) . '</div>';
                        echo '<button class="btn btn-success add-to-cart" 
                                data-id="' . $book['id'] . '"
                                data-title="' . htmlspecialchars($book['title']) . '"
                                data-price="' . $book['price'] . '"
                                data-category="' . htmlspecialchars($book['category']) . '">
                                Add to Cart
                              </button>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No books available at the moment.</p>';
                }
                $conn->close();
                ?>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 StoryStack - Bookstore. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Cart functionality
        function getCart() {
            return JSON.parse(localStorage.getItem('cart')) || [];
        }

        function saveCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        function updateCartCount() {
            const cart = getCart();
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cart-count').textContent = totalItems;
        }

        // Add to cart
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const book = {
                    id: parseInt(this.dataset.id),
                    title: this.dataset.title,
                    price: parseFloat(this.dataset.price),
                    category: this.dataset.category,
                    quantity: 1
                };

                let cart = getCart();
                const existingItem = cart.find(item => item.id === book.id);

                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    cart.push(book);
                }

                saveCart(cart);
                
                // Visual feedback
                this.textContent = 'Added!';
                this.style.backgroundColor = '#229954';
                setTimeout(() => {
                    this.textContent = 'Add to Cart';
                    this.style.backgroundColor = '';
                }, 1000);
            });
        });

        // Initialize cart count
        updateCartCount();
    </script>
</body>
</html>