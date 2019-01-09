

let fetch = require('node-fetch');
let fs = require('fs');
let getNetworkIPs = require('./modules/ipdata');
let Server = require('./modules/server');
let FormData = require('form-data');

const electron = require('electron');
const { app, BrowserWindow, ipcMain } = electron;
let mainWindow;

var fileName = "";
var filePath = "";

app.on("ready", function () {
    mainWindow = new BrowserWindow({});
    mainWindow.loadFile('ui/default.html');
});

ipcMain.on('main-page:load', (event) => {
    mainWindow.loadFile('ui/main.html');
});

ipcMain.on('sign-in-page:load', (event, data) => {
    mainWindow.loadFile('ui/signIn.html');
});

ipcMain.on('register:conformation', (event, data) => {
    mainWindow.loadFile('ui/registerConformation.html');
});

ipcMain.on('file:selected', (event, fileInfo) => {
    fileName = fileInfo.name;
    filePath = fileInfo.path;
    requestFileSend(fileName);
});

const port = 3000;

new Server(port);

function sendFile(fileName) {
    const fileDirectory = '';

    // Calculating content-length. Once uncomment, add "Content-Length": fileSizeInBytes  to the headers of options
    //const stats = fs.statSync('images/icon.png');
    //const fileSizeInBytes = stats.size;

    let bufferContent = fs.readFileSync(filePath);
    const options = {
        headers: {
            "Content-Type": "application/octet-stream",
            "Content-Disposition": 'attachment; filename="' + encodeURIComponent(fileName) + '"'
        },
        method: "POST",
        body: bufferContent
    };

    fetch("http://localhost:" + port, options)
        .then(res => {
            // const dest = fs.createWriteStream('recieved_data/icon_recieved.png');
            // res.body.pipe(dest);
        })
        .catch(console.error);
}


function requestFileSend(fileName) {
    fetch("http://localhost:" + port)
        .then(res => res.json())
        .then((jsonResp) => {
            if (jsonResp["allowFileTransfer"] == true) {
                sendFile(fileName);
            }
        })
        .catch(console.error);
}

//let interval = setInterval(ping, 60000);

function ping() {
    let pingData = new FormData();
    pingData.append("user_login", "testuser@gmail.com");
    pingData.append("local_ip", "192.168.1.101");
    pingData.append("local_port", port);
    options = {
        method: "POST",
        body: pingData
    }
    fetch("http://localhost:8888/worktime/server/ping.php", options)
        .then(res => res.json())
        .then(console.log)
        .catch(console.error);
}


getNetworkIPs(function (error, ip) {
    if (error) {
        console.error('error:', error);
    }
    else {
        console.log(ip);
    }
}, false);

