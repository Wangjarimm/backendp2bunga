<?php
class Customer
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new user
    public function create($username, $password, $name, $phone, $gender, $address, $role, $email)
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password, name, phone, gender, address, role, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$username, $password, $name, $phone, $gender, $address, $role, $email]);
    }

    // Read user by ID
    public function read($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update user information
    public function update($id, $username, $password, $name, $phone, $gender, $address, $role, $email) {
        // Ambil data customer saat ini dari database untuk mendapatkan password dan role lama
        $currentCustomer = $this->read($id);  // Mendapatkan data customer berdasarkan ID
    
        // Jika password baru diberikan, enkripsi password
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
        } else {
            // Jika tidak ada password baru, gunakan password lama
            $password = $currentCustomer['password'];
        }
    
        // Jika role baru diberikan, gunakan role baru, jika tidak gunakan role lama
        if (empty($role)) {
            $role = $currentCustomer['role'];  // Gunakan role lama jika role baru tidak diberikan
        }
    
        // Query untuk memperbarui data customer
        $sql = "UPDATE users SET username = ?, name = ?, phone = ?, gender = ?, address = ?, email = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$username, $name, $phone, $gender, $address, $email, $password, $role, $id]);
    }
    
    
    

    // Delete user by ID
    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // List all users
    public function listAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM users');
        return $stmt->fetchAll();
    }

    // Register a new user
    public function register($username, $password, $name, $phone, $gender, $address, $email) {
        if (!$username || !$password || !$name || !$phone || !$gender || !$address || !$email) {
            return false; // Return false jika ada data yang kosong
        }
    
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Lakukan query insert
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, name, phone, gender, address, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $hashedPassword, $name, $phone, $gender, $address, $email]);
    }
    

    // Login user
    public function login($username, $password)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function checkUsernameExists($username) {
        $query = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }
    
    public function checkPhoneExists($phone) {
        $query = "SELECT COUNT(*) FROM users WHERE phone = :phone";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }
    
}
