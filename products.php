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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  /* ----- PAGE HEADER ----- */
  .page-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    padding: 5rem 2.5rem 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)" /></svg>');
    opacity: 0.5;
  }
  .page-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3.5rem;
    font-weight: 900;
    color: white;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
    animation: fadeInUp 0.8s ease-out;
  }
  .page-header p {
    font-size: 1.3rem;
    color: rgba(255, 255, 255, 0.9);
    position: relative;
    z-index: 1;
    animation: fadeInUp 0.8s ease-out 0.2s backwards;
  }
  /* ----- BOOKS GRID ----- */
  .books-grid {
    padding: 4rem 2.5rem;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
  }
  .book {
    background: white;
    padding: 0;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    animation: fadeInUp 0.6s ease-out backwards;
  }
  .book:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 12px 40px rgba(233, 69, 96, 0.2);
    border-color: var(--accent-color);
  }
  .book-header {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
    padding: 2rem;
    text-align: center;
    position: relative;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .book-icon {
    font-size: 3rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0.5rem;
    animation: float 3s ease-in-out infinite;
  }
  .book-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  .book h3 {
    color: var(--text-dark);
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.8rem;
    line-height: 1.4;
    min-height: 2.8rem;
  }
  .book-category {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.1), rgba(255, 107, 107, 0.1));
    color: var(--accent-color);
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
    width: fit-content;
  }
  .book-price {
    color: var(--accent-color);
    font-size: 2rem;
    font-weight: 700;
    margin: 1rem 0 1.5rem;
  }
  .book-actions {
    display: flex;
    gap: 0.8rem;
    margin-top: auto;
  }
  .btn {
    flex: 1;
    padding: 0.8rem 1rem;
    border: none;
    border-radius: 12px;
    color: white;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
  }
  .btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
  }
  .btn:hover::before {
    width: 300px;
    height: 300px;
  }
  .overview-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }
  .overview-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }
  .cart-btn {
    background: linear-gradient(135deg, #1abc9c, #16a085);
    box-shadow: 0 4px 15px rgba(26, 188, 156, 0.3);
  }
  .cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(26, 188, 156, 0.4);
  }
  /* ----- MODAL ----- */
  .overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    animation: fadeIn 0.3s ease-out;
    padding: 2rem;
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  .overlay.show {
    display: flex;
  }
  .modal {
    background: white;
    padding: 2.5rem;
    border-radius: 24px;
    text-align: center;
    width: 90%;
    max-width: 600px;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
  }
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  .modal-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.5rem;
    color: white;
  }
  .modal h2 {
    color: var(--text-dark);
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }
  .modal small {
    display: inline-block;
    background: linear-gradient(135deg, rgba(233, 69, 96, 0.1), rgba(255, 107, 107, 0.1));
    color: var(--accent-color);
    padding: 0.5rem 1.2rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }
  #ovSummary {
    text-align: left;
    color: #555;
    font-size: 0.95rem;
    line-height: 1.8;
    margin: 1.5rem 0;
    padding: 1.2rem;
    background: rgba(233, 69, 96, 0.05);
    border-radius: 12px;
    border-left: 4px solid var(--accent-color);
    font-family: 'Poppins', sans-serif;
  }
  .modal p.book-price-modal {
    color: var(--accent-color);
    font-size: 2.5rem;
    font-weight: 700;
    margin: 1.5rem 0;
  }
  .modal .btn {
    margin-top: 1.5rem;
    width: auto;
    padding: 0.9rem 2.5rem;
  }
  .close-btn {
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
  }
  .close-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
  }
  /* ----- CART POPUP ----- */
  #cartPopup {
    position: fixed;
    top: 100px;
    right: 30px;
    background: linear-gradient(135deg, #1abc9c, #16a085);
    color: white;
    padding: 1.2rem 2rem;
    border-radius: 16px;
    font-size: 1rem;
    font-weight: 600;
    box-shadow: 0 8px 32px rgba(26, 188, 156, 0.4);
    opacity: 0;
    transform: translateX(400px);
    pointer-events: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 3000;
    display: flex;
    align-items: center;
    gap: 0.8rem;
  }
  #cartPopup.show {
    opacity: 1;
    transform: translateX(0);
  }
  #cartPopup i {
    font-size: 1.5rem;
  }
  /* ----- ANIMATIONS ----- */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  /* Add staggered animation delays */
  .book:nth-child(1) { animation-delay: 0.1s; }
  .book:nth-child(2) { animation-delay: 0.15s; }
  .book:nth-child(3) { animation-delay: 0.2s; }
  .book:nth-child(4) { animation-delay: 0.25s; }
  .book:nth-child(5) { animation-delay: 0.3s; }
  .book:nth-child(6) { animation-delay: 0.35s; }
  /* ----- RESPONSIVE ----- */
  @media (max-width: 768px) {
    .page-header h2 {
      font-size: 2.5rem;
    }
    .page-header p {
      font-size: 1.1rem;
    }
    .books-grid {
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      padding: 2rem 1.5rem;
      gap: 1.5rem;
    }
    #cartPopup {
      right: 15px;
      top: 80px;
      font-size: 0.9rem;
      padding: 1rem 1.5rem;
    }
    .modal {
      padding: 2rem;
      max-width: 95%;
    }
  }
  @media (max-width: 480px) {
    .books-grid {
      grid-template-columns: 1fr;
    }
    .book-actions {
      flex-direction: column;
    }
  }
</style>
</head>
<body>
  <div class="container">
    <header>
      <div class="navbar">
        <div class="logo">
          <i class="fas fa-book-open"></i>
          <h1>StoryStack</h1>
        </div>
        <nav class="nav-links">
          <a href="index.html" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Home</span>
          </a>
          <a href="products.php" class="nav-link active">
            <i class="fas fa-book"></i>
            <span>Browse Books</span>
          </a>
          <a href="cart.html" class="nav-link cart-link">
            <i class="fas fa-shopping-cart"></i>
            <span>Cart</span>
            <span class="cart-badge" id="cart-count">0</span>
          </a>
        </nav>
        
        <div id="auth-links" class="auth-nav">
          <a href="about.html" class="nav-link">About Us</a>
          <span class="nav-divider">|</span>
          <a href="login.html" class="nav-link">Login</a>
          <span class="nav-divider">|</span>
          <a href="signup.html" class="btn-signup">Sign Up</a>
        </div>
      </div>
    </header>
    <div class="page-header">
      <h2><i class="fas fa-book-reader"></i> Browse Our Book Collection</h2>
      <p>Explore a wide variety of genres and find your next favorite book!</p>
    </div>
    <div class="books-grid">
      <?php while($book = $result->fetch_assoc()): ?>
        <div class="book" 
             data-id="<?= $book['id'] ?>"
             data-title="<?= htmlspecialchars($book['title']) ?>"
             data-category="<?= htmlspecialchars($book['category']) ?>"
             data-summary="<?= htmlspecialchars($book['summary']) ?>"
             data-price="<?= $book['price'] ?>">
          <div class="book-header">
            <i class="fas fa-book book-icon"></i>
          </div>
          <div class="book-content">
            <h3><?= htmlspecialchars($book['title']) ?></h3>
            <span class="book-category">
              <i class="fas fa-tag"></i>
              <?= htmlspecialchars($book['category']) ?>
            </span>
            <p class="book-price">$<?= number_format($book['price'],2) ?></p>
            <div class="book-actions">
              <button class="btn overview-btn" onclick="openOverviewFromData(this)">
                <i class="fas fa-info-circle"></i>
                <span>Overview</span>
              </button>
              <button class="btn cart-btn" onclick="addToCartFromData(this)">
                <i class="fas fa-cart-plus"></i>
                <span>Add</span>
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <div class="overlay" id="overlay" onclick="closeOverview()">
      <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-icon">
          <i class="fas fa-book-open"></i>
        </div>
        <h2 id="ovTitle"></h2>
        <small id="ovCat"></small>
        <p id="ovSummary"></p>
        <p id="ovPrice" class="book-price-modal"></p>
        <button class="btn close-btn" onclick="closeOverview()">
          <i class="fas fa-times"></i>
          Close
        </button>
      </div>
    </div>
    <div id="cartPopup">
      <i class="fas fa-check-circle"></i>
      <span>Added to cart successfully!</span>
    </div>
    <footer>
      <p>&copy; 2025 StoryStack - Bookstore. All rights reserved.</p>
    </footer>
  </div>
<script>
  // ----- CART COUNT -----
  function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById("cart-count").textContent = totalItems;
  }
  // ----- AUTH LINKS -----
  function updateAuthLinks() {
    const user = JSON.parse(localStorage.getItem("user"));
    const authLinks = document.getElementById("auth-links");
    if(user){
      authLinks.innerHTML = `
        <a href="about.html" class="nav-link">About Us</a>
        <span class="nav-divider">|</span>
        <span class="user-email"><i class="fas fa-user-circle"></i> ${user.username}</span>
        <span class="nav-divider">|</span>
        <a href="#" onclick="logout(); return false;" class="nav-link">Logout</a>
      `;
    } else {
      authLinks.innerHTML = `
        <a href="about.html" class="nav-link">About Us</a>
        <span class="nav-divider">|</span>
        <a href="login.html" class="nav-link">Login</a>
        <span class="nav-divider">|</span>
        <a href="signup.html" class="btn-signup">Sign Up</a>
      `;
    }
  }
  function logout() {
    localStorage.removeItem("user");
    window.location.href="php/logout.php";
  }
  // ----- MODAL (Using data attributes - SAFE for special characters) -----
  function openOverviewFromData(button) {
    const bookDiv = button.closest('.book');
    const title = bookDiv.dataset.title;
    const category = bookDiv.dataset.category;
    const summary = bookDiv.dataset.summary;
    const price = bookDiv.dataset.price;
    
    document.getElementById("ovTitle").innerText = title;
    document.getElementById("ovCat").innerText = category;
    document.getElementById("ovSummary").innerText = summary || "No summary available.";
    document.getElementById("ovPrice").innerText = "$" + Number(price).toFixed(2);
    document.getElementById("overlay").classList.add("show");
  }
  
  function closeOverview(){
    document.getElementById("overlay").classList.remove("show");
  }
  
  // ----- ADD TO CART (Using data attributes - SAFE for special characters) -----
  function addToCartFromData(button){
    const bookDiv = button.closest('.book');
    const id = bookDiv.dataset.id;
    const title = bookDiv.dataset.title;
    const price = parseFloat(bookDiv.dataset.price);
    const category = bookDiv.dataset.category;
    
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const index = cart.findIndex(i=>i.id==id);
    if(index>-1){
      cart[index].quantity +=1;
    } else {
      cart.push({id, title, price, category, quantity:1});
    }
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
    
    const popup = document.getElementById("cartPopup");
    popup.classList.add("show");
    setTimeout(()=>popup.classList.remove("show"),3000);
  }
  
  // ----- NAVBAR SCROLL EFFECT -----
  window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });
  // Initialize
  updateCartCount();
  updateAuthLinks();
</script>
</body>
</html>
<?php $conn->close(); ?>