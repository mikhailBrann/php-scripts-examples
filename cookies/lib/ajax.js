class ListCockieHendler {
    constructor() {
        this.url = document.location.href + 'lib/cookieHandler.php';
    }

    //метод создает елемент списка
    createListElem(parent, key, inputData) {
        let item = document.createElement('div');
        let text = document.createElement('output');
        let remove = document.createElement('button');

        item.classList.add('todo-list__item');
        remove.classList.add('todo-list__item-remove');
        remove.dataset.count = key;
        remove.dataset.value = inputData[key];
        remove.innerText = "X";
        text.innerText = inputData[key];
        item.appendChild(text);
        item.appendChild(remove);
        parent.appendChild(item);
    }

    //метод создает список
    createList(requestData, parent) {
        if(parent) {
            parent.innerHTML = '';

            for(let key in requestData) {
                this.createListElem(parent, key, requestData);
            }
        }
    }

    //метод рендерит список
    renderList(parent=false) {
        fetch(this.url,
            {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8'
                    },
                    body: JSON.stringify({'responseType': 'getList'})
                }
            )
            .then(
                (response) => response.json()
            .then((response) => {
                if(response.status == 'ok') {
                    this.createList(response.result, parent);
                }
            })
        );
    }

    //метод добавляет в список еще один елемент
    addItem(inputData, linkObj=this) {
        fetch(this.url,
            {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(inputData)
            })
            .then(response => {
                if (response.status >= 200 && response.status < 300) {
                    return response.json();
                } else {
                    if (response.status >= 500) {
                        let error = new Error('Некорректный ответ от сервера(Нажата галочка "Неудачный запрос"');
                        error.response = response;
                        throw error;
                    } else {
                        let error = new Error('Некорректный ответ от сервера');
                        error.response = response;
                        throw error;
                    }
                }
            })
            .then(function (data) {
                if (data.result.added == true) {
                    let renderList = document.querySelector('.todo-list__wrap');
                    linkObj.renderList(renderList);
                }
            }
            ).catch((e) => {
                console.log('Error: ' + e.message);
            })
    }

    //метод очищает список
    clearList(inputData=false) {
        if (inputData) {
            inputData = {
                'responseType': 'removeList',
                'removeCount': inputData
            }
        } else {
            inputData = {
                'responseType': 'removeList'
            }
        }

        fetch(this.url,
            {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(inputData)
            })
            .then(response => response.json())
            .then((response) => {
                if (response.result) {
                    let wrapList = document.querySelector('.todo-list__wrap');
                    let removeItemList = wrapList.querySelector('button[data-count="' + response.removeCount + '"]');

                    if (removeItemList) {
                        let removeItemListElem = removeItemList.closest('.todo-list__item');
                        wrapList.removeChild(removeItemListElem);
                    }

                }
            }
        )
    }
}


document.addEventListener('DOMContentLoaded', function(){
    let newRequest = new ListCockieHendler;
    const listSubmit = document.querySelector('.todo-list__control-submit');
    const parent = document.querySelector('.todo-list');
    const listWrapper = parent.querySelector('.todo-list__wrap');
    const clearListBtn = document.querySelector('.todo-list__clear-btn');

    //инициализируем список при загрузке страницы
    newRequest.renderList(listWrapper);

    //добавляем пункты в список
    if (listSubmit && parent) {
        listSubmit.addEventListener('click',(event) => {
            let listWrapperCount = listWrapper.querySelectorAll('output').length + 1;
            let inputFileld = parent.querySelector('.todo-list__control-field');
            let errorChecker = document.querySelector('#err_control');

            if (errorChecker.checked) {
                newRequest.addItem({ 'responseType': 'error'});
            } else {
                let requestData = {
                    'responseType': 'addItemList',
                    'userlist': {[listWrapperCount]: inputFileld.value}
                };

                if (inputFileld.value != '') {
                    let result = newRequest.addItem(requestData);
                } else {
                    alert('Вы пытаетесь добавить пустое знаечение в список!');
                }
            }

        });
    }

    //очищаем список
    if (clearListBtn) {
        clearListBtn.addEventListener('click', () => {
            let errorChecker = document.querySelector('#err_control');
            if (errorChecker.checked) {
                newRequest.addItem({ 'responseType': 'error'});
            } else {
                newRequest.clearList();
                document.querySelector('.todo-list__wrap').innerHTML = '';
            }
        });
    }

    //очищаем список по пунктам
    let checkTodoList = document.querySelector('.todo-list__wrap');

    if (checkTodoList) {
        checkTodoList.addEventListener('click', (event) => {
           if (event.target.classList.contains('todo-list__item-remove')) {
               let removeChecker = event.target;
               let errorChecker = document.querySelector('#err_control');

               if (errorChecker.checked) {
                    newRequest.addItem({ 'responseType': 'error'});
               } else {
                    removeChecker.addEventListener('click', (e)=> {
                        let count = removeChecker.dataset.count;
                        newRequest.clearList(count);
                    });

                    removeChecker.click();
               }
           }
        });
    }
});