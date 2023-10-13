<?php
require 'config/database.php';
require 'includes/functions.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$tasks = getTasksForUser($pdo, $_SESSION['user_id'], 0);

$tasksDone = getTasksForUser($pdo, $_SESSION['user_id'], 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $due_date = $_POST['due_date'];
    addTaskForUser($pdo, $_SESSION['user_id'], $title, $due_date);
    echo 'Задача добавлена успешно.';
    exit;
} elseif (isset($_GET['mark_as_done'])) {
    $task_id = $_GET['mark_as_done'];
    markTaskAsDone($pdo, $task_id);
    echo 'Задача отмечена как выполненная.';
    exit;
} elseif (isset($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];
    deleteTask($pdo, $task_id);
    echo 'Задача удалена успешно.';
    exit;
}
if ($_GET['action'] == 'refreshTaskList') {
  $completedTasks = getTasksForUser($pdo, $_SESSION['user_id'], 0);
  echo json_encode($completedTasks);
  exit;
} elseif ($_GET['action'] == 'refreshCompletedTaskList') {
    $completedTasks = getTasksForUser($pdo, $_SESSION['user_id'], 1);
    echo json_encode($completedTasks);
    exit;
} elseif ($_GET['action'] == 'logout') {
  session_start();
  session_destroy();
  header("Location: /");
  exit();
}
?>

<?php include 'templates/header.php'; ?>

<div class="container mt-5">
    <div class="row">
      <div class="col-md-10">
        <h1>Задачник</h1>
      </div>
      <div class="col-md-2">
          <a href="index.php?action=logout" class="btn btn-danger">Выход</a>
      </div>
    </div>

    <form id="add-task-form" class="mb-3">
        <div class="form-group">
            <label class="form-label">Название задачи</label>
            <input type="text" name="title" class="form-control" placeholder="Задача" required>
        </div>
        <div class="form-group">
            <label class="form-label">Сроки задачи</label>
            <input type="date" name="due_date" class="form-control">
        </div>
        <input type="hidden" name="add_task" value="1">
        <button type="submit" name="add_task" class="btn btn-primary">Добавить задачу</button>
    </form>
    <div class="row">
      <div class="col-md-8">
        <h4>Таски которые предстоит выполнить</h4>
        <ul id="task-list">
          <!-- Таски подгрузятся через ajax -->
        </ul>
      </div>
      <div class="col-md-4">
        <h4>Выполненые таски</h4>
        <ul id="completed-task-list">
          <!-- Таски подгрузятся через ajax -->
        </ul>
      </div>
    </div>

</div>

<script>
$(document).ready(function() {
    $('#add-task-form').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: $(this).serialize(),
            success: function (data) {
                Toastify({
                    text: data,
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                }).showToast();
                refreshTaskList();
            }
        });
    });

    $(document).on('click', '.delete-task-btn', function () {
        var taskId = $(this).data('task-id');
        $.ajax({
            type: 'GET',
            url: 'index.php?delete_task=' + taskId,
            success: function (data) {
                Toastify({
                    text: data,
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                }).showToast();
                refreshTaskList();
                refreshCompletedTaskList();
            }
        });
    });

    $(document).on('click', '.mark-task-btn', function () {
        var taskId = $(this).data('task-id');
        $.ajax({
            type: 'GET',
            url: 'index.php?mark_as_done=' + taskId,
            success: function (data) {
                Toastify({
                    text: data,
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                }).showToast();
                refreshTaskList();
                refreshCompletedTaskList();
            }
        });
    });

    function refreshCompletedTaskList() {
        $.ajax({
            type: 'GET',
            url: 'index.php?action=refreshCompletedTaskList',
            dataType: 'json',
            success: function (data) {
                var completedTaskList = $('#completed-task-list');
                completedTaskList.html('');
                for (var i = 0; i < data.length; i++) {
                    completedTaskList.append('<li class="list-group-item">' + data[i].title + '  <a data-task-id="' + data[i].id + '" class="btn btn-danger btn-sm float-right mr-2 delete-task-btn">Удалить</a></li>');
                }
            }
        });
    }
    function refreshTaskList() {
        $.ajax({
            type: 'GET',
            url: 'index.php?action=refreshTaskList',
            dataType: 'json',
            success: function (data) {
                var completedTaskList = $('#task-list');
                completedTaskList.html('');
                for (var i = 0; i < data.length; i++) {
                    completedTaskList.append('<li class="list-group-item">' + data[i].title + ' (Срок: ' + data[i].due_date + ')  <a data-task-id="' + data[i].id + '" class="btn btn-success btn-sm float-right mark-task-btn">Отметить как выполненную</a> <a data-task-id="' + data[i].id + '" class="btn btn-danger btn-sm float-right mr-2 delete-task-btn">Удалить</a></li>');
                }
            }
        });
    }

    refreshTaskList();
    refreshCompletedTaskList();

});
</script>
<?php include 'templates/footer.php'; ?>
