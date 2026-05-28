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
function makeTag(parentId, tagText, fgColor, bgColor, deleteAction = "", deleteName = ""){
    // console.log('Rendering tag');
    var div = document.createElement('div');
    div.classList.add('tag');
    var form = document.createElement('form');
    form.action=deleteAction;
    form.method='post';
    var submit = document.createElement('input');
    submit.value = 'x';
    submit.type='submit';
    submit.classList.add('tag-delete-btn');
    
    form.appendChild(submit);

    var span = document.createElement('span');
    span.classList.add('tag-output-text');
    span.innerText = tagText;
    if(span.innerText == '') span.innerText='<empty tag>';
    span.style.color = fgColor;
    div.style.backgroundColor = bgColor;
    div.appendChild(form);
    div.appendChild(span);
    document.getElementById(parentId).appendChild(div);
    return div;
}
function makeLinkTag(parentId, tagText, href, fgColor, bgColor){
    // console.log('Rendering tag');
    var div = document.createElement('div');
    div.classList.add('tag');
    var a = document.createElement('a');
    a.classList.add('tag-output-text');
    a.href=href;
    a.innerText = tagText;
    if(a.innerText == '') a.innerText='<empty tag>';
    a.style.color = fgColor;
    div.style.backgroundColor = bgColor;
    div.appendChild(a);
    document.getElementById(parentId).appendChild(div);
    return div;
}