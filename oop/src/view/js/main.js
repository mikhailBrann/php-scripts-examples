class DiskApi {
    constructor() {
        this.url = document.location.href + 'ajax/ajax.php';
    }

    sendFile(data, currentDir,fileName, discType='yandex') {

        const params = {
            method: 'POST',
            body: data
        }

        const requestResult = fetch(this.url + '?send_file=true&file_name=' + fileName + '&cur_dir=' + currentDir + '&disk_type=' + discType, params)
            .then(response => {
                if (response.ok) {
                    if (response.status == 204) {
                        return {
                            status: 'ok'
                        }
                    }

                    return response.json();
                } else {
                    return Promise.reject({ status: response.status});
                }
            });

        return requestResult;
    }

    getResponse(
        responseBody={},
        renderObj={},
        previosPath=false,
        reloadPage=false,
        responseType='POST'
    ) {
        const params = {
            method: responseType,
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(responseBody)
        };

        const requestResult = fetch(this.url, params)
            .then(response => {
                if (response.ok) {

                    if (response.status == 204) {
                        return {
                            status: 'ok'
                        }
                    }

                    return response.json(); 
                } else {
                    return Promise.reject({ status: response.status});
                }
            });

        return requestResult;
    }

    renderList(renderObj={}, responseBody={}, previosPath) {
        // логика для кнопки назад
        localStorage.previosBtn = (previosPath || previosPath == 'false') ? previosPath : '/';
        renderObj.previosBtn = localStorage.previosBtn;

        renderObj.limit = false;
        renderObj.filesCount = false;
        renderObj.responseLoad = true;
        let result = this.getResponse(responseBody, renderObj, previosPath);

        result.then(function(data) {
            if (data.status <= 299) {
                renderObj.fileList = data.getList;
                renderObj.currentDir = data.currentPath;
                renderObj.limit = data.limit;
                renderObj.filesCount = data.total;
                renderObj.responseLoad = false;
                renderObj.offsetPos = data.offset;
                localStorage.offsetPaginationPos = data.offset;

                // активация кнопки назад
                if (localStorage.previosBtn == localStorage.currentDir) {
                    renderObj.prevBtnActive = false;
                } else {
                    renderObj.prevBtnActive = true;
                }
            }
        });
    }
}

function closeContextMenu(event, static=false) {
    if (event.target.classList.contains('loaders__view') || static) {
        this.contextMenu.active = false;
        this.contextMenu.createDir.btn = false;
        this.contextMenu.uploadFile.btn = false;
        this.contextMenu.deleteFileOrFolder.btn = false;
        this.contextMenu.rename.btn = false;
        this.contextMenu.download.btn = false;
        this.contextMenu.createDir.form = false;
        this.contextMenu.rename.form = false;
    }
}

function clickToElem(event) {
    let requestObject = new DiskApi;
    let renderObject = this;
    let clickElem = event.currentTarget;
    let url = renderObject.fileList[clickElem.dataset.id];

    if (url.type == 'dir') {
        renderObject.fileList = false;
        requestObject.renderList(renderObject, {diskType: 'yandex', responseType: 'getList', responsePath: url.path},  renderObject.currentDir);
        localStorage.currentDir = url.path;
        localStorage.currentDirName = url.name;
    }
}

function paginate(event) {
    let renderDisk = new DiskApi;
    let renderObject = this;
    let offset = event.currentTarget.dataset.start;
    renderObject.fileList = false;

    let result = renderDisk.renderList(renderObject, {diskType: 'yandex', responseType: 'getList', offset: offset,  responsePath: renderObject.currentDir});
}

function backToDir() {
    let renderObject = this;
    let requestObject = new DiskApi;

    renderObject.fileList = false;
    localStorage.currentDir = renderObject.previosBtn;
    requestObject.renderList(renderObject, {diskType: 'yandex', responseType: 'getList', responsePath: renderObject.previosBtn});
}

// работа с контекстным меню правой кнопкм мыши
function showContextMenu(event) {
    this.contextClose(event, static=true);

    this.contextMenu.active = true;
    this.contextMenu.menuPosition.y = event.clientY;
    this.contextMenu.menuPosition.x = event.clientX;

    if (!event.target.closest('.loaders__item') || event.target.classList.contains('loaders__view-container')) {
        this.contextMenu.createDir.btn = true;
        this.contextMenu.uploadFile.btn = true;
        this.contextMenu.deleteFileOrFolder.btn = false;
        this.contextMenu.rename.btn = false;
        this.contextMenu.download.btn = false;

    } else if (event.target.classList.contains('loaders__item') || event.target.closest('.loaders__item')) {
        this.contextMenu.deleteFileOrFolder.btn = true;
        this.contextMenu.rename.btn = true;
        this.contextMenu.createDir.btn = false;
        this.contextMenu.uploadFile.btn = false;

        let elemID = event.target.closest('.loaders__item') ? event.target.closest('.loaders__item').dataset.id : event.target.dataset.id;
        this.contextMenu.deleteFileOrFolder.elemIndex = elemID;
        this.contextMenu.rename.elemIndex = elemID;

        // активация кнопки скачать в контекстном меню
        this.contextMenu.download.btn = this.fileList[elemID].type != 'dir' ? true : false;
        this.contextMenu.download.elemIndex = elemID;

    }
}


function renameFileOrOrder(event) {
    let parent = event.currentTarget.closest('.js-context-menu__item');
    let submitBtn = parent.querySelector('button');
    let renderObj = this;
    let requestObject = new DiskApi;

    renderObj.contextMenu.rename.form = renderObj.contextMenu.rename.form ? false : true;

    submitBtn.addEventListener('click', (event) => {
        let input = document.querySelector('#renameItem');
        let elemIndex = renderObj.contextMenu.rename.elemIndex;
        let elem = renderObj.fileList[elemIndex];
        let type = elem.type;
        let pathFrom = elem.path;
        // получаем отдельно имя и расширение файла
        let expansion = type != 'dir' ? /\.[^\.]*$/.exec(elem.name) : false;
        // очишаем поле ввода от лишних симоволов (только цифры, буквы, подчеркивания и пробелы) и создаем новый путь для файла
        let value = input.value.trim().replace(/([^a-zA-Zа-яА-Я0-9-_\s]+)/gm, '');
        let newPath = expansion != false ? renderObj.currentDir + value + expansion[0] : renderObj.currentDir + value;
        let result = requestObject.getResponse(responseBody={diskType: 'yandex', responseType: 'renameItem', from: pathFrom, path: newPath});

        result.then((data) => {
            if (data.result.response.status <= 299) {
                renderObj.fileList = false;
                renderObj.contextClose(event, static=true);
                requestObject.renderList(renderObj, {diskType: 'yandex', responseType: 'getList', responsePath: renderObj.currentDir});
            }
        }).catch(error => {
            if (error.status == 409) {
                renderObj.errors = `${type != 'dir' ? 'файл' : 'папка'} "${expansion ? value + expansion : value}" уже существует!`;
            }
        });
    });
}

function createFolder(event) {
    event.preventDefault();
    this.contextMenu.createDir.form = true;

    let parent = event.currentTarget.closest('.js-context-menu__item');
    let submitBtn = parent.querySelector('button');
    let renderObj = this;
    
    submitBtn.addEventListener('click', (event) => {
        let input = parent.querySelector('#createDir');
        let value = input.value.trim();
        let requestObject = new DiskApi;
        let path = renderObj.currentDir == 'disk:/' ? renderObj.currentDir + value : renderObj.currentDir  + '/' + value;
        let result = requestObject.getResponse(responseBody={diskType: 'yandex', responseType: 'addFolder', responsePath: path});

        result.then((data) => {
            if (data.result.response.status <= 299) {
                renderObj.fileList = false;
                requestObject.renderList(renderObj, {diskType: 'yandex', responseType: 'getList', responsePath: renderObj.currentDir});
                renderObj.contextClose(event, static=true);
            } 
        }).catch(error => {
            if (error.status == 409) {
                renderObj.errors = `Папка с названием ${value} уже существует.`;
            }
        });
    });
}


function deleteFileOrFolder(event) {
    let delElemIndex = this.contextMenu.deleteFileOrFolder.elemIndex;

    if (delElemIndex) {
        let type = this.fileList[delElemIndex].type == 'dir' ? 'папку' : 'файл';
        let name = this.fileList[delElemIndex].name;
        let path = this.fileList[delElemIndex].path;
        let confurmMess = `Вы точно хотите удалить ${type} "${name}"?`
        let userQuestion = confirm(confurmMess);

        if (userQuestion) {
            let requestObject = new DiskApi;
            let requestResult = requestObject.getResponse(responseBody={diskType: 'yandex', responseType: 'removeItem', responsePath: path}, renderObj=this);

            requestResult.then((data) => {
                if (data.status == 'ok') {
                    this.fileList = false;
                    requestObject.renderList(renderObj, {diskType: 'yandex', responseType: 'getList', responsePath: renderObj.currentDir});
                    this.contextClose(event, static=true);
                }
            }).catch(error => {
                if (error.status > 299) {
                    this.errors = `Ошибка при попытке удалить ${type} "${name}"!`;
                }
            });
        }
    }
}

function downloadFile(event) {
    let renderObj = this;
    let indexElem = renderObj.contextMenu.download.elemIndex;
    let elem = renderObj.fileList[indexElem];
    let requestObject = new DiskApi;
    let requestResult = requestObject.getResponse(responseBody={diskType: 'yandex', responseType: 'getItem', responsePath: elem.path});

    requestResult.then((data) => {
        let link = document.createElement("a");
        let dataJson = JSON.parse(data.result.response.data);
        let downloadLink = dataJson.href;

        link.setAttribute('rel', 'noreferrer');
        link.href = downloadLink;
        link.click();
        link.remove();
    }).catch(error => {
        if (error.status > 299) {
            renderObj.errors = `Ошибка при скачивании файла "${elem.name}"!`;
        }
    });
}

function clickUpload(event) {
    let fileField = document.querySelector('.loaders__view-wrap #loadFile');
    fileField.click();
}


function dropFile(event) {
    let dropFiled = event.currentTarget;
    let renderObj = this;
    let resultArr = [];

    Array.from(dropFiled.files).map((file, index) => {
        let fileType = file.name.match(/\.([^.]+)$/)?.[1];
        let errorMess = '';
        let renderObj = this;

        // если файл уже есть на диске
        const fileNames = this.fileList.map(file => file.name);

        if (fileNames.includes(file.name)) {
            errorMess += `| Файл с именем ${file.name} уже есть на вашем диске`;
        }

        if (typeof fileType == 'undefined') {
            errorMess += '| Папку через поле загрузки фалов добовлять нельзя';
        }

        if (fileType == 'exe' || fileType == 'sh') {
            errorMess += '| Файлы формата .exe и .sh на диск добавлять нельзя';
        }

        if ((file.size / 1024) > 10000) {
            errorMess += '| Размер файла больше 10мб';
        }


        if (errorMess != '') {
            this.errors = errorMess;
            dropFiled.value = null;
        } else {
            this.errors = false;

            // забираем файл из инпута
            let fileToSend = new FormData();
            let requestObject = new DiskApi;

            fileToSend.append('send_file', file);
            let result =requestObject.sendFile(fileToSend, renderObj.currentDir, file.name);

            // делаем копию списка загруженых файлов, чтобы не отправлять запрос в случае ошибки
            let fileListSnapshot = renderObj.fileList ? Object.assign([], renderObj.fileList) : false;

            renderObj.responseLoad = true;
            renderObj.fileList = false;
            renderObj.contextClose(event, static=true);

            result.then((data) => {
                if (data.result.response.status <= 299) {
                    requestObject.renderList(renderObj, {diskType: 'yandex', responseType: 'getList', responsePath: renderObj.currentDir});
                }
            }).catch(error => {
                renderObj.responseLoad = false;
                renderObj.fileList = fileListSnapshot;
            });

        }
    });
}


document.addEventListener('DOMContentLoaded', function() {
    let renderDisk = new DiskApi;
    let loaderView = new Vue({
        el: '.loaders__view',
        data: {
            responseLoad: true,
            fileList: false,
            limit:false,
            offsetPos:false,
            filesCount:false,
            errors: false,
            currentDir: '/',
            prevBtnActive: false,
            previosBtn: false,
            contextMenu: {
                active: false,
                createDir: {
                    btn: false,
                    form: false
                },
                uploadFile: {
                    btn: false
                },
                rename: {
                    btn: false,
                    form: false,
                    elemIndex: false
                },
                deleteFileOrFolder: {
                    btn: false,
                    elemIndex: false
                },
                download: {
                    btn: false,
                    elemIndex: false
                },
                menuPosition: {
                    y: 0,
                    x: 0
                },
                data: {
                    validateFile: false,
                    fileList: false
                },
                validate: false
            }
        },
        methods: {
            clickToFile: clickToElem,
            clickToBack: backToDir,
            contextOpen: showContextMenu,
            contextClose: closeContextMenu,
            createDir: createFolder,
            delFileOrDir: deleteFileOrFolder,
            renameItem: renameFileOrOrder,
            downloadItem: downloadFile,
            clickUploadFile: clickUpload,
            uploadedFile: dropFile,
            paginateFile: paginate
        }
    });

    let isCurDir = localStorage.previosBtn;
    let respBody = {
        diskType: 'yandex',
        responseType: 'getList'
    }

    if (isCurDir) {
        respBody.responsePath = localStorage.currentDir;
    }

    if (localStorage.offsetPaginationPos) {
        respBody.offset = localStorage.offsetPaginationPos;
    }

    renderDisk.renderList(loaderView, respBody, localStorage.previosBtn);
});
