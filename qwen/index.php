<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>TaskFlow - Gestión de Tareas en Tiempo Real</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos creativos con animaciones */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero {
            text-align: center;
            padding: 60px 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 3.5em;
            color: #2c3e50;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero p {
            font-size: 1.2em;
            color: #7f8c8d;
            max-width: 600px;
            margin: 0 auto 30px;
        }

        .task-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .task-form input,
        .task-form textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #3498db;
            border-radius: 5px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .task-form button {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .task-form button:hover {
            background: #2980b9;
            transform: scale(1.05);
        }

        .task-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .task-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .task-card:hover {
            transform: translateY(-5px);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .task-title {
            font-size: 1.5em;
            color: #2c3e50;
        }

        .task-date {
            font-size: 0.9em;
            color: #7f8c8d;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .pending .status-indicator {
            background: #f1c40f;
        }

        .completed .status-indicator {
            background: #2ecc71;
        }

        .overdue .status-indicator {
            background: #e74c3c;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .progress-bar {
            height: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            margin: 20px 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #3498db;
            width: 0%;
            transition: width 0.3s ease;
        }

        .controls button {
            background: none;
            border: none;
            color: #3498db;
            cursor: pointer;
            margin: 0 5px;
            transition: color 0.3s ease;
        }

        .controls button:hover {
            color: #2980b9;
        }

        /* Estilos para el consolidado */
        .dashboard {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .stat-box {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .stat-number {
            font-size: 2em;
            color: #3498db;
        }

        .stat-label {
            color: #7f8c8d;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
            }

            .stat-box {
                margin: 15px 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="hero">
            <h1>TaskFlow</h1>
            <p>Gestiona tus proyectos con estilo y visualiza tu progreso en tiempo real</p>
        </div>

        <div class="task-form">
            <h2>Crear Nueva Lista de Tareas</h2>
            <input type="text" id="listTitle" placeholder="Título de la lista" required>
            <textarea id="listDescription" placeholder="Descripción" rows="3"></textarea>
            <input type="date" id="listDeadline">
            <button onclick="createTaskList()">Crear Lista <i class="fas fa-plus"></i></button>
        </div>

        <div class="task-list" id="taskListContainer"></div>

        <div class="dashboard">
            <h2>Consolidado de Tareas</h2>
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-number" id="totalTasks">0</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number" id="completedTasks">0</div>
                    <div class="stat-number" id="pendingTasks">0</div>
                    <div class="stat-label">Completadas</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number" id="overdueTasks">0</div>
                    <div class="stat-label">Vencidas</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sistema de gestión de tareas con localStorage
        let taskLists = JSON.parse(localStorage.getItem('taskLists')) || [];

        function saveToLocalStorage() {
            localStorage.setItem('taskLists', JSON.stringify(taskLists));
        }

        function createTaskList() {
            const title = document.getElementById('listTitle').value;
            const description = document.getElementById('listDescription').value;
            const deadline = document.getElementById('listDeadline').value;

            if (!title) return alert('El título es requerido');

            const newList = {
                id: Date.now(),
                title,
                description,
                deadline,
                status: 'pending',
                activities: [],
                createdAt: new Date()
            };

            taskLists.push(newList);
            saveToLocalStorage();
            renderTaskLists();
            document.getElementById('listTitle').value = '';
            document.getElementById('listDescription').value = '';
            document.getElementById('listDeadline').value = '';
        }

        function renderTaskLists() {
            const container = document.getElementById('taskListContainer');
            container.innerHTML = '';

            taskLists.forEach(list => {
                const card = document.createElement('div');
                card.className = `task-card ${list.status}`;

                const deadlineDate = new Date(list.deadline);
                const now = new Date();
                if (list.status !== 'completed') {
                    if (deadlineDate < now) list.status = 'overdue';
                }

                card.innerHTML = `
                    <div class="task-header">
                        <div class="task-info">
                            <div class="status-indicator"></div>
                            <div>
                                <div class="task-title">${list.title}</div>
                                <div class="task-date">${list.description}</div>
                            </div>
                        </div>
                        <div class="controls">
                            <button onclick="editTaskList(${list.id})"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteTaskList(${list.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    <div class="deadline">Fecha límite: ${list.deadline}</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${getProgress(list)}%"></div>
                    </div>
                    <div class="activities" id="activities-${list.id}">
                        ${list.activities.map(activity => `
                            <div class="activity-item">
                                <input type="checkbox" ${activity.completed ? 'checked' : ''} 
                                    onchange="toggleActivity(${list.id}, ${activity.id})">
                                <span>${activity.text}</span>
                                <small>${activity.deadline}</small>
                            </div>
                        `).join('')}
                    </div>
                    <button class="add-activity" onclick="showActivityForm(${list.id})">
                        + Agregar Actividad
                    </button>
                `;

                container.appendChild(card);
            });

            updateDashboardStats();
        }

        function getProgress(list) {
            if (list.activities.length === 0) return 0;
            const completed = list.activities.filter(a => a.completed).length;
            return (completed / list.activities.length) * 100;
        }

        function toggleActivity(listId, activityId) {
            const list = taskLists.find(l => l.id === listId);
            const activity = list.activities.find(a => a.id === activityId);
            activity.completed = !activity.completed;
            saveToLocalStorage();
            renderTaskLists();
        }

        function showActivityForm(listId) {
            const activitiesContainer = document.querySelector(`#activities-${listId}`);
            const form = `
                <div class="activity-form">
                    <input type="text" id="activityText-${listId}" placeholder="Nueva actividad">
                    <input type="date" id="activityDeadline-${listId}">
                    <button onclick="addActivity(${listId})">Agregar</button>
                </div>
            `;
            activitiesContainer.insertAdjacentHTML('beforeend', form);
        }

        function addActivity(listId) {
            const list = taskLists.find(l => l.id === listId);
            const text = document.querySelector(`#activityText-${listId}`).value;
            const deadline = document.querySelector(`#activityDeadline-${listId}`).value;

            if (!text) return alert('El texto de la actividad es requerido');

            list.activities.push({
                id: Date.now(),
                text,
                deadline,
                completed: false
            });

            saveToLocalStorage();
            renderTaskLists();
        }

        function deleteTaskList(id) {
            taskLists = taskLists.filter(list => list.id !== id);
            saveToLocalStorage();
            renderTaskLists();
        }

        function editTaskList(id) {
            const list = taskLists.find(list => list.id === id);
            const newTitle = prompt('Editar título', list.title);
            if (newTitle) {
                list.title = newTitle;
                saveToLocalStorage();
                renderTaskLists();
            }
        }

        function updateDashboardStats() {
            const total = taskLists.length;
            const completed = taskLists.filter(l => l.status === 'completed').length;
            const overdue = taskLists.filter(l => l.status === 'overdue').length;
            const pending = total - completed - overdue;

            document.getElementById('totalTasks').textContent = total;
            document.getElementById('completedTasks').textContent = completed;
            document.getElementById('pendingTasks').textContent = pending;
            document.getElementById('overdueTasks').textContent = overdue;
        }

        // Actualización en tiempo real
        setInterval(() => {
            taskLists.forEach(list => {
                const deadline = new Date(list.deadline);
                const now = new Date();
                if (list.status !== 'completed' && deadline < now) {
                    list.status = 'overdue';
                }
            });
            renderTaskLists();
        }, 60000); // Verificar cada minuto

        // Render inicial
        renderTaskLists();
    </script>
</body>

</html>