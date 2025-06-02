<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "ql_cosothuctap"; // Adjust to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get action from GET or POST
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

error_log("Action received: $action"); // Debug: Log the action

switch ($action) {
    case 'get_users':
    case 'search_users':
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15; // Default to 15 records per page
        $offset = ($page - 1) * $limit;
        $users = [];
        $totalUsers = 0;

        // Search condition
        $searchCondition = $keyword ? " WHERE %s LIKE ?" : "";
        $searchTerm = $keyword ? "%$keyword%" : null;

        // Count total users for pagination
        $countQueries = [
            'admin' => "SELECT COUNT(*) FROM admin" . ($searchCondition ? sprintf($searchCondition, 'email') : ''),
            'giang_vien' => "SELECT COUNT(*) FROM giang_vien" . ($searchCondition ? sprintf($searchCondition, 'ho_ten') : ''),
            'sinh_vien' => "SELECT COUNT(*) FROM sinh_vien" . ($searchCondition ? sprintf($searchCondition, 'ho_ten') : ''),
            'co_so_thuc_tap' => "SELECT COUNT(*) FROM co_so_thuc_tap" . ($searchCondition ? sprintf($searchCondition, 'ten_co_so') : '')
        ];

        foreach ($countQueries as $table => $query) {
            $stmt = $conn->prepare($query);
            if ($searchCondition && $keyword) {
                $stmt->bind_param("s", $searchTerm);
            }
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $totalUsers += $count;
            $stmt->close();
        }

        $totalPages = ceil($totalUsers / $limit);

        // Query admin
        $query = "SELECT id, email AS name, email, role, 'admin' AS `table` FROM admin" . $searchCondition;
        $query .= " LIMIT ? OFFSET ?";
        $stmt = $conn->prepare(sprintf($query, 'email'));
        if ($searchCondition && $keyword) {
            $stmt->bind_param("sii", $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();

        // Query giang_vien
        $query = "SELECT stt_gv AS id, ho_ten AS name, email, role, 'giang_vien' AS `table` FROM giang_vien" . $searchCondition;
        $query .= " LIMIT ? OFFSET ?";
        $stmt = $conn->prepare(sprintf($query, 'ho_ten'));
        if ($searchCondition && $keyword) {
            $stmt->bind_param("sii", $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();

        // Query sinh_vien
        $query = "SELECT stt_sv AS id, ho_ten AS name, email, role, 'sinh_vien' AS `table` FROM sinh_vien" . $searchCondition;
        $query .= " LIMIT ? OFFSET ?";
        $stmt = $conn->prepare(sprintf($query, 'ho_ten'));
        if ($searchCondition && $keyword) {
            $stmt->bind_param("sii", $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();

        // Query co_so_thuc_tap
        $query = "SELECT stt_cstt AS id, ten_co_so AS name, email, role, 'co_so_thuc_tap' AS `table` FROM co_so_thuc_tap" . $searchCondition;
        $query .= " LIMIT ? OFFSET ?";
        $stmt = $conn->prepare(sprintf($query, 'ten_co_so'));
        if ($searchCondition && $keyword) {
            $stmt->bind_param("sii", $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();

        // Sort users by name to ensure consistent display
        usort($users, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Slice the users array to respect the limit and offset
        $users = array_slice($users, 0, $limit);

        error_log("Users fetched: " . json_encode($users)); // Debug: Log the users
        echo json_encode([
            'success' => true,
            'users' => $users,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalUsers' => $totalUsers
        ]);
        break;

    case 'update_role':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $new_role = isset($_POST['new_role']) ? trim($_POST['new_role']) : '';
        $current_table = isset($_POST['table']) ? trim($_POST['table']) : '';

        $valid_tables = ['admin', 'sinh_vien', 'giang_vien', 'co_so_thuc_tap'];
        if (!$id || !in_array($new_role, $valid_tables) || !in_array($current_table, $valid_tables)) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            exit;
        }

        if ($current_table === $new_role) {
            echo json_encode(['success' => true, 'message' => 'No change in role']);
            exit;
        }

        // Map table to ID and name fields
        $table_config = [
            'admin' => ['id_field' => 'id', 'name_field' => 'email'],
            'giang_vien' => ['id_field' => 'stt_gv', 'name_field' => 'ho_ten'],
            'sinh_vien' => ['id_field' => 'stt_sv', 'name_field' => 'ho_ten'],
            'co_so_thuc_tap' => ['id_field' => 'stt_cstt', 'name_field' => 'ten_co_so']
        ];

        // Fetch user data
        $id_field = $table_config[$current_table]['id_field'];
        $name_field = $table_config[$current_table]['name_field'];
        $query = "SELECT $name_field AS name, email, password FROM $current_table WHERE $id_field = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            // Insert into new table
            $new_id_field = $table_config[$new_role]['id_field'];
            $new_name_field = $table_config[$new_role]['name_field'];
            if ($new_role === 'admin') {
                $query = "INSERT INTO $new_role (email, password, role) VALUES (?, ?, 'admin')";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $user['email'], $user['password']);
            } else {
                $query = "INSERT INTO $new_role ($new_name_field, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $role_value = $new_role;
                $stmt->bind_param("ssss", $user['name'], $user['email'], $user['password'], $role_value);
            }
            if ($stmt->execute()) {
                // Delete from old table
                $stmt = $conn->prepare("DELETE FROM $current_table WHERE $id_field = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update role']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found']);
        }
        break;

    case 'delete_user':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $table = isset($_POST['table']) ? trim($_POST['table']) : '';

        $valid_tables = ['admin', 'sinh_vien', 'giang_vien', 'co_so_thuc_tap'];
        if (!$id || !in_array($table, $valid_tables)) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            exit;
        }

        $table_config = [
            'admin' => 'id',
            'giang_vien' => 'stt_gv',
            'sinh_vien' => 'stt_sv',
            'co_so_thuc_tap' => 'stt_cstt'
        ];
        $id_field = $table_config[$table];
        $stmt = $conn->prepare("DELETE FROM $table WHERE $id_field = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}

$conn->close();
