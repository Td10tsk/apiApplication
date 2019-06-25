// if pagination isn't empty we modify menu
if(emptyPagination !== 2){
    changeMenu();
    getDocumentList();
}

// function search for cookie name and return it if exist or undefined if not
function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

// AJAX request for specific document
function getDocumentList() {
    let request = new XMLHttpRequest();
    let authString = getCookie('authorization');
    request.open('GET', './api/v1/document/?page=' + page + '&perPage=' + perPage);
    if(authString !== undefined){
        request.setRequestHeader('authorization', authString);
    };
    request.responseType = 'json';
    request.send();
    request.onload = function () {
        if(request.response === 'Bad Authorization' ){
            alert('Bad Authorization');
            return null;
        };
        let documents = request.response;
        showDocuments(documents);
    }
}

//change main menu if pagination is not empty
function changeMenu(){
    document.getElementById('dataList').style.display = 'table';
    document.querySelector('div.content').classList.add('top-left');
    document.querySelector('div.title').style.display = 'inline-block';
    document.querySelector('div.links').style.display = 'inline-block';
    document.querySelector('div.title').classList.add('title-small');
    document.querySelector('div.title-small').classList.remove('title');
}

//render document to page
function showDocuments(jsonObj) {
    let tbody = document.querySelector('table.table.table-hover tbody');
    tbody.innerHTML = "";
    let documents = jsonObj['document'];
    let pagination = jsonObj['pagination'];

    for (let i = 0; i < documents.length; i++) {
        let row = document.createElement('tr');
        let id = document.createElement('th');
        let status = document.createElement('th');
        let payload = document.createElement('th');
        let createAt = document.createElement('th');
        let modifyAt = document.createElement('th');

        id.innerHTML = '<a href="document/' + documents[i].id + '">'+ documents[i].id +'</a>';
        status.textContent = documents[i].status;
        payload.textContent = (documents[i].payload).substring(0, 15) + "...";
        createAt.textContent = documents[i].createAt;
        modifyAt.textContent = documents[i].modifyAt;

        row.appendChild(id);
        row.appendChild(status);
        row.appendChild(payload);
        row.appendChild(createAt);
        row.appendChild(modifyAt);

        tbody.append(row);
    }

    let rowMenu = document.createElement('tr');
    let colMenu = document.createElement('th');
    colMenu.setAttribute('colspan','5');
    colMenu.setAttribute('style','text-align: center;');
    colMenu.classList.add('links');
    rowMenu.append(colMenu);
    tbody.append(rowMenu);
    if(pagination.page > 1){
        colMenu.innerHTML = '<a href="?page=' + Number(page-1) + '&perPage='+ perPage +'">prev</a>';
    }
    if(pagination.page * pagination.perPage < pagination.total){
        colMenu.innerHTML += '<a href="?page=' + Number(page+1) + '&perPage='+ perPage +'">next</a>';
    }
}

var modal = document.getElementById('modal');
var account = document.getElementById('account');
var send = document.getElementById('send');
var login = document.getElementById('login');
var auth = document.getElementById('auth');
var removeToken = document.getElementById('removeToken');

// remove user token
removeToken.onclick = function() {
    document.cookie = 'authorization=; Max-Age=-99999999;';
}

// get token on click
send.onclick = function() {
    getAccount();
}

//show modal
account.onclick = function() {
    modal.style.display = 'block';
}

//get token for account name
function getAccount(){
    let request = new XMLHttpRequest();
    request.open('POST', './api/v1/login');
    request.setRequestHeader('Content-Type', 'application/json');
    request.responseType = 'json';
    request.send('{"login": "' + login.value + '"}');
    request.onload = function () {
        auth.textContent = JSON.stringify(request.response.token);
        document.cookie = 'authorization=' + request.response.user + ' ' + request.response.token;
    }
}

// hide modal
window.onclick  = function(event) {
    if(event.target === modal) {
        modal.style.display = 'none';
    }
}