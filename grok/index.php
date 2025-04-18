<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Listas de Tareas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in;
        }

        .task-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            animation: slideIn 1s ease-out;
        }

        .task-form input, .task-form button {
            padding: 10px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
        }

        .task-form input {
            width: 60%;
            background: rgba(255, 255, 255, 0.9);
        }

        .task-form button {
            background: #28a745;
            color: white;
            cursor: pointer;
            transition: transform 0.3s, background 0.3s;
        }

        .task-form button:hover {
            background: #218838;
            transform: scale(1.05);
        }

        .task-list, .dashboard {
            display: grid;
            gap: 20px;
        }

        .task-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 15px;
            border-radius: 10px;
            animation: cardAppear 0.5s ease-in;
            position: relative;
            transition: transform 0.3s;
        }

        .task-card:hover {
            transform: translateY(-5px);
        }

        .task-card h3 {
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .task-card .status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .status.pending { background: #ffc107; color: #000; }
        .status.completed { background: #28a745; color: #fff; }
        .status.overdue { background: #dc3545; color: #fff; }

        .task-card button {
            margin: 5px;
            padding: 8px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .task-card .edit { background: #007bff; color: white; }
        .task-card .edit:hover { background: #0056b3; }
        .task-card .delete { background: #dc3545; color: white; }
        .task-card .delete:hover { background: #c82333; }
        .task-card .add-activity { background: #17a2b8; color: white; }
        .task-card .add-activity:hover { background: #138496; }

        .activity-list {
            margin-top: 10px;
            padding-left: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
            padding: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .activity-item input[type="checkbox"] {
            margin-right: 10px;
        }

        .dashboard {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .dashboard h2 {
            margin-bottom: 15px;
        }

        .dashboard ul {
            list-style: none;
        }

        .dashboard li {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes cardAppear {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestor de Listas de Tareas</h1>
        <div class="task-form">
            <input type="text" id="taskName" placeholder="Nombre de la lista" required>
            <input type="date" id="taskDueDate" required>
            <button onclick="addTask()">Crear Lista</button>
        </div>
        <div class="task-list" id="taskList"></div>
        <div class="dashboard">
            <h2>Seguimiento en Tiempo Real</h2>
            <ul id="dashboard"></ul>
        </div>
    </div>

    <script>
        let tasks = JSON.parse(localStorage.getItem('tasks')) || [];

        function saveTasks() {
            localStorage.setItem('tasks', JSON.stringify(tasks));
            renderTasks();
            renderDashboard();
        }

        function addTask() {
            const name = document.getElementById('taskName').value;
            const dueDate = document.getElementById('taskDueDate').value;
            if (!name || !dueDate) return alert('Por favor, completa todos los campos');

            const task = {
                id: Date.now(),
                name,
                dueDate,
                activities: [],
                status: 'pending'
            };
            tasks.push(task);
            saveTasks();
            document.getElementById('taskName').value = '';
            document.getElementById('taskDueDate').value = '';
        }

        function editTask(id) {
            const task = tasks.find(t => t.id === id);
            const newName = prompt('Nuevo nombre de la lista:', task.name);
            const newDueDate = prompt('Nueva fecha de vencimiento (YYYY-MM-DD):', task.dueDate);
            if (newName) task.name = newName;
            if (newDueDate) task.dueDate = newDueDate;
            saveTasks();
        }

        function deleteTask(id) {
            if (confirm('¿Seguro que quieres eliminar esta lista?')) {
                tasks = tasks.filter(t => t.id !== id);
                saveTasks();
            }
        }

        function addActivity(taskId) {
            const activityName = prompt('Nombre de la actividad:');
            if (!activityName) return;

            const task = tasks.find(t => t.id === taskId);
            task.activities.push({
                id: Date.now(),
                name: activityName,
                completed: false
            });
            saveTasks();
        }

        function toggleActivity(taskId, activityId) {
            const task = tasks.find(t => t.id === taskId);
            const activity = task.activities.find(a => a.id === activityId);
            activity.completed = !activity.completed;
            updateTaskStatus(task);
            saveTasks();
        }

        function deleteActivity(taskId, activityId) {
            if (confirm('¿Seguro que quieres eliminar esta actividad?')) {
                const task = tasks.find(t => t.id === taskId);
                task.activities = task.activities.filter(a => a.id !== activityId);
                updateTaskStatus(task);
                saveTasks();
            }
        }

        function updateTaskStatus(task) {
            const now = new Date();
            const dueDate = new Date(task.dueDate);
            const allCompleted = task.activities.length > 0 && task.activities.every(a => a.completed);

            if (allCompleted && task.activities.length > 0) {
                task.status = 'completed';
            } else if (dueDate < now && !allCompleted) {
                task.status = 'overdue';
            } else {
                task.status = 'pending';
            }
        }

        function renderTasks() {
            const taskList = document.getElementById('taskList');
            taskList.innerHTML = '';
            tasks.forEach(task => {
                updateTaskStatus(task);
                const taskCard = document.createElement('div');
                taskCard.className = 'task-card';
                taskCard.innerHTML = `
                    <h3>${task.name} (Vence: ${task.dueDate})</h3>
                    <span class="status ${task.status}">
                        ${task.status === 'pending' ? 'Pendiente' : task.status === 'completed' ? 'Completada' : 'Vencida'}
                    </span>
                    <button class="edit" onclick="editTask(${task.id})">Editar</button>
                    <button class="delete" onclick="deleteTask(${task.id})">Eliminar</button>
                    <button class="add-activity" onclick="addActivity(${task.id})">Agregar Actividad</button>
                    <div class="activity-list">
                        ${task.activities.map(activity => `
                            <div class="activity-item">
                                <input type="checkbox" ${activity.completed ? 'checked' : ''} 
                                    onclick="toggleActivity(${task.id}, ${activity.id})">
                                <span>${activity.name}</span>
                                <button class="delete" onclick="deleteActivity(${task.id}, ${activity.id})">X</button>
                            </div>
                        `).join('')}
                    </div>
                `;
                taskList.appendChild(taskCard);
            });
        }

        function renderDashboard() {
            const dashboard = document.getElementById('dashboard');
            dashboard.innerHTML = '';
            tasks.forEach(task => {
                const li = document.createElement('li');
                li.innerHTML = `
                    ${task.name} - Estado: ${task.status === 'pending' ? 'Pendiente' : task.status === 'completed' ? 'Completada' : 'Vencida'}
                    (Vence: ${task.dueDate}, Actividades completadas: ${task.activities.filter(a => a.completed).length}/${task.activities.length})
                `;
                dashboard.appendChild(li);
            });
        }

        // Actualizar estados en tiempo real
        setInterval(() => {
            tasks.forEach(updateTaskStatus);
            saveTasks();
        }, 60000); // Cada minuto

        // Inicializar
        renderTasks();
        renderDashboard();
    </script>
</body>
</html>
