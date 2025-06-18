$(document).ready(function() {
    //  Если уже авторизован, сразу на задачи 
    if (localStorage.getItem('api_token')) {
        window.location.href = 'to-do-task.html';
        return;
    }

    //  Универсальная функция отправки формы с валидацией 
    function handleFormSubmit(formSelector, url, getData, successMsg) {
        $(formSelector).submit(function(e) {
            e.preventDefault();

            const form = this;
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            form.classList.remove('was-validated');

            const data = getData();

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(res) {

                    localStorage.setItem('api_token', res.data.token);
                    window.location.href = 'to-do-task.html';
                },
                error: function(res) {
                    alert((successMsg || 'Ошибка') + ': ' + (res.responseJSON?.message || 'Неизвестная ошибка'));
                }
            });
        });
    }

    //  Авторизация 
    handleFormSubmit(
        '#loginForm',
        'http://localhost:8000/api/login',
        function() {
            return {
                name: $('#logInputLogin').val(),
                password: $('#logInputPassword').val()
            };
        },
        'Ошибка при авторизации'
    );

    //  Регистрация 
    handleFormSubmit(
        '#regForm',
        'http://localhost:8000/api/register',
        function() {
            return {
                email: $('#regInputEmail').val(),
                name: $('#regInputLogin').val(),
                password: $('#regInputPassword').val()
            };
        },
        'Ошибка при регистрации'
    );
});
