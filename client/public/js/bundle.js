/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/public/js";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/app.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/app.js":
/*!***********************!*\
  !*** ./src/js/app.js ***!
  \***********************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar url = 'http://172.30.244.151/small-social-network/api/';\nwindow.addEventListener(\"load\", handleWindowLoad);\n\nfunction handleWindowLoad() {\n\n    var btnGETMessages = document.getElementById(\"buttonGetMessages\");\n    btnGETMessages.addEventListener(\"click\", handleClickGetAllMessages);\n    var btnAddMessage = document.getElementById(\"buttonAddMessage\");\n    btnAddMessage.addEventListener(\"click\", handleClickAddMessage);\n    var btnGetMessageById = document.getElementById(\"buttonGetMessageById\");\n    btnGetMessageById.addEventListener(\"click\", handleClickGetMessageById);\n}\n\nfunction handleClickGetAllMessages() {\n\n    var uri = 'messages/';\n    var promise = getMethod(url + uri);\n    printAllMessages(promise);\n}\n\nfunction getMethod(url) {\n    var promise = new Promise(function (resolve, reject) {\n\n        fetch(url, { method: \"GET\" }).then(function (response) {\n            if (response.ok) {\n                resolve(response.json());\n            } else {\n                reject(\"rejected:\" + response.status);\n            }\n        }).catch(function (exception) {\n            reject(\"exception: \" + exception);\n        });\n    });\n    return promise;\n}\n\nfunction postMethod(url, messageText) {\n    var promise = new Promise(function (resolve, reject) {\n\n        if (!(typeof messageText == 'string' && messageText.length >= 2)) {\n            reject(\"Bericht moet een string met minstens 2 karakters zijn.\");\n        }\n\n        var newMessage = { message: messageText };\n\n        fetch(url, { method: \"POST\", body: JSON.stringify(newMessage) }).then(function (response) {\n            if (response.ok) {\n\n                resolve(response.json());\n            } else {\n\n                reject(\"rejected:\" + response.status);\n            }\n        }).catch(function (exception) {\n            reject(\"exception: \" + exception);\n        });\n    });\n\n    return promise;\n}\n\nfunction handleClickGetMessageById() {\n\n    var id = document.getElementById(\"messageId\").value;\n    var uri = 'messages/' + id;\n    var promise = getMethod(url + uri);\n    printMessage(promise);\n}\n\nfunction handleClickAddMessage() {\n\n    var messageText = document.getElementById(\"messageText\").value;\n    var uri = 'messages/';\n    var promise = postMethod(url + uri, messageText);\n    printMessage(promise);\n}\n\nfunction printAllMessages(promise) {\n\n    promise.then(function (messages) {\n        var data = {};\n        data.messages = messages;\n\n        var numberOfMessages = data.messages.length;\n\n        for (var i = 0; i < numberOfMessages; i++) {\n\n            var message = data.messages[i];\n            var trElement = document.createElement(\"tr\");\n            document.getElementById('outputMessages').appendChild(trElement);\n            createTableData(message.id, 'outputMessages');\n            createTableData(message.message, 'outputMessages');\n            createTableData(message.createdOn, 'outputMessages');\n        }\n    }).catch(function (exception) {\n\n        var trElement = document.createElement(\"tr\");\n        document.getElementById('outputError').appendChild(trElement);\n        createTableData(exception, 'outputError');\n    });\n}\n\nfunction createTableData(data, idOfElement) {\n    var tdElement = document.createElement(\"td\");\n    tdElement.appendChild(document.createTextNode(data));\n    document.getElementById(idOfElement).appendChild(tdElement);\n}\n\nfunction printMessage(promise) {\n\n    promise.then(function (message) {\n\n        var trElement = document.createElement(\"tr\");\n        document.getElementById('outputMessages').appendChild(trElement);\n        createTableData(message.id, 'outputMessages');\n        createTableData(message.message, 'outputMessages');\n        createTableData(message.createdOn, 'outputMessages');\n    }).catch(function (exception) {\n\n        var trElement = document.createElement(\"tr\");\n        document.getElementById('outputError').appendChild(trElement);\n        createTableData(exception, 'outputError');\n    });\n}\n\n//# sourceURL=webpack:///./src/js/app.js?");

/***/ })

/******/ });