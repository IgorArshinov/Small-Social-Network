"use strict";

const url = 'http://172.30.244.151/small-social-network/api/';
window.addEventListener("load", handleWindowLoad);

function handleWindowLoad() {

    let btnGETMessages = document.getElementById("buttonGetMessages");
    btnGETMessages.addEventListener("click", handleClickGetAllMessages);
    let btnAddMessage = document.getElementById("buttonAddMessage");
    btnAddMessage.addEventListener("click", handleClickAddMessage);
    let btnGetMessageById = document.getElementById("buttonGetMessageById");
    btnGetMessageById.addEventListener("click", handleClickGetMessageById);

}

function handleClickGetAllMessages() {

    let uri = 'messages/';
    let promise = getMethod(url + uri);
    printAllMessages(promise);

}

function getMethod(url) {
    let promise = new Promise((resolve, reject) => {

        fetch(url, {method: "GET"})
            .then((response) => {
                if (response.ok) {
                    resolve(response.json());
                } else {
                    reject("rejected:" + response.status);
                }
            }).catch(exception => {
            reject("exception: " + exception);
        });
    });
    return promise;
}

function postMethod(url, messageText) {
    let promise = new Promise((resolve, reject) => {

        if (!(typeof messageText == 'string' && messageText.length >= 2)) {
            reject("Bericht moet een string met minstens 2 karakters zijn.");
        }

        let newMessage = {message: messageText};

        fetch(url, {method: "POST", body: JSON.stringify(newMessage)})
            .then((response) => {
                if (response.ok) {

                    resolve(response.json());
                } else {

                    reject("rejected:" + response.status);
                }
            }).catch(exception => {
            reject("exception: " + exception);
        });
    });

    return promise;
}

function handleClickGetMessageById() {

    let id = document.getElementById("messageId").value;
    let uri = 'messages/' + id;
    let promise = getMethod(url + uri);
    printMessage(promise);

}

function handleClickAddMessage() {

    let messageText = document.getElementById("messageText").value;
    let uri = 'messages/';
    let promise = postMethod(url + uri, messageText);
    printMessage(promise);
}

function printAllMessages(promise) {

    promise.then(messages => {
        let data = {};
        data.messages = messages;

        let numberOfMessages = data.messages.length;

        for (var i = 0; i < numberOfMessages; i++) {

            let message = data.messages[i];
            let trElement = document.createElement("tr");
            document.getElementById('outputMessages').appendChild(trElement);
            createTableData(message.id, 'outputMessages');
            createTableData(message.message, 'outputMessages');
            createTableData(message.createdOn, 'outputMessages');

        }

    }).catch(exception => {

        let trElement = document.createElement("tr");
        document.getElementById('outputError').appendChild(trElement);
        createTableData(exception, 'outputError');
    });
}

function createTableData(data, idOfElement) {
    var tdElement = document.createElement("td");
    tdElement.appendChild(document.createTextNode(data));
    document.getElementById(idOfElement).appendChild(tdElement);
}

function printMessage(promise) {

    promise.then(message => {

        let trElement = document.createElement("tr");
        document.getElementById('outputMessages').appendChild(trElement);
        createTableData(message.id, 'outputMessages');
        createTableData(message.message, 'outputMessages');
        createTableData(message.createdOn, 'outputMessages');

    }).catch(exception => {

        let trElement = document.createElement("tr");
        document.getElementById('outputError').appendChild(trElement);
        createTableData(exception, 'outputError');
    });
}