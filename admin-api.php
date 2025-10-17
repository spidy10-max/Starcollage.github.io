<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

class Database {
    private $host = 'localhost';
    private $db_name = 'star_college_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

class AdminAPI {
    private $conn;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    // Admin authentication
    public function login($username, $password) {
        $query = "SELECT * FROM admin_users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $user['password_hash'])) {
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'full_name' => $user['full_name'],
                        'role' => $user['role']
                    ]
                ];
            }
        }
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    // Track visitor
    public function trackVisitor($data) {
        $query = "INSERT INTO visitors SET 
                  ip_address = :ip_address,
                  user_agent = :user_agent,
                  page_visited = :page_visited,
                  referrer = :referrer";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    // Get analytics data
    public function getAnalytics() {
        // Total visitors
        $query = "SELECT COUNT(*) as total_visitors FROM visitors";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $totalVisitors = $stmt->fetch(PDO::FETCH_ASSOC);

        // Today's visitors
        $query = "SELECT COUNT(*) as today_visitors FROM visitors 
                  WHERE DATE(visit_time) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $todayVisitors = $stmt->fetch(PDO::FETCH_ASSOC);

        // Total applications
        $query = "SELECT COUNT(*) as total_applications FROM applications";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $totalApplications = $stmt->fetch(PDO::FETCH_ASSOC);

        // Recent visitors
        $query = "SELECT * FROM visitors ORDER BY visit_time DESC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $recentVisitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total_visitors' => $totalVisitors['total_visitors'],
            'today_visitors' => $todayVisitors['today_visitors'],
            'total_applications' => $totalApplications['total_applications'],
            'recent_visitors' => $recentVisitors
        ];
    }

    // Save application
    public function saveApplication($data) {
        $query = "INSERT INTO applications SET 
                  name = :name,
                  email = :email,
                  phone = :phone,
                  gender = :gender,
                  program = :program,
                  transport = :transport,
                  remarks = :remarks";
        
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute($data);
        
        return ['success' => $result, 'id' => $this->conn->lastInsertId()];
    }

    // Get applications
    public function getApplications() {
        $query = "SELECT * FROM applications ORDER BY application_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update website content
    public function updateContent($key, $content_en, $content_ur) {
        $query = "INSERT INTO website_content (content_key, content_en, content_ur) 
                  VALUES (:key, :content_en, :content_ur)
                  ON DUPLICATE KEY UPDATE 
                  content_en = :content_en, 
                  content_ur = :content_ur,
                  last_updated = CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':key' => $key,
            ':content_en' => $content_en,
            ':content_ur' => $content_ur
        ]);
    }

    // Get all website content
    public function getWebsiteContent() {
        $query = "SELECT * FROM website_content";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $content = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach($content as $item) {
            $result[$item['content_key']] = $item;
        }
        return $result;
    }

    // Update contact info
    public function updateContactInfo($data) {
        $query = "UPDATE contact_info SET 
                  phone1 = :phone1,
                  phone2 = :phone2,
                  address_en = :address_en,
                  address_ur = :address_ur,
                  email = :email";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    // Get contact info
    public function getContactInfo() {
        $query = "SELECT * FROM contact_info LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Handle API requests
$api = new AdminAPI();
$method = $_SERVER['REQUEST_METHOD'];
$request = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'POST':
        if(isset($_GET['action'])) {
            switch($_GET['action']) {
                case 'login':
                    echo json_encode($api->login($request['username'], $request['password']));
                    break;
                case 'track_visitor':
                    echo json_encode($api->trackVisitor($request));
                    break;
                case 'save_application':
                    echo json_encode($api->saveApplication($request));
                    break;
                case 'update_content':
                    echo json_encode($api->updateContent($request['key'], $request['content_en'], $request['content_ur']));
                    break;
                case 'update_contact':
                    echo json_encode($api->updateContactInfo($request));
                    break;
            }
        }
        break;
    
    case 'GET':
        if(isset($_GET['action'])) {
            switch($_GET['action']) {
                case 'analytics':
                    echo json_encode($api->getAnalytics());
                    break;
                case 'applications':
                    echo json_encode($api->getApplications());
                    break;
                case 'website_content':
                    echo json_encode($api->getWebsiteContent());
                    break;
                case 'contact_info':
                    echo json_encode($api->getContactInfo());
                    break;
            }
        }
        break;
}
?>