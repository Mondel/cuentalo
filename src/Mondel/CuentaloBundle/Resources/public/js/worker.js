importScripts('io.js');
onmessage = function (event) {

  	postMessage(get(event.data));

};