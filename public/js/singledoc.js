function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

//AJAx request for single document, check authorization
function getDocument() {
    let request = new XMLHttpRequest();
    let authString = getCookie('authorization');
    request.open('GET', '../api/v1/document/' + documentId);
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
        let data = request.response;
        showDocument(data);
    }
}

//render document
function showDocument(jsonObj){
    if(typeof jsonObj['document'] === 'undefined'){
        let container = document.getElementById('documentData');
        let payload = document.createElement('div');
        payload.textContent = 'Document not found';
        container.appendChild(payload);
        return null;
    }
    let data = jsonObj['document'];
    let container = document.getElementById('documentData');
    let id = document.createElement('div');
    let status = document.createElement('div');
    let payload = document.createElement('div');
    let createAt = document.createElement('div');
    let modifyAt = document.createElement('div');

    id.textContent = data.id;
    status.textContent = data.status;
    payload.textContent = data.payload;
    createAt.textContent = data.createAt;
    modifyAt.textContent = data.modifyAt;

    container.appendChild(id);
    container.appendChild(status);
    container.appendChild(payload);
    container.appendChild(createAt);
    container.appendChild(modifyAt);
}

getDocument();