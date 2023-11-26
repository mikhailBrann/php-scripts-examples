class User {
    constructor(url) {
        this.url = document.location.href + url + '.php';
    }

    //метод добавляет пользователя в БД
    addUser(inputData) {
        fetch(this.url,
            {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify({
                    'responseType': 'registration',
                    'inputData': inputData
                })
            })
            .then(response => {
                if (response.status !== 200) {
                    return Promise.reject();
                }

                return response.json();
            })
            .then(function (data) {
                    if (data.registr_status == 'error') {
                        alert(data.error_message);
                    }

                    if (data.registr_status == 'ok') {
                        alert('Пользователь "' + data.name + '" успешно зарегистрирован! Пожалуйста пройдите авторизацию');
                        let authBtn = document.querySelector('.tabs__head [data-number="2"]');
                        authBtn.click();
                    }
                }
            )
    }

    //метод авторизует пользователя
    authUser(inputData) {
        fetch(this.url,
            {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify({
                    'responseType': 'login',
                    'inputData': inputData
                })
            })
            .then(response => {
                if (response.status !== 200) {
                    return Promise.reject();
                }

                return response.json();
            })
            .then(function (data) {
                    if (data.registr_status == 'error') {
                        alert(data.error_message);
                    }

                    if (data.registr_status == 'ok') {
                        location.reload();
                    }
                }
            )
    }

    //метод разлогинивает пользователя
    logoutUser() {
        fetch(this.url)
                .then(
                    response => response.json()
                .then((response) => {
                    if(response.registr_status == 'logout') {
                        console.log(response.registr_status);
                        location.reload();
                    }
                })
            );
    }
}

//functions
function checkFields(submitBtn, wrapClass, inputValName) {
    let parent = submitBtn.closest(wrapClass);
    let fields = Array.from(parent.querySelectorAll('input'));
    let fieldsIsEmpty = true;
    let inputData = {};

    fields.map((input) => {
        if (input.value == '') {
            input.classList.add('empty');
            input.setAttribute('placeholder', 'Введите значение');
            fieldsIsEmpty = false;
        } else {
            input.classList.remove('empty');
            inputData[input.dataset[inputValName]] = input.value;
        }
    });

    return {
       'checkFields': fieldsIsEmpty,
        'fieldValue': inputData
    };
}


//tabs
let tabsChecker = Array.from(document.querySelectorAll('.tabs__head > div'));
let tabsBody = Array.from(document.querySelectorAll('.tabs__body > div'));

if (tabsChecker.length > 1) {
    tabsChecker.map((checker) => {
        checker.addEventListener('click', (event) => {
            if (!event.currentTarget.classList.contains('active')) {
                tabsChecker.map(elem => elem.classList.remove('active'));
                event.currentTarget.classList.add('active');
                tabsBody.map(elem => elem.dataset.number == event.currentTarget.dataset.number ? elem.classList.add('active') : elem.classList.remove('active'));
            }
        });
    });
}


//registration
let registrationBtn = document.querySelector('.registration__submit');

if (registrationBtn) {
    registrationBtn.addEventListener('click', () => {
        let result = checkFields(registrationBtn, '.registration', 'registration');

        if (result.checkFields) {
            let registration = new User('registration');
            registration.addUser(result.fieldValue);
        }
    });
}

//login
let loginBtn = document.querySelector('.login__submit');

if (loginBtn) {
    loginBtn.addEventListener('click', () => {
        let result = checkFields(loginBtn, '.login', 'login');

        if (result.checkFields) {
            let login = new User('login');
            login.authUser(result.fieldValue);
        }
    });
}

//logout
let logoutBtn = document.querySelector('.logout__submit');

if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        let logout = new User('logout');
        logout.logoutUser();
    });
}