<?php

namespace App\Services;

class ProductService
{
    private $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('app/products.json');
        $this->initializeFile();
    }

    private function initializeFile()
    {
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    public function getAllProducts()
    {
        $data = file_get_contents($this->filePath);
        $products = json_decode($data, true) ?: [];
        
        usort($products, function($a, $b) {
            return strtotime($b['datetime_submitted']) - strtotime($a['datetime_submitted']);
        });
        
        return $products;
    }

    public function storeProduct($data)
    {
        $products = $this->getAllProducts();
        $data['id'] = uniqid();
        $data['datetime_submitted'] = date('Y-m-d H:i:s');
        $data['total_value'] = $data['quantity'] * $data['price'];
        
        $products[] = $data;
        $this->saveToFile($products);
        
        return $data;
    }

    public function updateProduct($id, $data)
    {
        $products = $this->getAllProducts();
        
        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product['product_name'] = $data['product_name'];
                $product['quantity'] = $data['quantity'];
                $product['price'] = $data['price'];
                $product['total_value'] = $data['quantity'] * $data['price'];
                break;
            }
        }
        
        $this->saveToFile($products);
        return $this->getProduct($id);
    }

    public function deleteProduct($id)
    {
        $products = $this->getAllProducts();
        $products = array_filter($products, function($product) use ($id) {
            return $product['id'] != $id;
        });
        
        $this->saveToFile(array_values($products));
        return true;
    }

    public function getProduct($id)
    {
        $products = $this->getAllProducts();
        
        foreach ($products as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }
        
        return null;
    }

    private function saveToFile($data)
    {
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getTotalSum()
    {
        $products = $this->getAllProducts();
        $total = 0;
        
        foreach ($products as $product) {
            $total += $product['total_value'];
        }
        
        return $total;
    }
}