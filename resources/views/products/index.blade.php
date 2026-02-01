<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Product Inventory System</h2>
            </div>
            
            <div class="card-body">
                <form id="productForm" class="mb-4">
                    @csrf
                    <input type="hidden" id="productId">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="quantity" class="form-label">Quantity in Stock</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" step="1" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="price" class="form-label">Price per Item ($)</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100" id="submitBtn">
                                <i class="bi bi-plus-circle"></i> Add Product
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity in Stock</th>
                                <th>Price per Item</th>
                                <th>Datetime Submitted</th>
                                <th>Total Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTable">
                            @foreach($products as $product)
                            <tr id="product-{{ $product['id'] }}">
                                <td>{{ $product['product_name'] }}</td>
                                <td>{{ $product['quantity'] }}</td>
                                <td>${{ number_format($product['price'], 2) }}</td>
                                <td>{{ $product['datetime_submitted'] }}</td>
                                <td>${{ number_format($product['total_value'], 2) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $product['id'] }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $product['id'] }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold" id="totalSum">${{ number_format($totalSum, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/product.js') }}"></script>
</body>
</html>