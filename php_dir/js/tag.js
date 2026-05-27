function renderTag(){
    // console.log('Rendering tag');
    let tagDivId='tag-output';
    let tagPId='tag-output-text';
    let tagTextId='tag-name';
    let fgInputId='fgcolor';
    let bgInputId='bgcolor';
    var div = document.getElementById(tagDivId);
    var p = document.getElementById(tagPId);
    p.innerText = document.getElementById(tagTextId).value;
    if(p.innerText == '') p.innerText='<empty tag>';
    p.style.color = document.getElementById(fgInputId).value;
    div.style.backgroundColor = document.getElementById(bgInputId).value;
    document.getElementById('tag-output-wrapper').style.display='block';
    // console.log('Rendered tag?');
}
function makeTag(parentId, tagText, fgColor, bgColor){
    // console.log('Rendering tag');
    var div = document.createElement('div');
    var p = document.getElementById('p');
    p.innerText = tagText;
    if(p.innerText == '') p.innerText='<empty tag>';
    p.style.color = fgColor;
    div.style.backgroundColor = bgColor;
    document.getElementById(parentId).appendChild(div);
    return div;
}