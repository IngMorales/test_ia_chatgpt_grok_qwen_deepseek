<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TaskMaster Pro - Gestor Inteligente</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #818cf8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f8fafc;
            --dark: #1e293b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .header h1 {
            color: var(--dark);
            font-size: 2.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .list-creator {
            background: var(--light);
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            transition: transform 0.2s ease;
        }

        .list-creator:hover {
            transform: translateY(-2px);
        }

        .input-group {
            flex-grow: 1;
            position: relative;
        }

        input[type="text"] {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #4f46e5;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .lists-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .list-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: cardEnter 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .list-actions {
            display: flex;
            gap: 0.5rem;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--light);
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .task-item:hover {
            transform: translateX(5px);
        }

        .progress-container {
            background: var(--light);
            padding: 2rem;
            border-radius: 1rem;
            margin-top: 2rem;
        }

        @keyframes cardEnter {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-chip {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            background: var(--light);
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-chip::before {
            content: "";
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            display: block;
        }

        .status-chip.completed {
            color: var(--success);
            background: rgba(16, 185, 129, 0.1);
        }

        .status-chip.completed::before {
            background: var(--success);
        }

        .status-chip.in-progress {
            color: var(--warning);
            background: rgba(245, 158, 11, 0.1);
        }

        .status-chip.in-progress::before {
            background: var(--warning);
        }

        .status-chip.pending {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        .status-chip.pending::before {
            background: var(--danger);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 TaskMaster Pro</h1>
            <div class="list-creator">
                <div class="input-group">
                    <input type="text" id="listName" placeholder="Nombre de la lista...">
                </div>
                <button class="btn btn-primary" onclick="TaskManager.createList()">
                    <span>➕ Crear Lista</span>
                </button>
            </div>
        </div>

        <div class="lists-container" id="listsContainer"></div>

        <div class="progress-container">
            <canvas id="progressChart"></canvas>
        </div>
    </div>

    <script>
        class TaskManager {
            static instance;
            
            constructor() {
                if (TaskManager.instance) return TaskManager.instance;
                TaskManager.instance = this;
                
                this.lists = JSON.parse(localStorage.getItem('taskLists')) || [];
                this.initChart();
                this.render();
            }

            static getInstance() {
                if (!TaskManager.instance) {
                    TaskManager.instance = new TaskManager();
                }
                return TaskManager.instance;
            }

            // CRUD Listas
            static createList() {
                const instance = TaskManager.getInstance();
                const input = document.getElementById('listName');
                const name = input.value.trim();
                
                if (!name) {
                    alert('Por favor ingresa un nombre para la lista');
                    return;
                }

                instance.lists.push({
                    id: Date.now(),
                    name: name,
                    tasks: [],
                    status: 'pending',
                    created: new Date(),
                    lastUpdated: new Date()
                });

                input.value = '';
                instance.save();
                instance.render();
            }

            updateList(listId, newName) {
                const list = this.lists.find(l => l.id === listId);
                if (list) {
                    list.name = newName;
                    list.lastUpdated = new Date();
                    this.save();
                    this.render();
                }
            }

            deleteList(listId) {
                this.lists = this.lists.filter(l => l.id !== listId);
                this.save();
                this.render();
            }

            // CRUD Tareas
            addTask(listId, taskText) {
                const list = this.lists.find(l => l.id === listId);
                if (list && taskText.trim()) {
                    list.tasks.push({
                        id: Date.now(),
                        text: taskText.trim(),
                        completed: false,
                        created: new Date()
                    });
                    this.updateListStatus(listId);
                    this.save();
                    this.renderList(listId);
                }
            }

            editTask(listId, taskId, newText) {
                const list = this.lists.find(l => l.id === listId);
                if (list) {
                    const task = list.tasks.find(t => t.id === taskId);
                    if (task) {
                        task.text = newText.trim();
                        this.save();
                        this.renderList(listId);
                    }
                }
            }

            deleteTask(listId, taskId) {
                const list = this.lists.find(l => l.id === listId);
                if (list) {
                    list.tasks = list.tasks.filter(t => t.id !== taskId);
                    this.updateListStatus(listId);
                    this.save();
                    this.renderList(listId);
                }
            }

            toggleTask(listId, taskId) {
                const list = this.lists.find(l => l.id === listId);
                if (list) {
                    const task = list.tasks.find(t => t.id === taskId);
                    if (task) {
                        task.completed = !task.completed;
                        this.updateListStatus(listId);
                        this.save();
                        this.renderList(listId);
                    }
                }
            }

            // Helpers
            updateListStatus(listId) {
                const list = this.lists.find(l => l.id === listId);
                if (!list) return;

                const totalTasks = list.tasks.length;
                const completedTasks = list.tasks.filter(t => t.completed).length;

                if (totalTasks === 0) {
                    list.status = 'pending';
                } else if (completedTasks === totalTasks) {
                    list.status = 'completed';
                } else if (completedTasks > 0) {
                    list.status = 'in-progress';
                } else {
                    list.status = 'pending';
                }
                list.lastUpdated = new Date();
            }

            // Renderizado
            render() {
                this.renderLists();
                this.updateChart();
            }

            renderLists() {
                const container = document.getElementById('listsContainer');
                container.innerHTML = '';
                
                this.lists.forEach(list => {
                    const listElement = document.createElement('div');
                    listElement.className = 'list-card';
                    listElement.innerHTML = `
                        <div class="list-header">
                            <h3 contenteditable="true" 
                                onblur="TaskManager.getInstance().updateList(${list.id}, this.innerText)">
                                ${list.name}
                            </h3>
                            <div class="list-actions">
                                <button class="btn btn-danger" 
                                    onclick="TaskManager.getInstance().deleteList(${list.id})">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        <div class="status-chip ${list.status}">
                            ${this.getStatusText(list.status)}
                        </div>
                        <div class="tasks-container" id="tasks-${list.id}"></div>
                        <div class="task-input" style="margin-top: 1rem;">
                            <input type="text" 
                                   id="taskInput-${list.id}" 
                                   placeholder="Nueva tarea..."
                                   onkeypress="if(event.key === 'Enter') TaskManager.getInstance().addTask(${list.id}, this.value)">
                            <button class="btn btn-primary" 
                                    style="margin-top: 0.5rem;"
                                    onclick="TaskManager.getInstance().addTask(${list.id}, document.getElementById('taskInput-${list.id}').value)">
                                ➕ Agregar Tarea
                            </button>
                        </div>
                    `;
                    container.appendChild(listElement);
                    this.renderTasks(list.id);
                });
            }

            renderTasks(listId) {
                const list = this.lists.find(l => l.id === listId);
                const container = document.getElementById(`tasks-${listId}`);
                if (!container) return;

                container.innerHTML = '';
                list.tasks.forEach(task => {
                    const taskElement = document.createElement('div');
                    taskElement.className = 'task-item';
                    taskElement.innerHTML = `
                        <input type="checkbox" 
                            ${task.completed ? 'checked' : ''} 
                            onchange="TaskManager.getInstance().toggleTask(${listId}, ${task.id})">
                        <span contenteditable="true" 
                            style="${task.completed ? 'text-decoration: line-through; opacity: 0.7;' : ''}"
                            onblur="TaskManager.getInstance().editTask(${listId}, ${task.id}, this.innerText)">
                            ${task.text}
                        </span>
                        <button class="btn btn-danger" 
                                style="margin-left: auto; padding: 0.25rem 0.5rem;"
                                onclick="TaskManager.getInstance().deleteTask(${listId}, ${task.id})">
                            ✖
                        </button>
                    `;
                    container.appendChild(taskElement);
                });
            }

            // Gráficos
            initChart() {
                this.ctx = document.getElementById('progressChart').getContext('2d');
                this.chart = new Chart(this.ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completadas', 'En Progreso', 'Pendientes'],
                        datasets: [{
                            data: [0, 0, 0],
                            backgroundColor: [
                                getComputedStyle(document.documentElement).getPropertyValue('--success'),
                                getComputedStyle(document.documentElement).getPropertyValue('--warning'),
                                getComputedStyle(document.documentElement).getPropertyValue('--danger')
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: { enabled: true }
                        }
                    }
                });
            }

            updateChart() {
                const counts = {
                    completed: this.lists.filter(l => l.status === 'completed').length,
                    'in-progress': this.lists.filter(l => l.status === 'in-progress').length,
                    pending: this.lists.filter(l => l.status === 'pending').length
                };

                this.chart.data.datasets[0].data = [
                    counts.completed,
                    counts['in-progress'],
                    counts.pending
                ];
                this.chart.update();
            }

            // Utilidades
            getStatusText(status) {
                return {
                    'completed': 'Completada',
                    'in-progress': 'En Progreso',
                    'pending': 'Pendiente'
                }[status];
            }

            save() {
                localStorage.setItem('taskLists', JSON.stringify(this.lists));
                this.updateChart();
            }
        }

        // Inicialización
        const taskManager = TaskManager.getInstance();
    </script>
</body>
</html>