module.exports = Files;

class Files {

    saveFileFromBuffer(file, buffer) {
        let fs = require('fs');
        let stream = require('stream');
        let wstream = fs.createWriteStream(file);
        wstream.write(buffer);
        wstream.end();

        fs.writeFile(file, buffer, function (err) {
            if (err) {
                console.log(err);
            }
            else {
                console.log("File recieved!");
            }
        });
    }
}