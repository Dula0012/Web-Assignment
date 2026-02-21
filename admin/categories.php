<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
            $stmt->execute([
                ':name' => $_POST['name'],
                ':description' => $_POST['description']
            ]);
            $message = 'Category added successfully!';
        } elseif ($_POST['action'] === 'update') {
            $stmt = $pdo->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
            $stmt->execute([
                ':name' => $_POST['name'],
                ':description' => $_POST['description'],
                ':id' => $_POST['id']
            ]);
            $message = 'Category updated successfully!';
        }
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $message = 'Category deleted successfully!';
    header('Location: categories.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$editCategory = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editCategory = $stmt->fetch();
}
?>
<?php include 'includes/header.php'; ?>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="admin-content">
    <div class="content-header">
        <h2><?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?></h2>
    </div>
    
    <div class="form-container">
        <form method="POST" action="categories.php">
            <input type="hidden" name="action" value="<?php echo $editCategory ? 'update' : 'add'; ?>">
            <?php if ($editCategory): ?>
                <input type="hidden" name="id" value="<?php echo $editCategory['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" value="<?php echo $editCategory ? $editCategory['name'] : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo $editCategory ? $editCategory['description'] : ''; ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary"><?php echo $editCategory ? 'Update Category' : 'Add Category'; ?></button>
                <?php if ($editCategory): ?>
                    <a href="categories.php" class="btn-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="content-header">
        <h2>All Categories</h2>
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo $category['name']; ?></td>
                        <td><?php echo $category['description']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($category['created_at'])); ?></td>
                        <td>
                            <a href="categories.php?edit=<?php echo $category['id']; ?>" class="btn-small btn-edit">Edit</a>
                            <a href="categories.php?delete=<?php echo $category['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
