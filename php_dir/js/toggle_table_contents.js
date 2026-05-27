function toggleTableContents(event){
    console.log("jere");
    var thead = event.target;
    while(thead!=null && thead.nodeName != "TABLE"){
        thead=thead.parentNode;
    }
    if(thead == null) return;
    var tbody = thead.children[1];
    console.log(tbody);
    console.log(tbody.style.display);
    if(tbody.style.display == 'none')
        tbody.style.display = '';
    else tbody.style.display = 'none';
}