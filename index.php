<?php
session_start();
include('config/config.php');
include('config/checklogin.php');

check_login();

if (isset($_POST['make'])) {
    // Prevent Posting Blank Values
    if (empty($_POST["order_code"]) || empty($_POST["customer_name"]) || empty($_GET['prod_price'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $order_code = $_POST['order_code'];
        $customer_name = $_POST['customer_name'];
        $prod_id = $_GET['prod_id'];
        $prod_name = $_GET['prod_name'];
        $prod_price = $_GET['prod_price'];
        $prod_qty = $_POST['prod_qty'];

        // Get customer ID
        $sql = mysqli_query($con, "SELECT * FROM rpos_customers WHERE customer_name='$customer_name'");
        $cust = mysqli_fetch_assoc($sql);
        $customer_id = $cust['customer_id'];

        // Insert Captured information to orders table
        $postQuery = mysqli_query($con, "INSERT INTO rpos_orders (order_code, customer_id, customer_name, prod_id, prod_name, prod_price, prod_qty) VALUES ('$order_code', '$customer_id', '$customer_name', '$prod_id', '$prod_name', '$prod_price', '$prod_qty')");

        // Add product to cart
        $cartQuery = mysqli_query($con, "INSERT INTO cart (customer_id, prod_id, prod_name, prod_price, prod_qty) VALUES ('$customer_id', '$prod_id', '$prod_name', '$prod_price', '$prod_qty')");

        // Check if both queries were successful
        if ($postQuery && $cartQuery) {
            $success = "Order Submitted";
            header("Location: orders.php"); // Redirect to products page
            exit(); // Make sure to exit after the redirect
        } 
    }
}

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>
    
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>
        
        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>
        
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Please Fill All Fields</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <label>Serveman Name</label>
                                        <select class="form-control" name="customer_name" id="custName" onChange="getCustomer(this.value)">
                                            <option>Select Serveman Name</option>
                                            <?php
                                            // Load All Customers
                                            $ret = mysqli_query($con, "SELECT * FROM rpos_customers");
                                            while ($cust = mysqli_fetch_assoc($ret)) {
                                            ?>
                                                <option value="<?php echo $cust['customer_name'] ?>"><?php echo $cust['customer_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="customer_id" readonly id="customerID" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Order Code</label>
                                        <input type="text" name="order_code" class="form-control" value="">
                                    </div>
                                </div>
                                <hr>
                                <?php
                                $prod_id = $_GET['prod_id'];
                                $ret = mysqli_query($con, "SELECT * FROM rpos_products WHERE prod_id = '$prod_id'");
                                while ($prod = mysqli_fetch_assoc($ret)) {
                                ?>
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label>Product Price (RwFr)</label>
                                            <input type="text" readonly name="prod_price" value="<?php echo number_format($prod['prod_price']) ?> RwFr" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Product Quantity</label>
                                            <input type="text" name="prod_qty" class="form-control" value="">
                                        </div>
                                    </div>
                                <?php } ?>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="submit" name="make" value="Make Order" class="btn btn-success">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
