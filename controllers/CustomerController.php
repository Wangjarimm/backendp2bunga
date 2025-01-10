<?php

class CustomerController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Customer($pdo);
    }

    public function create($data) {
        if ($this->model->create($data['username'], $data['password'], $data['name'], $data['phone'], $data['gender'], $data['address'], $data['role'], $data['email'])) {
            return json_encode(["message" => "Customer successfully added."]);
        } else {
            return json_encode(["message" => "Failed to add customer."]);
        }
    }

    public function read($id) {
        $customer = $this->model->read($id);
        if ($customer) {
            return json_encode($customer);
        } else {
            return json_encode(['message' => 'Customer not found']);
        }
    }
    public function update($id) {
        // Mengambil data JSON dari body request
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);  // Mengurai data JSON
    
        // Debug: Cek data yang diterima
        error_log("Received data: " . print_r($data, true));  // Debugging log
    
        // Pastikan username tidak kosong
        if (empty($data['username'])) {
            return json_encode(['error' => 'Username tidak boleh kosong']);
        }
    
        // Ambil data customer saat ini dari database untuk mendapatkan password dan role lama
        $currentCustomer = $this->model->read($id);  // Mendapatkan data customer berdasarkan ID
    
        // Gunakan password lama jika tidak ada password baru
        $password = !empty($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : $currentCustomer['password'];  // Gunakan password lama jika tidak ada password baru
        $role = !empty($data['role']) ? $data['role'] : $currentCustomer['role'];  // Gunakan role lama jika tidak ada yang baru
    
        // Update data customer ke database
        if ($this->model->update($id, $data['username'], $password, $data['name'], $data['phone'], $data['gender'], $data['address'], $role, $data['email'])) {
            return json_encode(["message" => "Customer successfully updated."]);
        } else {
            return json_encode(["message" => "Failed to update customer."]);
        }
    }
    
    
    
    
    
    

    public function delete($id) {
        if ($this->model->delete($id)) {
            return json_encode(["message" => "Customer successfully deleted."]);
        } else {
            return json_encode(["message" => "Failed to delete customer."]);
        }
    }

    public function listAll() {
        $customers = $this->model->listAll();
        if (count($customers) > 0) {
            return json_encode($customers);
        } else {
            return json_encode(['message' => 'No customers found']);
        }
    }

    public function register($data) {
        if (empty($data['username']) || empty($data['phone']) || empty($data['password'])) {
            return json_encode(["message" => "Username, Phone, or Password cannot be empty."]);
        }
        
        // Pengecekan untuk username dan phone sudah ada
        if ($this->model->checkUsernameExists($data['username'])) {
            return json_encode(["message" => "Username already exists."]);
        }
        
        if ($this->model->checkPhoneExists($data['phone'])) {
            return json_encode(["message" => "Phone number already registered."]);
        }
        
        // Melakukan registrasi
        if ($this->model->register($data['username'], $data['password'], $data['name'], $data['phone'], $data['gender'], $data['address'], $data['email'])) {
            return json_encode(["message" => "Registration successful."]);
        } else {
            return json_encode(["message" => "Registration failed."]);
        }
    }
    

    public function login($data) {
        $user = $this->model->login($data['username'], $data['password']);
        if ($user) {
            return json_encode($user);
        } else {
            return json_encode(['message' => 'Invalid credentials']);
        }
    }
}
