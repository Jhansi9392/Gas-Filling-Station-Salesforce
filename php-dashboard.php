<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get fuel inventory status
$fuel_query = "SELECT * FROM fuel_types";
$fuel_result = $conn->query($fuel_query);

// Get today's sales
$sales_query = "SELECT SUM(total_amount) as daily_sales 
                FROM sales 
                WHERE DATE(sale_date) = CURDATE()";
$sales_result = $conn->query($sales_query);
$daily_sales = $sales_result->fetch_assoc()['daily_sales'] ?? 0;

// Get customer count
$customer_query = "SELECT COUNT(*) as total_customers FROM customers";
$customer_result = $conn->query($customer_query);
$total_customers = $customer_result->fetch_assoc()['total_customers'];
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="modules/inventory/">
                            Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="modules/customers/">
                            Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="modules/sales/">
                            Sales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="modules/reports/">
                            Reports
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <!-- Stats cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Daily Sales</h5>
                            <h2 class="card-text">$<?php echo number_format($daily_sales, 2); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Customers</h5>
                            <h2 class="card-text"><?php echo $total_customers; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fuel inventory table -->
            <div class="row mt-4">
                <div class="col-12">
                    <h3>Fuel Inventory Status</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fuel Type</th>
                                <th>Current Stock (L)</th>
                                <th>Price per Liter</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($fuel = $fuel_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fuel['name']); ?></td>
                                <td><?php echo number_format($fuel['current_stock'], 2); ?></td>
                                <td>$<?php echo number_format($fuel['price_per_liter'], 2); ?></td>
                                <td>
                                    <?php if ($fuel['current_stock'] <= $fuel['min_stock_level']): ?>
                                        <span class="badge bg-danger">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Normal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
