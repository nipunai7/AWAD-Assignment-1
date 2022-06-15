function loadDoc() { //Loading the XML File
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        myFunction(xhttp);
        }else{
            document.getElementById("cd-table").innerHTML = "<h2 class='text-center'>XML File Could Not be Loaded</h2>"; //Shows when fails to load the XML File
        }
    };
    xhttp.open("GET", "xmlFile.xml", true);
    xhttp.send();
}

function myFunction(xml) { //Creating the table from Loaded XML file
    xmlDoc = xml.responseXML;
    var table="<tr><th>Title</th><th>Artist</th><th>Country</th><th>Company</th><th>Price</th><th>Year</th><th colspan='2' class='text-center'>Modify</th></tr>";
    var x = xmlDoc.getElementsByTagName("cd");
    xmlText = new XMLSerializer().serializeToString(xmlDoc);

    if (x.length != 0){

        for (i = 0; i <x.length; i++) {
        table += "<tr><td>" +
        x[i].getElementsByTagName("title")[0].childNodes[0].nodeValue +
        "</td><td>" +
        x[i].getElementsByTagName("artist")[0].childNodes[0].nodeValue +
        "</td><td>" +
        x[i].getElementsByTagName("country")[0].childNodes[0].nodeValue +
        "</td><td>" +
        x[i].getElementsByTagName("company")[0].childNodes[0].nodeValue +
        "</td><td>" +
        x[i].getElementsByTagName("price")[0].childNodes[0].nodeValue +
        "</td><td>" +
        x[i].getElementsByTagName("year")[0].childNodes[0].nodeValue +
        "</td><td><button class='btn btn-danger' onclick=\"removeXML('cd',"+i+");\">Delete</button></td></tr>";
        }
        document.getElementById("cd-table").innerHTML = table;

    }else { 
        document.getElementById("cd-table").innerHTML = "<h2 class='text-center'>No Data to show</h2>"; //Show no data in the XML File
    }
    
}

function removeXML(){ //Removing an XML node from the XMLDom
    console.log(arguments[0]);
    console.log(arguments[1]);
    var y = arguments[1];
    var rem = xmlDoc.getElementsByTagName(arguments[0])[arguments[1]];
    xmlDoc.documentElement.removeChild(rem);
    myFunction(xhttp);
    
}

function downloadXML(){ //Download the XML File to user
    download('xmlFile.xml',xmlText);
}

function addXML() { //Adding new entries to the XML DOM
    newNode = xmlDoc.createElement("cd");
    newEle = [];

    x = xmlDoc.documentElement;
    y = xmlDoc.getElementsByTagName("cd")[0];
    x.insertBefore(newNode,y); //Adding the new CD node to the beggining of the XMLDom

    newEle = ["title","artist","country","company","price","year"];
    var val= [document.getElementById("cdName").value,document.getElementById("artist").value,document.getElementById("country").value,document.getElementById("company").value,document.getElementById("price").value,document.getElementById("datepicker").value ];
    z = xmlDoc.getElementsByTagName("cd")[0];

    for (i=0;i<newEle.length;i++){ //Checking for empty values in the form
        console.log(val[i]);
        if (val[i] == ""){
            alert(newEle[i]+" cannot be empty");
            return;
        }
    }

    for (i=0;i<newEle.length;i++) { //Adding new values to the new CD
        newElem = xmlDoc.createElement(newEle[i]);
        newText=xmlDoc.createTextNode(val[i]);
        newElem.appendChild(newText);
        z.appendChild(newElem);
    }
    myFunction(xhttp);
    xmlText = new XMLSerializer().serializeToString(xmlDoc);
    localStorage.setItem("xmlupdated", xmlText);
    xmlDoc2 = localStorage.getItem("xmlupdated");
    parser = new DOMParser();
    xmlDoc2 = parser.parseFromString(xmlDoc2,"text/xml");
}

function download(filename, text) { //Creating the XML file from the XMLDom and downlaoding
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function update(url){ //Updating function for DB and server XML file
   var xmlWrite = new XMLHttpRequest();
   xmlWrite.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            alert (this.responseText);
            console.log(this);
            console.log(xmlText);
        }
    };
    var param="xmlDoc="+ xmlText;
    xmlWrite.open("POST", url, true);
    xmlWrite.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlWrite.send(param);
}
