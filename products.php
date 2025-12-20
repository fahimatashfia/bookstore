<?php
session_start();
require_once 'php/db_config.php';

$sql = "SELECT * FROM books ORDER BY category, title";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>StoryStack - Browse Books</title>
<link rel="stylesheet" href="style.css" />
<style>
  /* ----- MODAL ----- */
  .overlay {
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.6);
    display:none;
    justify-content:center;
    align-items:center;
  }
  .overlay.show { display:flex; }
  .modal {
    background:#fff;
    padding:30px;
    border-radius:20px;
    text-align:center;
    width:380px;
  }

  /* ----- BOOKS GRID ----- */
  .books-grid {
    padding:40px;
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(230px,1fr));
    gap:25px;
  }
  .book {
    background:#fff;
    padding:22px;
    border-radius:22px;
    text-align:center;
    box-shadow:0 15px 30px rgba(0,0,0,.15);
  }
  .book h3 { color:#1f1c2c; text-align:center; }
  .book span { display:block; margin:8px 0; color:#555; }
  .book p { color:#ff6a00; font-weight:bold; }
  .btn { padding:10px 22px; border:none; border-radius:25px; color:#fff; cursor:pointer; margin:5px; }
  .overview-btn { background:#ff512f; }
  .cart-btn { background:#1abc9c; }

  /* ----- CART POPUP ----- */
  #cartPopup {
    position:fixed;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    background:#1abc9c;
    color:#fff;
    padding:20px 30px;
    border-radius:35px;
    font-size:18px;
    font-weight:bold;
    box-shadow:0 12px 30px rgba(0,0,0,.3);
    opacity:0;
    pointer-events:none;
    transition:.4s ease;
    z-index:1000;
    text-align:center;
  }
  #cartPopup.show { opacity:1; }
</style>
</head>

<body>
  <div class="container">
    <header>
      <h1>StoryStack</h1>
      <nav>
        <a href="index.html">Home</a>
        <a href="products.php">Browse Books</a>
        <a href="cart.html">Cart (<span id="cart-count">0</span>)</a>

        <div id="auth-links" class="auth-nav">
          <a href="about.html">About Us</a>
          <span class="nav-divider">|</span>
          <a href="login.html">Login</a>
          <span class="nav-divider">|</span>
          <a href="signup.html">Sign Up</a>
        </div>
      </nav>
    </header>

    <main class="welcome-section">
      <h2>Browse Our Book Collection</h2>
      <p>Explore a wide variety of genres and find your next favorite book!</p>
    </main>

    <div class="books-grid">
      <?php while($book = $result->fetch_assoc()): ?>
        <div class="book">
          <h3><?= htmlspecialchars($book['title']) ?></h3>
          <span><?= htmlspecialchars($book['category']) ?></span>
          <p>$<?= number_format($book['price'],2) ?></p>

          <button class="btn overview-btn"
            onclick="openOverview(
              '<?= htmlspecialchars($book['title'],ENT_QUOTES) ?>',
              '<?= htmlspecialchars($book['category'],ENT_QUOTES) ?>',
              <?= $book['price'] ?>
            )">Overview</button>

          <button class="btn cart-btn"
            onclick="addToCart(
              '<?= $book['id'] ?>',
              '<?= htmlspecialchars($book['title'],ENT_QUOTES) ?>',
              <?= $book['price'] ?>,
              '<?= htmlspecialchars($book['category'],ENT_QUOTES) ?>'
            )">Add to Cart</button>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="overlay" id="overlay">
      <div class="modal">
        <h2 id="ovTitle"></h2>
        <small id="ovCat"></small>
        <p id="ovPrice"></p>
        <button class="btn cart-btn" onclick="closeOverview()">Close</button>
      </div>
    </div>

    <div id="cartPopup">Added to the cart</div>

    <footer>
      <p>&copy; 2025 StoryStack - Bookstore. All rights reserved.</p>
    </footer>
  </div>

<script>
  // ----- CART COUNT -----
  function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    document.getElementById("cart-count").textContent = cart.length;
  }

  // ----- AUTH LINKS -----
  function updateAuthLinks() {
    const user = JSON.parse(localStorage.getItem("user"));
    const authLinks = document.getElementById("auth-links");

    if(user){
      authLinks.innerHTML = `
        <a href="about.html">About Us</a>
        <span class="nav-divider">|</span>
        <span class="user-email">${user.username}</span>
        <span class="nav-divider">|</span>
        <a href="#" onclick="logout(); return false;">Logout</a>
      `;
    } else {
      authLinks.innerHTML = `
        <a href="about.html">About Us</a>
        <span class="nav-divider">|</span>
        <a href="login.html">Login</a>
        <span class="nav-divider">|</span>
        <a href="signup.html">Sign Up</a>
      `;
    }
  }

  function logout() {
    localStorage.removeItem("user");
    window.location.href="php/logout.php";
  }

  // ----- MODAL -----
  function openOverview(t,c,p){
    document.getElementById("ovTitle").innerText = t;
    document.getElementById("ovCat").innerText = c;
    document.getElementById("ovPrice").innerText = "$" + Number(p).toFixed(2);
    document.getElementById("overlay").classList.add("show");
  }

  function closeOverview(){
    document.getElementById("overlay").classList.remove("show");
  }

  // ----- ADD TO CART (localStorage) -----
  function addToCart(id,title,price,category){
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    const index = cart.findIndex(i=>i.id==id);
    if(index>-1){
      cart[index].quantity +=1;
    } else {
      cart.push({id, title, price: parseFloat(price), category, quantity:1});
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();

    const popup = document.getElementById("cartPopup");
    popup.classList.add("show");
    setTimeout(()=>popup.classList.remove("show"),2000);
  }

  updateCartCount();
  updateAuthLinks();
</script>
</body>
</html>

<?php $conn->close(); ?>