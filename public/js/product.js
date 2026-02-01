$(document).ready(function() {
    const form = $('#productForm');
    const submitBtn = $('#submitBtn');
    const productsTable = $('#productsTable');
    const totalSumElement = $('#totalSum');

    form.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        
        if (!validateForm()) {
            return;
        }
        
        const formData = $(this).serialize();
        const productId = $('#productId').val();
        const method = productId ? 'PUT' : 'POST';
        const url = productId ? `/products/${productId}` : '/products';
        
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (productId) {
                        updateProductRow(response.product);
                    } else {
                        addProductRow(response.product);
                    }
                    updateTotalSum(response.totalSum);
                    resetForm();
                    showAlert('Product saved successfully!', 'success');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    displayErrors(xhr.responseJSON.errors);
                } else {
                    showAlert('An error occurred. Please try again.', 'danger');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });

    productsTable.on('click', '.edit-btn', function() {
        const productId = $(this).data('id');
        
        $.ajax({
            url: `/products/${productId}`,
            type: 'GET',
            success: function(response) {
                console.log(response.product)
                let product = response.product
                $('#productId').val(product.id);
                $('#product_name').val(product.product_name);
                $('#quantity').val(product.quantity);
                $('#price').val(product.price);
                submitBtn.html('<i class="bi bi-check-circle"></i> Update Product');
                submitBtn.removeClass('btn-success').addClass('btn-primary');
                $('html, body').animate({
                    scrollTop: form.offset().top
                }, 500);
            }
        });
    });

    productsTable.on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this product?')) {
            const productId = $(this).data('id');
            const row = $(`#product-${productId}`);
            
            $.ajax({
                url: `/products/${productId}`,
                type: 'DELETE',
                data: {_token: $('input[name="_token"]').val()},
                success: function(response) {
                    if (response.success) {
                        row.remove();
                        updateTotalSum(response.totalSum);
                        showAlert('Product deleted successfully!', 'success');
                    }
                },
                error: function() {
                    showAlert('Error deleting product', 'danger');
                }
            });
        }
    });

    function addProductRow(product) {
        const row = `
            <tr id="product-${product.id}">
                <td>${product.product_name}</td>
                <td>${product.quantity}</td>
                <td>$${parseFloat(product.price).toFixed(2)}</td>
                <td>${product.datetime_submitted}</td>
                <td>$${parseFloat(product.total_value).toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="${product.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${product.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        productsTable.prepend(row);
    }

    function updateProductRow(product) {
        const row = $(`#product-${product.id}`);
        row.html(`
            <td>${product.product_name}</td>
            <td>${product.quantity}</td>
            <td>$${parseFloat(product.price).toFixed(2)}</td>
            <td>${product.datetime_submitted}</td>
            <td>$${parseFloat(product.total_value).toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="${product.id}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${product.id}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `);
    }

    function updateTotalSum(totalSum) {
        totalSumElement.text('$' + parseFloat(totalSum).toFixed(2));
    }

    function resetForm() {
        form[0].reset();
        $('#productId').val('');
        submitBtn.html('<i class="bi bi-plus-circle"></i> Add Product');
        submitBtn.removeClass('btn-primary').addClass('btn-success');
    }

    function validateForm() {
        let isValid = true;
        
        const productName = $('#product_name').val().trim();
        const quantity = $('#quantity').val();
        const price = $('#price').val();
        
        if (!productName) {
            showFieldError('product_name', 'Product name is required');
            isValid = false;
        }
        
        if (!quantity || quantity < 0) {
            showFieldError('quantity', 'Valid quantity is required');
            isValid = false;
        }
        
        if (!price || price < 0) {
            showFieldError('price', 'Valid price is required');
            isValid = false;
        }
        
        return isValid;
    }

    function showFieldError(field, message) {
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}`).next('.invalid-feedback').text(message);
    }

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function displayErrors(errors) {
        clearErrors();
        for (const field in errors) {
            if (errors.hasOwnProperty(field)) {
                showFieldError(field.replace('.', '_'), errors[field][0]);
            }
        }
    }

    function showAlert(message, type) {
        const alert = $(`
            <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        setTimeout(() => {
            alert.alert('close');
        }, 3000);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});