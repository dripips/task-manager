<?php
function isUserExists($pdo, $username) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function registerUser($pdo, $username, $password) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $password])) {
        $user_id = $pdo->lastInsertId();

        $_SESSION['user_id'] = $user_id;
        return true;
    }

    return false;
}

function loginUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    return false;
}

function getTasksForUser($pdo, $user_id, $done) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? AND is_done = ?");
    $stmt->execute([$user_id, $done]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addTaskForUser($pdo, $user_id, $title, $due_date) {
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, due_date) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $title, $due_date]);
}

function markTaskAsDone($pdo, $task_id) {
    $stmt = $pdo->prepare("UPDATE tasks SET is_done = 1 WHERE id = ?");
    $stmt->execute([$task_id]);
}

function deleteTask($pdo, $task_id) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);
}

function taskBelongsToUser($pdo, $task_id, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
    $count = $stmt->fetchColumn();

    return $count > 0;
}
?>
