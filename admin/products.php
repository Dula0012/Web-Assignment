<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $imageName = $_POST['image'];
        
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
            $uploadDir = '../assets/images/products/';
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $fileType = $_FILES['product_image']['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                $fileExtension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid() . '_' . time() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $imageName;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadPath)) {
                    $message = 'Image uploaded successfully! ';
                } else {
                    $error = 'Failed to upload image. ';
                }
            } else {
                $error = 'Invalid file type. Only JPG, PNG, and GIF allowed. ';
            }
        }
        
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image, stock, brand) VALUES (:name, :description, :price, :category_id, :image, :stock, :brand)");
            $stmt->execute([
                ':name' => $_POST['name'],
                ':description' => $_POST['description'],
                ':price' => $_POST['price'],
                ':category_id' => $_POST['category_id'],
                ':image' => $imageName,
                ':stock' => $_POST['stock'],
                ':brand' => $_POST['brand']
            ]);
            $message .= 'Product added successfully!';
        } elseif ($_POST['action'] === 'update') {
            $stmt = $pdo->prepare("UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id, image = :image, stock = :stock, brand = :brand WHERE id = :id");
            $stmt->execute([
                ':name' => $_POST['name'],
                ':description' => $_POST['description'],
                ':price' => $_POST['price'],
                ':category_id' => $_POST['category_id'],
                ':image' => $imageName,
                ':stock' => $_POST['stock'],
                ':brand' => $_POST['brand'],
                ':id' => $_POST['id']
            ]);
            $message .= 'Product updated successfully!';
        }
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $message = 'Product deleted successfully!';
    header('Location: products.php');
    exit;
}

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editProduct = $stmt->fetch();
}
?>
<?php include 'includes/header.php'; ?>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-content">
    <div class="content-header">
        <h2><?php echo $editProduct ? 'Edit Produ enctype="multipart/form-data"ct' : 'Add New Product'; ?></h2>
    </div>
    
    <div class="form-container">
        <form method="POST" action="products.php">
            <input type="hidden" name="action" value="<?php echo $editProduct ? 'update' : 'add'; ?>">
            <?php if ($editProduct): ?>
                <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo $editProduct ? $editProduct['name'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="brand">Brand *</label>
                    <input type="text" id="brand" name="brand" value="<?php echo $editProduct ? $editProduct['brand'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo $editProduct ? $editProduct['description'] : ''; ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (Rs.) *</label>
                    <input type="number" step="0.01" id="price" name="price" value="<?php echo $editProduct ? $editProduct['price'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $editProduct ? $editProduct['stock'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($editProduct && $editProduct['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Image Filename (Optional)</label>
                    <input type="text" id="image" name="image" value="<?php echo $editProduct ? $editProduct['image'] : ''; ?>" placeholder="e.g., product.jpg">
                    <small style="color: #666;">Leave empty if uploading a new image</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="product_image">Upload Product Image</label>
                <input type="file" id="product_image" name="product_image" accept="image/jpeg,image/jpg,image/png,image/gif" class="file-input">
                <small style="color: #666;">Accepted formats: JPG, PNG, GIF (Max 5MB)</small>
                <?php if ($editProduct && $editProduct['image']): ?>
                    <div class="current-image">
                        <p style="margin-top: 10px;">Current Image:</p>
                        <img src="../assets/images/products/<?php echo $editProduct['image']; ?>" alt="Current product image" style="max-width: 150px; border-radius: 5px; margin-top: 5px;">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
                <?php if ($editProduct): ?>
                    <a href="products.php" class="btn-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="content-header">
        <h2>All Products</h2>
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><img src="../assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['brand']; ?></td>
                        <td><?php echo $product['category_name']; ?></td>
                        <td>Rs. <?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn-small btn-edit">Edit</a>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
