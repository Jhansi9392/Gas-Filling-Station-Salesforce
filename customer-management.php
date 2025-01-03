<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Handle customer search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = '';
if ($search) {
    $search = $conn->real_escape_string($search);
    $where_clause = "WHERE first_name LIKE '%$search%' 
                     OR last_name LIKE '%$search%' 
                     OR email LIKE '%$search%'
                     OR phone LIKE '%$search%'";
}

// Get customers with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$query = "SELECT * FROM customers $where_clause 
          ORDER BY created_at DESC LIMIT $offset, $per_page";
$result = $conn->query($query);

// Get total customers for pagination
$total_query = "SELECT COUNT(*) as count FROM customers $where_clause";
$total_result = $conn->query($total_query);
$total_customers = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_customers / $per_page);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../inventory/">
                            Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="../customers/">
                            Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../sales/">
                            Sales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../reports/">
                            Reports
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Customer Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                        Add New Customer
                    </button>
                </div>
            </div>

            <!-- Search form -->
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search customers..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>

            <!-- Customers table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Loyalty Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($customer = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                            <td><?php echo $customer['loyalty_points']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editCustomer(<?php echo $customer['id']; ?>)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCustomer(<?php echo $customer['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                