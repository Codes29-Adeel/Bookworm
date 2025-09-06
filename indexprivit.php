<?php
session_start();

// Database connection simulation
$books = [
    [
        'id' => 1,
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'genre' => 'Fiction',
        'price' => 12.99,
        'rating' => 4.5,
        'reviews' => 128,
        'stock' => 42,
        'bestseller' => true,
        'cover' => 'gatsby',
        'description' => 'A classic novel of the Jazz Age, telling the tragic story of Jay Gatsby and his pursuit of the American Dream.'
    ],
    [
        'id' => 2,
        'title' => 'To Kill a Mockingbird',
        'author' => 'Harper Lee',
        'genre' => 'Fiction',
        'price' => 10.99,
        'rating' => 4.8,
        'reviews' => 215,
        'stock' => 31,
        'bestseller' => true,
        'cover' => 'mockingbird',
        'description' => 'A gripping, heart-wrenching tale of race and prejudice through the eyes of a young girl in Alabama.'
    ],
    [
        'id' => 3,
        'title' => '1984',
        'author' => 'George Orwell',
        'genre' => 'Science Fiction',
        'price' => 9.99,
        'rating' => 4.7,
        'reviews' => 187,
        'stock' => 24,
        'bestseller' => true,
        'cover' => '1984',
        'description' => 'A dystopian social science fiction novel that examines totalitarianism, thought control, and surveillance.'
    ],
    [
        'id' => 4,
        'title' => 'Pride and Prejudice',
        'author' => 'Jane Austen',
        'genre' => 'Fiction',
        'price' => 8.99,
        'rating' => 4.6,
        'reviews' => 156,
        'stock' => 19,
        'bestseller' => false,
        'cover' => 'pride',
        'description' => 'A romantic novel that charts the emotional development of Elizabeth Bennet as she learns about the repercussions of hasty judgments.'
    ],
    [
        'id' => 5,
        'title' => 'The Hobbit',
        'author' => 'J.R.R. Tolkien',
        'genre' => 'Fantasy',
        'price' => 11.99,
        'rating' => 4.9,
        'reviews' => 241,
        'stock' => 37,
        'bestseller' => true,
        'cover' => 'hobbit',
        'description' => 'A fantasy novel about the adventures of hobbit Bilbo Baggins as he sets out on a quest with a group of dwarves.'
    ],
    [
        'id' => 6,
        'title' => 'The Da Vinci Code',
        'author' => 'Dan Brown',
        'genre' => 'Mystery',
        'price' => 13.99,
        'rating' => 4.2,
        'reviews' => 178,
        'stock' => 28,
        'bestseller' => false,
        'cover' => 'davinci',
        'description' => 'A mystery thriller that follows symbologist Robert Langdon as he investigates a murder in the Louvre Museum.'
    ]
];

$genres = array_unique(array_column($books, 'genre'));
sort($genres);

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize user data
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'purchases' => []
    ];
}

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'add_to_cart':
            $book_id = (int)$_GET['id'];
            $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

            if (isset($_SESSION['cart'][$book_id])) {
                $_SESSION['cart'][$book_id] += $quantity;
            } else {
                $_SESSION['cart'][$book_id] = $quantity;
            }
            break;

        case 'remove_from_cart':
            $book_id = (int)$_GET['id'];
            if (isset($_SESSION['cart'][$book_id])) {
                unset($_SESSION['cart'][$book_id]);
            }
            break;

        case 'update_cart':
            foreach ($_POST['quantity'] as $book_id => $quantity) {
                $book_id = (int)$book_id;
                $quantity = (int)$quantity;

                if ($quantity > 0) {
                    $_SESSION['cart'][$book_id] = $quantity;
                } else {
                    unset($_SESSION['cart'][$book_id]);
                }
            }
            break;

        case 'checkout':
            // Simulate payment processing
            $total = 0;
            foreach ($_SESSION['cart'] as $book_id => $quantity) {
                foreach ($books as $book) {
                    if ($book['id'] == $book_id) {
                        $total += $book['price'] * $quantity;
                        break;
                    }
                }
            }

            // Record purchase
            $purchase = [
                'date' => date('Y-m-d H:i:s'),
                'items' => $_SESSION['cart'],
                'total' => $total
            ];

            $_SESSION['user']['purchases'][] = $purchase;
            $_SESSION['cart'] = [];
            break;
    }

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Get book by ID
function getBookById($id)
{
    global $books;
    foreach ($books as $book) {
        if ($book['id'] == $id) {
            return $book;
        }
    }
    return null;
}

// Get cart items
$cart_items = [];
$cart_total = 0;
foreach ($_SESSION['cart'] as $book_id => $quantity) {
    $book = getBookById($book_id);
    if ($book) {
        $item_total = $book['price'] * $quantity;
        $cart_items[] = [
            'book' => $book,
            'quantity' => $quantity,
            'total' => $item_total
        ];
        $cart_total += $item_total;
    }
}

// Get recommendations (based on user's cart and bestsellers)
$recommendations = array_filter($books, function ($book) {
    return $book['bestseller'] && !isset($_SESSION['cart'][$book['id']]);
});
shuffle($recommendations);
$recommendations = array_slice($recommendations, 0, 3);

// Get purchase history
$purchases = $_SESSION['user']['purchases'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookWorm - Online Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #f72585;
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .book-cover {
            height: 220px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: bold;
            font-size: 2.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .book-cover.gatsby {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
        }

        .book-cover.mockingbird {
            background: linear-gradient(135deg, #a1c4fd, #c2e9fb);
        }

        .book-cover.1984 {
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
        }

        .book-cover.pride {
            background: linear-gradient(135deg, #d4fc79, #96e6a1);
        }

        .book-cover.hobbit {
            background: linear-gradient(135deg, #84fab0, #8fd3f4);
        }

        .book-cover.davinci {
            background: linear-gradient(135deg, #a6c0fe, #f68084);
        }

        .rating-stars {
            color: #ffc107;
        }

        .bestseller-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--warning);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .book-card {
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            position: relative;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            font-weight: bold;
        }

        .sidebar {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        /* image slider css code start  */
        /* Custom styles for the slider */
        /* Custom styling for the slider */
        .carousel {
            /* margin: 0 auto; */
            /* max-width: 900px; */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
            height: 400px;
        }
        
        .carousel-item img {
            width: 100%;
            object-fit: cover;
            height: 400px;
        }
        
        .carousel-caption {
            background-color: rgba(54, 58, 245, 0.85);
            border-radius: 10px;
            padding: 20px;
            bottom: 20px;
        }
        
        .controls-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 15px;
        }
        
        .control-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        
        #playPauseBtn {
            background-color: #28a745;
            color: white;
        }
        
        #prevBtn, #nextBtn {
            background-color: #007bff;
            color: white;
        }
        
        @media (max-width: 768px) {
            .carousel-item img {
                height: 300px;
            }
            
            .carousel-caption {
                padding: 10px;
                bottom: 10px;
            }
            
            .carousel-caption h5 {
                font-size: 1rem;
            }
            
            .carousel-caption p {
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
            }
        }
        /* image slider css code end  */
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-gradient shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-book me-2"></i>BookWgit </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Bestsellers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">New Releases</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($genres as $genre): ?>
                                <li><a class="dropdown-item" href="#"><?= $genre ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
                <div class="d-flex">
                    <form class="d-flex me-2">
                        <input class="form-control me-2" type="search" placeholder="Search books...">
                        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                    <div class="dropdown">
                        <a class="btn btn-light position-relative" href="#" role="button" id="cartDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-cart3"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= count($cart_items) ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                            <li class="dropdown-header fw-bold">Your Cart</li>
                            <?php if (empty($cart_items)): ?>
                                <li class="px-3 py-2">Your cart is empty</li>
                            <?php else: ?>
                                <?php foreach ($cart_items as $item): ?>
                                    <li class="dropdown-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?= $item['book']['title'] ?></strong>
                                                <div class="text-muted">Qty: <?= $item['quantity'] ?></div>
                                            </div>
                                            <div class="text-end">
                                                $<?= number_format($item['total'], 2) ?>
                                                <div>
                                                    <a href="?action=remove_from_cart&id=<?= $item['book']['id'] ?>" class="text-danger small"><i class="bi bi-trash"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                                <li class="dropdown-divider"></li>
                                <li class="dropdown-item d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong>$<?= number_format($cart_total, 2) ?></strong>
                                </li>
                                <li class="dropdown-item">
                                    <a href="?view=cart" class="btn btn-primary w-100">View Cart</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="dropdown ms-2">
                        <a class="btn btn-light" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">Welcome, <?= $_SESSION['user']['name'] ?></li>
                            <!-- <li><a class="dropdown-item" href="newcontact/login.html"><i class="bi bi-person me-2"></i>My Profile</a></li>  -->
                            <li><a class="dropdown-item" href="?view=purchases"><i class="bi bi-receipt me-2"></i>My Purchases</a></li>
                            <li><a class="dropdown-item" href="newcontact/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- image silder code start  -->
    <div class=" mt-2">

        <!-- Image Slider -->
        <div id="imageSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#imageSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#imageSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#imageSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="image/11.jpg" class="" alt="Slide 1">
                    <div class="carousel-caption bg-primary d-none d-md-block">
                        <h5>Beautiful Landscape</h5>
                        <p>Explore the wonders of nature with our first slide.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="image/22.jpg" class="" alt="Slide 2">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>City Skyline</h5>
                        <p>Discover the beauty of modern architecture.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="image/33.jpg" class="d-block w-100" alt="Slide 3">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Ocean View</h5>
                        <p>Relax with breathtaking ocean views.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#imageSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#imageSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Slider Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carouselElement = document.getElementById('imageSlider');
            const carousel = new bootstrap.Carousel(carouselElement, {
                interval: 3000, // 3 seconds auto-rotation
                ride: 'carousel',
                wrap: true
            });

            // Control buttons
            const playPauseBtn = document.getElementById('playPauseBtn');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            let isPlaying = true;

            // Toggle play/pause
            playPauseBtn.addEventListener('click', function() {
                if (isPlaying) {
                    carousel.pause();
                    playPauseBtn.textContent = 'Play';
                    playPauseBtn.style.backgroundColor = '#dc3545';
                } else {
                    carousel.cycle();
                    playPauseBtn.textContent = 'Pause';
                    playPauseBtn.style.backgroundColor = '#28a745';
                }
                isPlaying = !isPlaying;
            });

            // Previous button
            prevBtn.addEventListener('click', function() {
                carousel.prev();
                // If paused and user manually navigates, keep it paused
                if (!isPlaying) {
                    carousel.pause();
                }
            });

            // Next button
            nextBtn.addEventListener('click', function() {
                carousel.next();
                // If paused and user manually navigates, keep it paused
                if (!isPlaying) {
                    carousel.pause();
                }
            });

            // Pause on hover
            carouselElement.addEventListener('mouseenter', function() {
                if (isPlaying) {
                    carousel.pause();
                }
            });

            carouselElement.addEventListener('mouseleave', function() {
                if (isPlaying) {
                    carousel.cycle();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    carousel.prev();
                    if (!isPlaying) carousel.pause();
                } else if (e.key === 'ArrowRight') {
                    carousel.next();
                    if (!isPlaying) carousel.pause();
                } else if (e.key === ' ') {
                    // Spacebar toggles play/pause
                    playPauseBtn.click();
                    e.preventDefault(); // Prevent page scrolling
                }
            });

            // Touch/swipe support for mobile
            let touchStartX = 0;
            let touchEndX = 0;

            carouselElement.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].clientX;
            }, {
                passive: true
            });

            carouselElement.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].clientX;
                handleSwipe();
            }, {
                passive: true
            });

            function handleSwipe() {
                const threshold = 50; // Minimum swipe distance

                if (touchEndX < touchStartX - threshold) {
                    // Swipe left - next slide
                    carousel.next();
                    if (!isPlaying) carousel.pause();
                } else if (touchEndX > touchStartX + threshold) {
                    // Swipe right - previous slide
                    carousel.prev();
                    if (!isPlaying) carousel.pause();
                }
            }
        });
    </script>
    <!-- image slider code end  -->

    <div class="container py-4 mt-5">
        <?php if (!isset($_GET['view']) || $_GET['view'] == 'home'): ?>
            <!-- Hero Section -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-3">Discover Your Next Favorite Book</h1>
                    <p class="lead mb-4">Explore our vast collection of books across all genres. From bestsellers to hidden gems, find the perfect read for every mood.</p>
                    <a href="#featured" class="btn btn-primary btn-lg me-2">Browse Books</a>
                    <a href="#bestsellers" class="btn btn-outline-primary btn-lg">See Bestsellers</a>
                </div>
                <div class="col-md-6">
                    <div class="position-relative">
                        <div class="book-cover gatsby">G</div>
                        <div class="position-absolute top-0 end-0">
                            <div class="book-cover hobbit" style="width: 120px; height: 160px; transform: rotate(10deg);">H</div>
                        </div>
                        <div class="position-absolute bottom-0 start-0">
                            <div class="book-cover mockingbird" style="width: 100px; height: 140px; transform: rotate(-8deg);">M</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="row mb-5">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Why Choose BookWorm?</h2>
                    <p class="text-muted">We provide the best reading experience for book lovers</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h5>Free Shipping</h5>
                    <p class="text-muted">Free delivery on orders over $25</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Secure Payment</h5>
                    <p class="text-muted">100% secure payment options</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <h5>Easy Returns</h5>
                    <p class="text-muted">30-day hassle-free returns</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-download"></i>
                    </div>
                    <h5>Instant E-Books</h5>
                    <p class="text-muted">Download immediately after purchase</p>
                </div>
            </div>

            <!-- Bestsellers -->
            <div id="bestsellers" class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Bestsellers</h2>
                    <a href="#" class="btn btn-outline-primary">View All</a>
                </div>
                <div class="row g-4">
                    <?php foreach ($books as $book): ?>
                        <?php if ($book['bestseller']): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card h-100 book-card">
                                    <?php if ($book['bestseller']): ?>
                                        <div class="bestseller-badge">Bestseller</div>
                                    <?php endif; ?>
                                    <div class="book-cover <?= $book['cover'] ?>"><?= substr($book['title'], 0, 1) ?></div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $book['title'] ?></h5>
                                        <p class="card-text text-muted"><?= $book['author'] ?></p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="rating-stars">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <i class="bi bi-star<?= $i < floor($book['rating']) ? '-fill' : ($i < $book['rating'] ? '-half' : '') ?>"></i>
                                                <?php endfor; ?>
                                                <span class="ms-1">(<?= $book['reviews'] ?>)</span>
                                            </div>
                                            <span class="badge bg-light text-dark"><?= $book['genre'] ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="mb-0">$<?= $book['price'] ?></h4>
                                            <a href="?action=add_to_cart&id=<?= $book['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- All Books -->
            <div id="featured" class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Browse Books</h2>
                    <div class="d-flex">
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Sort by
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Newest First</a></li>
                                <li><a class="dropdown-item" href="#">Price: Low to High</a></li>
                                <li><a class="dropdown-item" href="#">Price: High to Low</a></li>
                                <li><a class="dropdown-item" href="#">Top Rated</a></li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">All Genres</a></li>
                                <?php foreach ($genres as $genre): ?>
                                    <li><a class="dropdown-item" href="#"><?= $genre ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <?php foreach ($books as $book): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card h-100 book-card">
                                <div class="book-cover <?= $book['cover'] ?>"><?= substr($book['title'], 0, 1) ?></div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $book['title'] ?></h5>
                                    <p class="card-text text-muted"><?= $book['author'] ?></p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="rating-stars">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <i class="bi bi-star<?= $i < floor($book['rating']) ? '-fill' : ($i < $book['rating'] ? '-half' : '') ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ms-1">(<?= $book['reviews'] ?>)</span>
                                        </div>
                                        <span class="badge bg-light text-dark"><?= $book['genre'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0">$<?= $book['price'] ?></h4>
                                        <a href="?action=add_to_cart&id=<?= $book['id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recommendations -->
            <?php if (!empty($recommendations)): ?>
                <div class="mb-5">
                    <h2 class="fw-bold mb-4">Recommended For You</h2>
                    <div class="row g-4">
                        <?php foreach ($recommendations as $book): ?>
                            <div class="col-md-4">
                                <div class="card h-100 book-card">
                                    <div class="book-cover <?= $book['cover'] ?>"><?= substr($book['title'], 0, 1) ?></div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $book['title'] ?></h5>
                                        <p class="card-text text-muted"><?= $book['author'] ?></p>
                                        <p class="card-text"><?= substr($book['description'], 0, 100) ?>...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="mb-0">$<?= $book['price'] ?></h4>
                                            <a href="?action=add_to_cart&id=<?= $book['id'] ?>" class="btn btn-primary">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif ($_GET['view'] == 'cart'): ?>
            <!-- Cart Page -->
            <div class="row">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-4">Your Shopping Cart</h2>

                    <?php if (empty($cart_items)): ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                                <h3>Your cart is empty</h3>
                                <p class="text-muted">Looks like you haven't added any books to your cart yet</p>
                                <a href="?" class="btn btn-primary mt-3">Browse Books</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="post" action="?action=update_cart">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <?php foreach ($cart_items as $item): ?>
                                        <div class="cart-item">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="book-cover <?= $item['book']['cover'] ?> small-cover" style="height: 100px;"><?= substr($item['book']['title'], 0, 1) ?></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5><?= $item['book']['title'] ?></h5>
                                                    <p class="text-muted mb-1"><?= $item['book']['author'] ?></p>
                                                    <div class="rating-stars">
                                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                                            <i class="bi bi-star<?= $i < floor($item['book']['rating']) ? '-fill' : ($i < $item['book']['rating'] ? '-half' : '') ?>"></i>
                                                        <?php endfor; ?>
                                                        <span class="ms-1">(<?= $item['book']['reviews'] ?>)</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        <input type="number" name="quantity[<?= $item['book']['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control">
                                                        <button class="btn btn-outline-secondary" type="submit">Update</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <h5>$<?= number_format($item['total'], 2) ?></h5>
                                                    <a href="?action=remove_from_cart&id=<?= $item['book']['id'] ?>" class="text-danger"><i class="bi bi-trash"></i> Remove</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </form>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5>Subtotal</h5>
                                        <p class="text-muted">Shipping and taxes calculated at checkout</p>
                                    </div>
                                    <h3>$<?= number_format($cart_total, 2) ?></h3>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <a href="?action=checkout" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                                    <a href="?" class="btn btn-outline-primary">Continue Shopping</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <div class="sidebar">
                        <h5 class="mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>$<?= number_format($cart_total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax</span>
                            <span>$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold mb-4">
                            <span>Total</span>
                            <span>$<?= number_format($cart_total, 2) ?></span>
                        </div>

                        <?php if (!empty($recommendations)): ?>
                            <h5 class="mb-3">You May Also Like</h5>
                            <?php foreach ($recommendations as $book): ?>
                                <div class="d-flex mb-3">
                                    <div class="book-cover <?= $book['cover'] ?> me-3" style="width: 60px; height: 80px;"><?= substr($book['title'], 0, 1) ?></div>
                                    <div>
                                        <div class="fw-bold"><?= $book['title'] ?></div>
                                        <div class="text-muted small"><?= $book['author'] ?></div>
                                        <div class="fw-bold">$<?= $book['price'] ?></div>
                                        <a href="?action=add_to_cart&id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-primary mt-1">Add</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($_GET['view'] == 'purchases'): ?>
            <!-- Purchase History -->
            <div class="row">
                <div class="col-md-12">
                    <h2 class="fw-bold mb-4">Purchase History</h2>

                    <?php if (empty($purchases)): ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-receipt-cutoff display-1 text-muted mb-4"></i>
                                <h3>No purchases yet</h3>
                                <p class="text-muted">Your purchase history will appear here</p>
                                <a href="?" class="btn btn-primary mt-3">Browse Books</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($purchases as $index => $purchase): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between">
                                    <div>
                                        <strong>Order #<?= count($purchases) - $index ?></strong>
                                        <span class="ms-3"><?= $purchase['date'] ?></span>
                                    </div>
                                    <div>
                                        <span class="badge bg-success">Completed</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($purchase['items'] as $book_id => $quantity): ?>
                                        <?php $book = getBookById($book_id); ?>
                                        <?php if ($book): ?>
                                            <div class="cart-item">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="book-cover <?= $book['cover'] ?> small-cover" style="height: 60px;"><?= substr($book['title'], 0, 1) ?></div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <h6><?= $book['title'] ?></h6>
                                                        <p class="text-muted small mb-0"><?= $book['author'] ?></p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="text-muted">Qty: <?= $quantity ?></div>
                                                    </div>
                                                    <div class="col-md-3 text-end">
                                                        <div>$<?= number_format($book['price'] * $quantity, 2) ?></div>
                                                        <a href="#" class="small"><i class="bi bi-download"></i> Download E-Book</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <div class="card-footer bg-white d-flex justify-content-between">
                                    <div></div>
                                    <div class="fw-bold">
                                        Total: $<?= number_format($purchase['total'], 2) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<!-- adeel  -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3"><i class="bi bi-book me-2"></i>BookWorm</h5>
                    <p>Your onstop destination for all your reading needs. Discover new worlds between the pages.</p>
                    <div class="d-flex mt-4">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Bestsellers</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">New Releases</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Categories</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Customer Service</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Shipping Policy</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Returns & Refunds</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-3">Newsletter</h5>
                    <p>Subscribe to get special offers and updates</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center pt-3">
                <p>&copy; 2023 BookWorm. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js">
        alert('Wellcome your own page')
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>