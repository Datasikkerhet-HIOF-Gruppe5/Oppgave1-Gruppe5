<?php
session_start();

function user_registration($data) {
    $errors = array();

    if (!preg_match('/^[a-zA-Z]+$/', $data['username'])) {
        $errors[] = "Please enter a valid username";
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email";
    }

    if (strlen(trim($data['password'])) < 6) {
        $errors[] = "Password must be at least 6 chars long";
    }

    if ($data['password'] != $data['password2']) {
        $errors[] = "Passwords must match";
    }

    $checkEmail = checkEmailExistence($data['email']);
    if ($checkEmail) {
        $errors[] = "That email already exists";
    }

    $checkUsername = checkUsernameExistence($data['username']);
    if ($checkUsername) {
        $errors[] = "That username already exists";
    }

    if (count($errors) == 0) {
        $arr['username'] = $data['username'];
        $arr['email'] = $data['email'];
        $arr['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $arr['date'] = date('d-m-Y H:i:s');

        insertUser($arr);
    }

    return $errors;
}

function lecturer_registration($data) {
    $errors = array();

    if (!preg_match('/^[a-zA-Z]+$/', $data['username'])) {
        $errors[] = "Please enter a valid username";
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email";
    }

    if (strlen(trim($data['password'])) < 6) {
        $errors[] = "Password must be at least 6 chars long";
    }

    if ($data['password'] != $data['password2']) {
        $errors[] = "Passwords must match";
    }

    if (empty($data['course'])) {
        $errors[] = "Please enter the course name";
    }

    if (strlen(trim($data['pin'])) < 4) {
        $errors[] = "Please enter a 4 digit PIN code for the course";
    }

    $checkEmail = checkEmailExistence($data['email']);
    if ($checkEmail) {
        $errors[] = "That email already exists";
    }

    $checkUsername = checkUsernameExistence($data['username']);
    if ($checkUsername) {
        $errors[] = "That username already exists";
    }

    if (!empty($_FILES) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'path/';  // må sette path til der vi lagrer bilder på webserveren

        $username = $_POST['username'];
        $uploadFile = $uploadDir . $username . '_' . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $arr['image'] = $uploadFile;
        } else {
            $errors[] = "Failed to upload image";
        }
    } else {
        $errors[] = "Please upload an image";
    }

    if (count($errors) == 0) {
        $arr['name'] = $data['name'];
        $arr['username'] = $data['username'];
        $arr['email'] = $data['email'];
        $arr['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $arr['date'] = date('d-m-Y H:i:s');
        $arr['role'] = 'lecturer';

        insertLecturer($arr);

        $lecturerId = getLecturerIdById($arr['id']);

        $courseData = [
            'lecturer_id' => $lecturerId,
            'course_name' => $data['course'],
            'pin' => $data['pin'],
        ];

        insertCourse($courseData);
    }

    return $errors;
}

function login($data) {
    $errors = array();

    if (empty($data['username']) || empty($data['password'])) {
        $errors[] = "Both username and password are required";
        return $errors;
    }

    $user = getUserByUsername($data['username']);
    $lecturer = getLecturerByUsername($data['username']);

    if (!$user && !$lecturer) {
        $errors[] = "User not found";
        return $errors;
    }

    $dbUser = ($user !== null) ? $user : $lecturer;

    if (!password_verify($data['password'], $dbUser['password'])) {
        $errors[] = "Incorrect password";
        return $errors;
    }

    $_SESSION['user'] = $dbUser;

    return $errors;
}

function updatePassword($data)
{
    $errors = array();

    if (empty($data['old_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
        $errors[] = "All fields are required";
        return $errors;
    }

    $userId = $_SESSION['user']['id'];
    $user = getUserById($userId);

    if (!$user) {
        $errors[] = "User not found";
        return $errors;
    }

    if (!password_verify($data['old_password'], $user['password'])) {
        $errors[] = "Incorrect old password";
        return $errors;
    }

    if (strlen(trim($data['new_password'])) < 6) {
        $errors[] = "New password must be at least 6 characters long";
    }

    if ($data['new_password'] !== $data['confirm_password']) {
        $errors[] = "New passwords do not match";
    }

    if (count($errors) == 0) {
        $newHashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

        updateUserPassword($userId, $newHashedPassword);
    }

    return $errors;
}

function updateUserPassword($userId, $newHashedPassword)
{
    $query = "UPDATE users SET password = :password WHERE id = :id";
    $params = array('password' => $newHashedPassword, 'id' => $userId);

    database_run($query, $params);
}

function getLecturerByUsername($username) {
    $result = database_run("SELECT * FROM lecturers WHERE username = :username LIMIT 1", ['username' => $username]);

    return (is_array($result) && count($result) > 0) ? $result[0] : null;
}

function getUserByUsername($username) {
    $result = database_run("SELECT * FROM users WHERE username = :username LIMIT 1", ['username' => $username]);

    return (is_array($result) && count($result) > 0) ? $result[0] : null;
}

function getLecturerIdById($lecturerId) {
    $query = "SELECT id FROM lecturers WHERE id = :id LIMIT 1";
    $result = database_run($query, ['id' => $lecturerId]);

    return ($result && count($result) > 0) ? $result[0]['id'] : null;
}
function checkEmailExistence($email) {
    $result = database_run("SELECT * FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
    return is_array($result);
}

function checkUsernameExistence($username) {
    $user = getUserByUsername($username);
    return ($user !== null);
}

function insertUser($data) {
    $query = "INSERT INTO users (username, email, password, date, role) VALUES (:username, :email, :password, :date, :role)";
    database_run($query, $data);
}

function insertLecturer($data) {
    $query = "INSERT INTO lecturers (username, email, image, date, role) VALUES (:username, :email, :image, :date, :role)";
    database_run($query, $data);
}

function insertCourse($data) {
    $query = "INSERT INTO courses (lecturer_id, course_name, pin) VALUES (:lecturer_id, :course_name, :pin)";
    database_run($query, $data);
}



function database_run($query, $params = array()) {
    try {
        $conn = new mysqli("hostname", "username", "password", "database");

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare($query);

        if ($stmt) {
            if (!empty($params)) {
                $types = '';
                $values = array();

                foreach ($params as $param) {
                    $types .= get_type($param);
                    $values[] = $param;
                }

                $stmt->bind_param($types, ...$values);
            }

            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();
        } else {
            throw new Exception("Error in preparing query: " . $conn->error);
        }

        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : true;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    } finally {
        if ($conn) {
            $conn->close();
        }
    }
}

function get_type($value) {
    if (is_int($value)) {
        return 'i';
    } elseif (is_float($value)) {
        return 'd';
    } else {
        return 's';
    }
}

