<?php
class ServiceController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Service($pdo);
    }

    public function create($data) {
        // Ambil data dari request body
        $input = json_decode(file_get_contents("php://input"), true);
    
        // Pastikan semua data tersedia sebelum digunakan
        $name = isset($input['name']) ? $input['name'] : null;
        $price = isset($input['price']) ? $input['price'] : null;
        $description = isset($input['description']) ? $input['description'] : null;
        $photo = isset($input['photo']) ? $input['photo'] : null; // URL gambar, bukan file upload
    
        if (!$name || !$price || !$description || !$photo) {
            echo json_encode(["success" => false, "message" => "Incomplete data."]);
            return;
        }
    
        // Simpan data ke database
        $result = $this->model->create($name, $price, $description, $photo);
    
        if ($result) {
            echo json_encode(["success" => true, "message" => "Service successfully added."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add service."]);
        }
    }
    
    
    

    public function read($id) {
        $service = $this->model->read($id);
        if ($service) {
            echo json_encode($service);
        } else {
            echo json_encode(['message' => 'Service not found']);
        }
    }

    public function update($id, $data) {
        if ($this->model->update($id, $data['name'], $data['price'], $data['description'], $data['photo'])) {
            echo json_encode(["message" => "Service successfully updated."]);
        } else {
            echo json_encode(["message" => "Failed to update service."]);
        }
    }
    

    public function delete($id) {
        if ($this->model->delete($id)) {
            echo json_encode(["message" => "Service successfully deleted."]);
        } else {
            echo json_encode(["message" => "Failed to delete service."]);
        }
    }
    

    public function listAll() {
        $services = $this->model->listAll();
        if (count($services) > 0) {
            echo json_encode($services);
        } else {
            echo json_encode(['message' => 'No services found']);
        }
    }
    public function getMostOrderedServices()
    {
        $services = $this->model->getMostOrderedServices();
    
        // Debugging: pastikan hanya data yang diinginkan yang dikirim
        error_log(print_r($services, true));  // Cek hasil sebelum dikirim
    
        if ($services) {
            echo json_encode($services);  // Mengembalikan hanya data yang relevan
        } else {
            echo json_encode(['message' => 'No services found']);
        }
    }
    
    
  
}

?>