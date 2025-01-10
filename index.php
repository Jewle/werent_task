<?php
$host = 'localhost';
$dbname = 'werent';
$user = 'postgres';
$password = '';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function fetchCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchProducts($pdo) {
    $stmt = $pdo->query("SELECT products.id, products.name, products.price, categories.name AS category_name FROM products JOIN categories ON products.category_id = categories.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchOrders($pdo) {
    $stmt = $pdo->query("SELECT orders.id, products.name AS product_name, orders.quantity, orders.purchase_time FROM orders JOIN products ON orders.product_id = products.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchStatistics($pdo) {
    $stmt = $pdo->query("SELECT statistics.date, categories.name AS category_name, statistics.quantity FROM statistics JOIN categories ON statistics.category_id = categories.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addCategory'])) {
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    }
    if (isset($_POST['deleteCategory'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
    if (isset($_POST['updateCategory'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name]);
    }

    if (isset($_POST['addProduct'])) {
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price) VALUES (:name, :category_id, :price)");
        $stmt->execute(['name' => $name, 'category_id' => $category_id, 'price' => $price]);
    }
    if (isset($_POST['deleteProduct'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
    if (isset($_POST['updateProduct'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stmt = $pdo->prepare("UPDATE products SET name = :name, category_id = :category_id, price = :price WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name, 'category_id' => $category_id, 'price' => $price]);
    }

    if (isset($_POST['addOrder'])) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $stmt = $pdo->prepare("INSERT INTO orders (product_id, quantity) VALUES (:product_id, :quantity)");
        $stmt->execute(['product_id' => $product_id, 'quantity' => $quantity]);
    }
    if (isset($_POST['deleteOrder'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    header("Location: index.php");
    exit;
}

$categories = fetchCategories($pdo);
$products = fetchProducts($pdo);
$orders = fetchOrders($pdo);
$statistics = fetchStatistics($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
        form input, form select, form button {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }
        form input[type="text"], form input[type="number"] {
            width: calc(100% - 150px);
        }
        form button {
            background: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CRUD FOR WERENT</h1>
        <a href = "working_script.php">Working Script</a>

        <h2>Categories</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Category Name" required>
            <button type="submit" name="addCategory">Add Category</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars($category['id']) ?></td>
                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                                <button type="submit" name="updateCategory">Update</button>
                            </form>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                <button type="submit" name="deleteCategory" style="background: #dc3545; color: white;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Products</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Product Name" required>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <button type="submit" name="addProduct">Add Product</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                                <select name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= $product['category_name'] === $category['name'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required>
                                <button type="submit" name="updateProduct">Update</button>
                            </form>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <button type="submit" name="deleteProduct" style="background: #dc3545; color: white;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Orders</h2>
        <form method="POST">
            <select name="product_id" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <button type="submit" name="addOrder">Add Order</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Purchase Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td><?= htmlspecialchars($order['quantity']) ?></td>
                        <td><?= htmlspecialchars($order['purchase_time']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Statistics</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statistics as $stat): ?>
                    <tr>
                        <td><?= htmlspecialchars($stat['date']) ?></td>
                        <td><?= htmlspecialchars($stat['category_name']) ?></td>
                        <td><?= htmlspecialchars($stat['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
