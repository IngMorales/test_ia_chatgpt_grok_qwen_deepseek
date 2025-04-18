document.addEventListener('DOMContentLoaded', () => {
    // --- REFERENCIAS AL DOM ---
    const initialListForm = document.getElementById('add-list-form-initial');
    const initialListNameInput = document.getElementById('new-list-name-initial');
    const initialListDueDateInput = document.getElementById('new-list-due-date-initial');
    const initialListCreatorDiv = document.getElementById('list-creator-initial');

    const mainListForm = document.getElementById('add-list-form-main');
    const mainListNameInput = document.getElementById('new-list-name-main');
    const mainListDueDateInput = document.getElementById('new-list-due-date-main');

    const listsContainer = document.getElementById('lists-container');
    const statusOverview = {
        pending: document.getElementById('pending-count'),
        completed: document.getElementById('completed-count'),
        overdue: document.getElementById('overdue-count')
    };

    // --- ESTADO DE LA APLICACIÓN ---
    let lists = []; // Array para almacenar todas las listas y sus tareas

    // --- FUNCIONES AUXILIARES ---

    // Generar ID único (simple para demo)
    const generateId = () => `id_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;

    // Obtener fecha actual en formato YYYY-MM-DD para comparar con due dates
    const getTodayDateString = () => {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Meses son 0-indexados
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    // Guardar en localStorage
    const saveLists = () => {
        localStorage.setItem('todoLists', JSON.stringify(lists));
    };

    // Cargar desde localStorage
    const loadLists = () => {
        const storedLists = localStorage.getItem('todoLists');
        if (storedLists) {
            lists = JSON.parse(storedLists);
        } else {
            lists = []; // Inicializar si no hay nada guardado
        }
        // Ocultar el creador inicial si ya hay listas
        if (lists.length > 0) {
            initialListCreatorDiv.style.display = 'none';
            mainListForm.style.display = 'flex'; // Mostrar el formulario principal
        } else {
            initialListCreatorDiv.style.display = 'block';
            mainListForm.style.display = 'none';
        }
    };

    // --- FUNCIONES DE RENDERIZADO ---

    // Renderizar todas las listas y sus tareas
    const renderLists = () => {
        listsContainer.innerHTML = ''; // Limpiar contenedor antes de re-renderizar
        if (lists.length === 0) {
            // Podríamos mostrar un mensaje "No hay listas aún" si quisiéramos
            // initialListCreatorDiv.style.display = 'block'; // Asegurar que se muestre si está vacío
            // mainListForm.style.display = 'none';
        } else {
            // initialListCreatorDiv.style.display = 'none'; // Ocultar si hay listas
            mainListForm.style.display = 'flex'; // Mostrar formulario principal
        }

        lists.forEach(list => {
            const listCard = document.createElement('div');
            listCard.classList.add('list-card');
            listCard.dataset.listId = list.id;

            // Actualizar estado de la lista (completada, vencida, pendiente)
            updateListStatus(list.id); // Asegura que el estado esté correcto antes de renderizar
            listCard.classList.add(`status-${list.status}`);


            // --- Cabecera de la Lista ---
            const listHeader = document.createElement('div');
            listHeader.classList.add('list-header');

            const listTitle = document.createElement('h3');
            listTitle.textContent = list.name;

            const listControls = document.createElement('div');
            listControls.classList.add('list-controls');

            // Botón Editar (funcionalidad básica: renombrar)
            // const editBtn = document.createElement('button');
            // editBtn.innerHTML = '<i class="fas fa-pencil-alt"></i>';
            // editBtn.classList.add('btn', 'btn-edit');
            // editBtn.title = "Editar nombre de lista";
            // editBtn.addEventListener('click', () => editListName(list.id));
            // listControls.appendChild(editBtn); // Descomentar para añadir botón editar

            // Botón Eliminar Lista
            const deleteBtn = document.createElement('button');
            deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
            deleteBtn.classList.add('btn', 'btn-danger');
            deleteBtn.title = "Eliminar lista";
            deleteBtn.addEventListener('click', () => deleteList(list.id));
            listControls.appendChild(deleteBtn);

            listHeader.appendChild(listTitle);
            listHeader.appendChild(listControls);
            listCard.appendChild(listHeader);

             // --- Mostrar Fecha de Vencimiento ---
            if (list.dueDate) {
                const dueDateEl = document.createElement('span');
                dueDateEl.classList.add('list-due-date');
                dueDateEl.textContent = `Vence: ${list.dueDate}`;
                // Marcar si está vencida y no completada
                if (list.status === 'overdue') {
                     dueDateEl.classList.add('overdue-date');
                     dueDateEl.textContent += ' (Vencida)';
                } else if (list.dueDate === getTodayDateString() && list.status !== 'completed') {
                     dueDateEl.textContent += ' (Hoy)';
                }
                listCard.appendChild(dueDateEl);
            }


            // --- Lista de Tareas ---
            const taskListUl = document.createElement('ul');
            taskListUl.classList.add('task-list');
            list.tasks.forEach(task => {
                const taskItem = document.createElement('li');
                taskItem.classList.add('task-item');
                taskItem.dataset.taskId = task.id;
                if (task.completed) {
                    taskItem.classList.add('completed');
                }

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = task.completed;
                checkbox.title = task.completed ? "Marcar como pendiente" : "Marcar como completada";
                checkbox.addEventListener('change', () => toggleTaskComplete(list.id, task.id));

                const taskText = document.createElement('span');
                taskText.textContent = task.text;

                const deleteTaskBtn = document.createElement('button');
                deleteTaskBtn.innerHTML = '<i class="fas fa-times"></i>';
                deleteTaskBtn.classList.add('delete-task-btn');
                deleteTaskBtn.title = "Eliminar tarea";
                deleteTaskBtn.addEventListener('click', () => deleteTask(list.id, task.id));

                taskItem.appendChild(checkbox);
                taskItem.appendChild(taskText);
                taskItem.appendChild(deleteTaskBtn);
                taskListUl.appendChild(taskItem);
            });
            listCard.appendChild(taskListUl);

            // --- Formulario para Añadir Nueva Tarea ---
            const addTaskForm = document.createElement('form');
            addTaskForm.classList.add('add-task-form');
            addTaskForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const taskInput = addTaskForm.querySelector('input[type="text"]');
                const taskText = taskInput.value.trim();
                if (taskText) {
                    addTask(list.id, taskText);
                    taskInput.value = ''; // Limpiar input
                }
            });

            const taskInput = document.createElement('input');
            taskInput.type = 'text';
            taskInput.placeholder = 'Añadir nueva tarea...';
            taskInput.required = true;

            const addTaskBtn = document.createElement('button');
            addTaskBtn.type = 'submit';
            addTaskBtn.innerHTML = '<i class="fas fa-plus"></i> Añadir';
            addTaskBtn.classList.add('btn', 'btn-secondary', 'btn-small');

            addTaskForm.appendChild(taskInput);
            addTaskForm.appendChild(addTaskBtn);
            listCard.appendChild(addTaskForm);

            // --- Añadir Tarjeta al Contenedor ---
            listsContainer.appendChild(listCard);
        });

        // Actualizar el resumen de estado después de renderizar
        renderStatusOverview();
    };

    // Renderizar el resumen de estado
    const renderStatusOverview = () => {
        const counts = { pending: 0, completed: 0, overdue: 0 };
        const today = getTodayDateString();

        lists.forEach(list => {
            // Re-evaluar estado aquí por si acaso (aunque updateListStatus debería hacerlo)
             const isOverdue = list.dueDate && list.dueDate < today && list.status !== 'completed';
             const isCompleted = list.status === 'completed'; // Asumiendo que updateListStatus lo maneja

            if (isCompleted) {
                counts.completed++;
            } else if (isOverdue) {
                counts.overdue++;
                 list.status = 'overdue'; // Asegurar estado
            } else {
                counts.pending++;
                 if (list.status !== 'pending') list.status = 'pending'; // Asegurar estado
            }
        });

        statusOverview.pending.textContent = counts.pending;
        statusOverview.completed.textContent = counts.completed;
        statusOverview.overdue.textContent = counts.overdue;

        // Pequeña animación al actualizar contadores (opcional)
        Object.values(statusOverview).forEach(el => {
            el.style.transform = 'scale(1.1)';
            setTimeout(() => el.style.transform = 'scale(1)', 150);
        });
    };


    // --- FUNCIONES CRUD y LÓGICA ---

    // Añadir nueva lista
    const addList = (name, dueDate) => {
        const newList = {
            id: generateId(),
            name: name,
            createdAt: new Date().toISOString(),
            dueDate: dueDate || null, // Guardar como null si está vacío
            status: 'pending', // Estados: pending, completed, overdue
            tasks: []
        };
        lists.push(newList);
        saveLists();
        renderLists();

        // Ocultar creador inicial y mostrar el principal si es la primera lista
        if (lists.length === 1) {
            initialListCreatorDiv.style.display = 'none';
            mainListForm.style.display = 'flex';
        }
    };

    // Eliminar lista
    const deleteList = (listId) => {
        if (confirm('¿Estás seguro de que quieres eliminar esta lista y todas sus tareas?')) {
            lists = lists.filter(list => list.id !== listId);
            saveLists();
            renderLists();
             // Si se eliminó la última lista, mostrar el creador inicial de nuevo
             if (lists.length === 0) {
                 initialListCreatorDiv.style.display = 'block';
                 mainListForm.style.display = 'none';
             }
        }
    };

    // Editar nombre de lista (Ejemplo básico)
    // function editListName(listId) {
    //     const list = lists.find(l => l.id === listId);
    //     if (!list) return;
    //     const newName = prompt("Introduce el nuevo nombre para la lista:", list.name);
    //     if (newName && newName.trim() !== '') {
    //         list.name = newName.trim();
    //         saveLists();
    //         renderLists();
    //     }
    // }


    // Añadir tarea a una lista
    const addTask = (listId, taskText) => {
        const list = lists.find(l => l.id === listId);
        if (list) {
            const newTask = {
                id: generateId(),
                text: taskText,
                completed: false
            };
            list.tasks.push(newTask);
            updateListStatus(listId); // Actualizar estado de la lista
            saveLists();
            renderLists(); // Re-renderizar para mostrar la nueva tarea
        }
    };

    // Eliminar tarea de una lista
    const deleteTask = (listId, taskId) => {
        const list = lists.find(l => l.id === listId);
        if (list) {
            list.tasks = list.tasks.filter(task => task.id !== taskId);
            updateListStatus(listId); // Actualizar estado de la lista
            saveLists();
            renderLists();
        }
    };

    // Marcar/desmarcar tarea como completada
    const toggleTaskComplete = (listId, taskId) => {
        const list = lists.find(l => l.id === listId);
        if (list) {
            const task = list.tasks.find(t => t.id === taskId);
            if (task) {
                task.completed = !task.completed;
                updateListStatus(listId); // El estado de la lista puede cambiar
                saveLists();
                renderLists(); // Re-renderizar para reflejar el cambio visual
            }
        }
    };

    // Actualizar el estado general de una lista (pending, completed, overdue)
    const updateListStatus = (listId) => {
        const list = lists.find(l => l.id === listId);
        if (!list) return;

        const allTasksCompleted = list.tasks.length > 0 && list.tasks.every(task => task.completed);
        const today = getTodayDateString();
        const isOverdue = list.dueDate && list.dueDate < today;

        if (allTasksCompleted) {
            list.status = 'completed';
        } else if (isOverdue) {
            list.status = 'overdue';
        } else {
            list.status = 'pending';
        }
        // Nota: No llamamos a saveLists/renderLists aquí directamente
        // porque esta función suele ser llamada *antes* de guardar y renderizar
        // en las funciones que modifican tareas o la propia lista.
    };


    // --- EVENT LISTENERS INICIALES ---

    initialListForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = initialListNameInput.value.trim();
        const dueDate = initialListDueDateInput.value;
        if (name) {
            addList(name, dueDate);
            initialListNameInput.value = '';
            initialListDueDateInput.value = ''; // Limpiar fecha también
        }
    });

    mainListForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = mainListNameInput.value.trim();
        const dueDate = mainListDueDateInput.value;
        if (name) {
            addList(name, dueDate);
            mainListNameInput.value = '';
            mainListDueDateInput.value = ''; // Limpiar fecha
        }
    });


    // --- INICIALIZACIÓN ---
    loadLists(); // Cargar listas al inicio
    renderLists(); // Renderizar el estado inicial

});