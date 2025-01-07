<?php
class Service {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new service
    public function create($name, $price, $description, $photo) {
        $stmt = $this->pdo->prepare('INSERT INTO services (nama_service, harga, deskripsi, foto) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$name, $price, $description, $photo]);
    }

    // Read service by ID
    public function read($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update service information
    public function update($id, $name, $price, $description, $photo) {
        $stmt = $this->pdo->prepare('UPDATE services SET nama_service = ?, harga = ?, deskripsi = ?, foto = ? WHERE id = ?');
        return $stmt->execute([$name, $price, $description, $photo, $id]);
    }

    // Delete service by ID
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM services WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // List all services
    public function listAll() {
        $stmt = $this->pdo->query('SELECT * FROM services');
        return $stmt->fetchAll();
    }
}
?>