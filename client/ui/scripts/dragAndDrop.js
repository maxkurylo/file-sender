const electron = require("electron");
const { ipcRenderer } = electron;

var holder = document.getElementById('drag-file');

holder.ondragover = () => {
    holder.style.backgroundColor = "rgb(78, 147, 236)";
    return false;
};

holder.ondragleave = () => {
    holder.style.backgroundColor = "#bbb";
    return false;
};

holder.ondragend = () => {
    holder.style.backgroundColor = "#bbb";
    return false;
};

holder.ondrop = (e) => {
    e.preventDefault();
    holder.style.backgroundColor = "#bbb";
    for (let file of e.dataTransfer.files) {
        const fileInfo = {
            lastModified: file["lastModified"],
            name: file["name"],
            path: file["path"],
            size: file["size"],
            type: file["type"]
        }
        console.log(file);
        ipcRenderer.send('file:selected', fileInfo);
        break;
    }
    return false;
};