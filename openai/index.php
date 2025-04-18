<?php
// index.php: Gestor de tareas interactivo con animaciones y almacenamiento en localStorage
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Listas y Tareas</title>
    <style>
        /* Reset y tipografía */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        header {
            text-align: center;
            padding: 50px 20px;
            color: #fff;
        }

        header h1 {
            font-size: 3rem;
            animation: dropIn 1s ease-out;
        }

        header p {
            font-size: 1.2rem;
            margin-top: 10px;
            opacity: 0;
            animation: fadeText 2s ease-out forwards;
            animation-delay: 1s;
        }

        @keyframes dropIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeText {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .container {
            width: 90%;
            max-width: 900px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin-bottom: 40px;
        }

        /* Form creación de listas */
        #create-list-section {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease-out;
        }

        input,
        button,
        select {
            font-size: 1rem;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background: #667eea;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #5a67d8;
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Listas y tarjetas */
        .list {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease-out;
        }

        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .list-header h2 {
            font-size: 1.5rem;
            word-break: break-word;
        }

        .list-header .actions button {
            margin-left: 5px;
        }

        .task-list,
        .summary-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        .task-item:hover {
            background: #f0f0f0;
        }

        .task-item.completed span {
            text-decoration: line-through;
            color: #999;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #fff;
        }

        .status-pending {
            background: #f6ad55;
        }

        .status-completed {
            background: #48bb78;
        }

        .status-upcoming {
            background: #4299e1;
        }

        /* Resumen */
        h3 {
            margin-top: 40px;
            margin-bottom: 10px;
            text-align: center;
        }

        .summary-list li {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <header>
        <h1>Gestor de Listas y Tareas</h1>
        <p>Crea y administra tus listas con seguimiento en tiempo real</p>
    </header>
    <div class="container">
        <!-- Sección para crear nueva lista -->
        <div id="create-list-section">
            <input type="text" id="new-list-name" placeholder="Nombre de la lista">
            <input type="date" id="new-list-date">
            <button id="create-list-btn">Crear Lista</button>
        </div>
        <!-- Contenedor de listas -->
        <div id="lists-container"></div>
        <!-- Resumen en tiempo real -->
        <h3>Resumen en Tiempo Real</h3>
        <ul id="summary" class="summary-list"></ul>
    </div>

    <script>
        // --- Variables y estado ---
        let lists = JSON.parse(localStorage.getItem('lists')) || [];
        const listsContainer = document.getElementById('lists-container');
        const summaryEl = document.getElementById('summary');
        const newListName = document.getElementById('new-list-name');
        const newListDate = document.getElementById('new-list-date');
        const createListBtn = document.getElementById('create-list-btn');

        // --- Funciones de almacenamiento ---
        function saveLists() {
            localStorage.setItem('lists', JSON.stringify(lists));
            renderLists();
            renderSummary();
        }

        // --- Renderizado de listas ---
        function renderLists() {
            listsContainer.innerHTML = '';
            lists.forEach(list => {
                const card = document.createElement('div');
                card.className = 'list';
                // Header
                const header = document.createElement('div');
                header.className = 'list-header';
                const title = document.createElement('h2');
                title.textContent = `${list.name} (Vence: ${list.dueDate})`;
                const actions = document.createElement('div');
                actions.className = 'actions';
                // Botones editar y eliminar lista
                const editBtn = document.createElement('button');
                editBtn.textContent = 'Editar';
                editBtn.onclick = () => editList(list.id);
                const delBtn = document.createElement('button');
                delBtn.textContent = 'Eliminar';
                delBtn.onclick = () => deleteList(list.id);
                actions.append(editBtn, delBtn);
                header.append(title, actions);
                card.append(header);
                // Tareas
                const taskList = document.createElement('ul');
                taskList.className = 'task-list';
                list.tasks.forEach(task => {
                    const li = document.createElement('li');
                    li.className = 'task-item';
                    if (task.completed) li.classList.add('completed');
                    const label = document.createElement('span');
                    label.textContent = task.name;
                    const controls = document.createElement('div');
                    const chk = document.createElement('input');
                    chk.type = 'checkbox';
                    chk.checked = task.completed;
                    chk.onchange = () => toggleTask(list.id, task.id);
                    const editT = document.createElement('button');
                    editT.textContent = '✏';
                    editT.onclick = () => editTask(list.id, task.id);
                    const delT = document.createElement('button');
                    delT.textContent = '✖';
                    delT.onclick = () => deleteTask(list.id, task.id);
                    controls.append(chk, editT, delT);
                    li.append(label, controls);
                    taskList.append(li);
                });
                card.append(taskList);
                // Crear nueva tarea
                const addTaskDiv = document.createElement('div');
                addTaskDiv.style.marginTop = '10px';
                const taskInput = document.createElement('input');
                taskInput.placeholder = 'Nueva actividad';
                const taskBtn = document.createElement('button');
                taskBtn.textContent = 'Agregar';
                taskBtn.onclick = () => addTask(list.id, taskInput.value) & (taskInput.value = '');
                addTaskDiv.append(taskInput, taskBtn);
                card.append(addTaskDiv);
                listsContainer.append(card);
            });
        }

        // --- Renderizado de resumen ---
        function renderSummary() {
            summaryEl.innerHTML = '';
            const now = new Date();
            const upcoming = [],
                completed = [],
                pending = [];
            lists.forEach(l => {
                const due = new Date(l.dueDate);
                const allDone = l.tasks.length > 0 && l.tasks.every(t => t.completed);
                if (allDone) completed.push(l);
                else if (due < now) pending.push(l);
                else if (due - now <= 7 * 24 * 3600 * 1000) upcoming.push(l);
            });
            const sections = [{
                    title: 'Próximas a Cumplir',
                    items: upcoming,
                    badge: 'status-upcoming'
                },
                {
                    title: 'Completadas',
                    items: completed,
                    badge: 'status-completed'
                },
                {
                    title: 'Sin Cumplir',
                    items: pending,
                    badge: 'status-pending'
                }
            ];
            sections.forEach(sec => {
                const li = document.createElement('li');
                const name = document.createElement('span');
                name.textContent = `${sec.title} (${sec.items.length})`;
                const badge = document.createElement('span');
                badge.className = `status-badge ${sec.badge}`;
                badge.textContent = sec.items.length;
                li.append(name, badge);
                summaryEl.append(li);
            });
        }

        // --- CRUD Listas ---
        function createList() {
            const name = newListName.value.trim();
            const date = newListDate.value;
            if (!name || !date) return alert('Completa nombre y fecha');
            lists.push({
                id: Date.now(),
                name,
                dueDate: date,
                tasks: []
            });
            newListName.value = '';
            newListDate.value = '';
            saveLists();
        }

        function editList(id) {
            const list = lists.find(l => l.id === id);
            const newName = prompt('Nuevo nombre', list.name);
            const newDate = prompt('Nueva fecha (YYYY-MM-DD)', list.dueDate);
            if (newName) list.name = newName;
            if (newDate) list.dueDate = newDate;
            saveLists();
        }

        function deleteList(id) {
            if (!confirm('Eliminar lista?')) return;
            lists = lists.filter(l => l.id !== id);
            saveLists();
        }

        // --- CRUD Tareas ---
        function addTask(listId, taskName) {
            if (!taskName.trim()) return;
            const list = lists.find(l => l.id === listId);
            list.tasks.push({
                id: Date.now(),
                name: taskName.trim(),
                completed: false
            });
            saveLists();
        }

        function toggleTask(listId, taskId) {
            const task = lists.find(l => l.id === listId).tasks.find(t => t.id === taskId);
            task.completed = !task.completed;
            saveLists();
        }

        function editTask(listId, taskId) {
            const task = lists.find(l => l.id === listId).tasks.find(t => t.id === taskId);
            const newName = prompt('Editar actividad', task.name);
            if (newName) task.name = newName.trim();
            saveLists();
        }

        function deleteTask(listId, taskId) {
            const list = lists.find(l => l.id === listId);
            list.tasks = list.tasks.filter(t => t.id !== taskId);
            saveLists();
        }

        // --- Eventos ---
        createListBtn.addEventListener('click', createList);
        // Inicializar
        saveLists();
    </script>
</body>

</html>