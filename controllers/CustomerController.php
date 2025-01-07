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

    public function update($id, $data) {
        if ($this->model->update($id, $data['username'], $data['password'], $data['name'], $data['phone'], $data['gender'], $data['address'], $data['role'], $data['email'])) {
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
