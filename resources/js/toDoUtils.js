$(document).ready(function () {
    //  Проверка авторизации 
    if (!localStorage.getItem('api_token')) {
        window.location.href = 'index.html';
        return;
    }

    //  Глобальные переменные 
    let id = 0;
    let tag_delete_id = 0;
    let task_id = 0;
    let tag_id = 0;

    //  Навигация и начальная загрузка 
    const pathname = window.location.pathname;
    if (pathname.endsWith('/to-do-task.html')) {
        taskLoad();
        tagListLoad();
    } else if (pathname.endsWith('/to-do-tag.html')) {
        tagLoad();
    }

    //  Универсальная функция для AJAX-запросов 
    function sendAjax({ url, method = 'GET', data = {}, success, error }) {
        $.ajax({
            url,
            method,
            headers: { 'Authorization': localStorage.getItem('api_token') },
            data,
            success,
            error: error || function (res) {
                alert(res.responseJSON?.message || 'Неизвестная ошибка');
            }
        });
    }

    //  Загрузка всех тэгов для выпадающего списка 
    function tagListLoad() {
        sendAjax({
            url: 'http://localhost:8000/api/tags',
            success: function (res) {
                const $dropdown = $('.dropdown-menu').empty();
                res.forEach(function (tag) {
                    $dropdown.append(`<li><a class="dropdown-item tag-dropdown-item" href="#" data-tag-id="${tag.id}">${tag.title}</a></li>`);
                });
            }
        });
    }

    //  Загрузка всех задач с drag & drop 
    function taskLoad() {
        sendAjax({
            url: 'http://localhost:8000/api/tasks',
            success: function (res) {
                const $main = $('.task-main').empty();
                res.forEach(function (task) {
                    let taskHtml = `
                        <div class="card hover-zoom mb-3" data-id="${task.id}" style="cursor: grab;">
                            <div class="card-body">
                                <h5 class="card-title">${task.title}</h5>
                                <p class="card-text">${task.text || ''}</p>
                                <button class="btn btn-warning task_edit" id="edit_${task.id}" data-bs-toggle="modal" data-bs-target="#taskModalEdit">Редактировать</button>
                                <button class="btn btn-danger task_remove" id="remove_${task.id}">Удалить</button>
                                <div class="task-tags-list mt-2" id="tags_${task.id}">
                                    ${task.tags.map(tag => `
                                        <span class="badge bg-primary me-1">
                                            ${tag.tag}
                                            <button class="remove_tag btn btn-danger btn-xs btn-sm py-0 px-1 ms-1" id="tag_${tag.id}_${task.id}">x</button>
                                        </span>
                                    `).join('')}
                                </div>
                                <button class="add btn btn-primary btn-xs mt-2" id="${task.id}" data-bs-toggle="modal" data-bs-target="#tagModal">+</button>
                            </div>
                        </div>
                    `;
                    $main.append(taskHtml);
                });

                // Drag & Drop для задач
                if (window.Sortable) {
                    Sortable.create(document.querySelector('.task-main'), {
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function (evt) {
                            let order = [];
                            document.querySelectorAll('.task-main .card').forEach(el => {
                                order.push(el.getAttribute('data-id'));
                            });
                        }
                    });
                }
            }
        });
    }

    //  Загрузка всех тэгов с drag & drop 
    function tagLoad() {
        sendAjax({
            url: 'http://localhost:8000/api/tags',
            success: function (res) {
                const $main = $('.tag-main').empty();
                res.forEach(function (tag) {
                    let tagHtml = `
                        <div class="card hover-zoom mb-3" data-id="${tag.id}" style="width: 200px; cursor: grab;">
                            <div class="card-body">
                                <h5 class="card-title">${tag.title}</h5>
                                <button class="btn btn-warning tag_edit" id="edit_${tag.id}" data-bs-toggle="modal" data-bs-target="#tagModalEdit">Редактировать</button>
                                <button class="btn btn-danger tag_remove" id="remove_${tag.id}" data-bs-toggle="modal" data-bs-target="#tagModalDelete">Удалить</button>
                            </div>
                        </div>
                    `;
                    $main.append(tagHtml);
                });

                // Drag & Drop для тегов
                if (window.Sortable) {
                    Sortable.create(document.querySelector('.tag-main'), {
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function (evt) {
                            let order = [];
                            document.querySelectorAll('.tag-main .card').forEach(el => {
                                order.push(el.getAttribute('data-id'));
                            });
                        }
                    });
                }
            }
        });
    }

    //  Удаление задачи 
    $(document).on('click', '.task_remove', function (e) {
        e.preventDefault();
        let taskId = e.currentTarget.id.split('_')[1];
        if (confirm('Вы уверены, что хотите удалить задачу?')) {
            sendAjax({
                url: `http://localhost:8000/api/task/${taskId}`,
                method: 'DELETE',
                success: function () {
                    taskLoad();
                }
            });
        }
    });

    //  Удаление тэга и всех его связей 
    $(document).on('click', '.tag_remove', function (e) {
        tag_delete_id = e.currentTarget.id.split('_')[1];
        $('#tagModalDelete').modal('show');
    });

    $('#deleteTag').click(function (e) {
        e.preventDefault();
        sendAjax({
            url: `http://localhost:8000/api/tag/${tag_delete_id}`,
            method: 'DELETE',
            success: function () {
                $('#tagModalDelete').modal('hide');
                tagLoad();
            }
        });
    });

    //  Получение данных для редактирования задачи и тэга 
    $(document).on('click', '.task_edit', function (e) {
        id = e.currentTarget.id.split('_')[1];
        sendAjax({
            url: `http://localhost:8000/api/task/${id}`,
            success: function (task) {
                $('#taskInputTitleEdit').val(task.title);
                $('#taskInputTextEdit').val(task.text);
            }
        });
    });

    $(document).on('click', '.tag_edit', function (e) {
        id = e.currentTarget.id.split('_')[1];
        sendAjax({
            url: `http://localhost:8000/api/tag/${id}`,
            success: function (tag) {
                $('#tagInputTitleEdit').val(tag.title);
            }
        });
    });

    //  Добавление тэга к задаче 
    $(document).on('click', '.add', function (e) {
        task_id = e.currentTarget.id;
        $('#tagModal').modal('show');
    });

    $(document).on('click', '.tag-dropdown-item', function (e) {
        e.preventDefault();
        tag_id = $(this).data('tag-id');
        $('#dropdownMenuButton').text($(this).text());
    });

    $('#tagAdd').click(function (e) {
        e.preventDefault();
        if (!task_id || !tag_id) {
            alert('Выберите задачу и тэг!');
            return;
        }
        sendAjax({
            url: `http://localhost:8000/api/task_tag`,
            method: 'POST',
            data: { task_id, tag_id },
            success: function () {
                $('#tagModal').modal('hide');
                taskLoad();
                task_id = 0;
                tag_id = 0;
                $('#dropdownMenuButton').text('Выберите вариант');
            }
        });
    });

    //  Удаление тэга у задачи 
    $(document).on('click', '.remove_tag', function (e) {
        e.preventDefault();
        let [_, tagId, taskId] = e.currentTarget.id.split('_');
        sendAjax({
            url: `http://localhost:8000/api/task_tag`,
            method: 'DELETE',
            data: { task_id: taskId, id: tagId },
            success: function () {
                taskLoad();
            }
        });
    });

    //  Универсальная функция для валидации и отправки формы 
    function validateAndSend(formId, ajaxOptions) {
        const form = document.getElementById(formId);
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        form.classList.remove('was-validated');
        sendAjax(ajaxOptions);
    }

    //  Редактирование задачи 
    $('#saveTaskEdit').click(function (e) {
        e.preventDefault();
        validateAndSend('taskFormEdit', {
            url: `http://localhost:8000/api/task/${id}`,
            method: 'PUT',
            data: {
                title: $('#taskInputTitleEdit').val(),
                text: $('#taskInputTextEdit').val()
            },
            success: function () {
                $('#taskModalEdit').modal('hide');
                $('#taskFormEdit')[0].reset();
                taskLoad();
                id = 0;
            }
        });
    });

    //  Редактирование тэга 
    $('#saveTagEdit').click(function (e) {
        e.preventDefault();
        validateAndSend('tagFormEdit', {
            url: `http://localhost:8000/api/tag/${id}`,
            method: 'PUT',
            data: { title: $('#tagInputTitleEdit').val() },
            success: function () {
                $('#tagModalEdit').modal('hide');
                $('#tagFormEdit')[0].reset();
                tagLoad();
                id = 0;
            }
        });
    });

    //  Создание задачи 
    $('#saveTask').click(function (e) {
        e.preventDefault();
        validateAndSend('taskForm', {
            url: 'http://localhost:8000/api/task',
            method: 'POST',
            data: {
                title: $('#taskInputTitle').val(),
                text: $('#taskInputText').val()
            },
            success: function () {
                $('#taskModal').modal('hide');
                $('#taskForm')[0].reset();
                taskLoad();
            }
        });
    });

    //  Создание тэга 
    $('#saveTag').click(function (e) {
        e.preventDefault();
        validateAndSend('tagForm', {
            url: 'http://localhost:8000/api/tag',
            method: 'POST',
            data: { title: $('#tagInputTitle').val() },
            success: function () {
                $('#tagModal').modal('hide');
                $('#tagForm')[0].reset();
                tagLoad();
            }
        });
    });

    //  Выход 
    $('#logout').click(function () {
        localStorage.removeItem('api_token');
        window.location.reload();
    });
});
