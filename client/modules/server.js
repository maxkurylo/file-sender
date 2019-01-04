const http = require('http');
let fs = require('fs');
const homedir = require('os').homedir();

class Server {

    constructor(port) {
        this.server = http.createServer(this.serverCallback.bind(this));
        this.server.listen(port, this.listenCallback.bind(this));
    }

    serverCallback(request, response) {
        if (request.method == 'POST') {
            const fileName = this.getFileNameFromHeader(request.headers);
            if (!fileName) {
                this.responseError.call(request, 200, "File name not given. Include it to Content-Disposition header");
            }
            request.on('data', function (data) {
                console.log(homedir);
                fs.writeFile(homedir + "/Desktop/" + fileName, data, function (err) {
                    if (err) {
                        console.log(err);
                    }
                    else {
                        console.log("File recieved!");
                    }
                });
            });
            response.writeHead(200, { 'Content-Type': 'text/json' });
            response.end('{"success": true}');
        }
        else if (request.method == 'GET') {
            response.writeHead(200, { 'Content-Type': 'text/json' });
            response.end('{"allowFileTransfer": true}');
        }
    }

    listenCallback(err) {
        if (err) {
            console.log('something bad happened', err);
        }
    }

    responseError(code, contentType, error) {
        this.writeHead(200, { 'Content-Type': 'text/json' });
        this.end('{"error": "File name not given. Include it to Content-Disposition header"');
    }

    getFileNameFromHeader(header) {
        if (!header || !header['content-disposition']) {
            return null;
        }
        const matchResult = header['content-disposition'].match(/filename="(.*)"/);
        return decodeURIComponent(matchResult[1]);
    }

};


module.exports = Server;