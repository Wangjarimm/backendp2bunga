<?php
class Service
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new service
    public function create($name, $price, $description, $photo)
    {
        $stmt = $this->pdo->prepare('INSERT INTO services (nama_service, harga, deskripsi, foto) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$name, $price, $description, $photo]);
    }

    // Read service by ID
    public function read($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update service information
    public function update($id, $name, $price, $description, $photo)
    {
        $stmt = $this->pdo->prepare('UPDATE services SET nama_service = ?, harga = ?, deskripsi = ?, foto = ? WHERE id = ?');
        return $stmt->execute([$name, $price, $description, $photo, $id]);
    }

    // Delete service by ID
    public function delete($id) {
        $pdo = $this->pdo;
    
        try {
            $pdo->beginTransaction();
    
            // Hapus data terkait di booking_services
            $stmt = $pdo->prepare("DELETE FROM booking_services WHERE service_id = ?");
            $stmt->execute([$id]);
    
            // Baru hapus service
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
    
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error deleting service: " . $e->getMessage());
            return false;
        }
    }
    

    public function getMostOrderedServices()
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            s.id AS id,
            s.nama_service,
            s.harga,
            s.deskripsi,
            s.foto,
            COUNT(bs.service_id) AS total_ordered
        FROM 
            services s
        LEFT JOIN 
            booking_services bs ON bs.service_id = s.id
        GROUP BY 
            s.id
        ORDER BY 
            total_ordered DESC
    ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            error_log("Tidak ada data layanan ditemukan.");
        } else {
            error_log("Hasil query: " . print_r($result, true));  // Menampilkan hasil query yang lebih terperinci
        }

        return $result;
    }




    // List all services
    public function listAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM services');
        return $stmt->fetchAll();
    }
}
