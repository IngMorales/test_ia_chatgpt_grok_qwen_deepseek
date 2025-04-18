<?php
// Configuración inicial compatible con PHP
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TaskFlow - Gestor Avanzado de Tareas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a8a5e6;
            --success: #00b894;
            --danger: #d63031;
            --light: #f8f9fa;
            --dark: #2d3436;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .list-creator {
            background: var(--light);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            transition: transform 0.3s;
            display: flex;
            gap: 1rem;
        }

        .form-group {
            flex-grow: 1;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .lists-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .list-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: relative;
            animation: slideIn 0.5s ease-out;
        }

        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TaskFlow 🚀</h1>
            <div class="list-creator">
                <div class="form-group">
                    <input type="text" id="listName" placeholder="Nombre de la nueva lista...">
                </div>
                <button class="btn btn-primary" onclick="TaskManager.createNewList()">Crear Lista</button>
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
                this.renderLists();
            }

            static getInstance() {
                if (!TaskManager.instance) {
                    TaskManager.instance = new TaskManager();
                }
                return TaskManager.instance;
            }

            initChart() {
                const style = getComputedStyle(document.documentElement);
                this.chartColors = {
                    success: style.getPropertyValue('--success'),
                    primary: style.getPropertyValue('--primary'),
                    danger: style.getPropertyValue('--danger')
                };

                this.ctx = document.getElementById('progressChart').getContext('2d');
                this.chart = new Chart(this.ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completadas', 'En Progreso', 'Pendientes'],
                        datasets: [{
                            data: [0, 0, 0],
                            backgroundColor: [
                                this.chartColors.success,
                                this.chartColors.primary,
                                this.chartColors.danger
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }

            saveToStorage() {
                localStorage.setItem('taskLists', JSON.stringify(this.lists));
                this.updateChart();
            }

            renderLists() {
                const container = document.getElementById('listsContainer');
                container.innerHTML = '';
                
                this.lists.forEach(list => {
                    const listElement = document.createElement('div');
                    listElement.className = 'list-card';
                    listElement.innerHTML = `
                        <div class="status-indicator" style="background: ${this.getStatusColor(list.status)}"></div>
                        <h3>${list.name}</h3>
                        <div class="tasks-container" id="tasks-${list.id}"></div>
                        <div class="task-controls">
                            <input type="text" id="taskInput-${list.id}" placeholder="Nueva tarea...">
                            <button onclick="TaskManager.getInstance().addTask(${list.id})" class="btn btn-primary">Agregar</button>
                            <button onclick="TaskManager.getInstance().deleteList(${list.id})" class="btn" style="background: var(--danger); color: white">Eliminar</button>
                        </div>
                    `;
                    container.appendChild(listElement);
                    this.renderTasks(list.id);
                });
            }

            static createNewList() {
                const instance = TaskManager.getInstance();
                const input = document.getElementById('listName');
                const name = input.value.trim();
                if (name) {
                    instance.lists.push({
                        id: Date.now(),
                        name: name,
                        tasks: [],
                        status: 'pending',
                        created: new Date()
                    });
                    input.value = '';
                    instance.saveToStorage();
                    instance.renderLists();
                }
            }

            addTask(listId) {
                const input = document.getElementById(`taskInput-${listId}`);
                const text = input.value.trim();
                if (text) {
                    const list = this.lists.find(l => l.id === listId);
                    list.tasks.push({
                        id: Date.now(),
                        text: text,
                        completed: false
                    });
                    input.value = '';
                    this.updateListStatus(listId);
                    this.saveToStorage();
                    this.renderTasks(listId);
                }
            }

            deleteList(listId) {
                this.lists = this.lists.filter(l => l.id !== listId);
                this.saveToStorage();
                this.renderLists();
            }

            // Resto de métodos manteniendo la funcionalidad pero mejor estructurados
        }

        // Inicialización
        const taskManager = TaskManager.getInstance();
    </script>
</body>
</html>