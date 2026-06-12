var ctx,output;
function clear_canvas(){
    ctx.clearRect(0,0,output.width,output.height);
}
function drawsegment(x1, y1, x2, y2, width=4,color="#000000"){
    x1*=output.width; x2*=output.width;
    y1*=output.height; y2*=output.height;
    ctx.beginPath();
    ctx.lineWidth=width;
    ctx.strokeStyle=color;
    ctx.moveTo(x1,y1);
    ctx.lineTo(x2,y2);
    ctx.stroke();
    ctx.closePath();
}
function adjusty(y, minval, maxval){
    return 0.9-(y-minval)/(maxval-minval)*0.8
}
function adjustx(x, minval, maxval){
    return 0.15+(x-minval)/(maxval-minval)*0.75;
}
function drawsegment2(x1, y1, x2, y2, minx, maxx, miny, maxy, width=4,color="#000000"){
    drawsegment(adjustx(x1,minx,maxx),adjusty(y1,miny,maxy),
                adjustx(x2,minx,maxx),adjusty(y2,miny,maxy),
                width,color);
}
function drawvertline(x,width){
    drawsegment(x,0,x,1,width,'white');
}
function drawhorline(y,width){
    drawsegment(0.09,y,1,y,width,'white');
}
function drawylabel(y, minval, maxval, is_thick=0){
    var drawy=adjusty(y,minval,maxval);
    drawsegment(0.09,drawy,0.9,drawy,(is_thick?2:1),'white');
    drawsegment(0.09,drawy,0.11,drawy,2,'white');
    drawtext(0.085,drawy,y,20,'white','middle','end');
}
function drawxlabel(x, minval, maxval, is_thick=0){
    var drawx=adjustx(x,minval,maxval);
    drawsegment(drawx,0.1,drawx,0.91,(is_thick?2:1),'white');
    drawsegment(drawx,0.89,drawx,0.91,2,'white');
    drawtext(drawx,0.915,x,20,'white','top','center');
}
function drawdisk(x,y,minx,maxx,miny,maxy,r,color="#000000"){
    //console.log(x,y);
    x=adjustx(x,minx,maxx)*output.width;
    y=adjusty(y,miny,maxy)*output.height;
    //console.log(x,y,r);
    ctx.beginPath();
    ctx.arc(x,y,r*Math.min(output.width,output.height),0,2 * Math.PI,false);
    ctx.fillStyle = color;
    ctx.fill();
    //ctx.lineWidth = 5;
    //ctx.strokeStyle = '#003300';
    //ctx.stroke();
    ctx.closePath();
}
function drawtext(x1,y1,text,fontsize,color,baseline,align){
    x1*=output.width;
    y1*=output.height;
    ctx.font=fontsize+"px sans-serif";
    ctx.fillStyle=color;
    ctx.textBaseline=baseline;
    ctx.textAlign=align;
    ctx.fillText(text,x1,y1);
}
function compute_partial_scores(history){
    var ret=[];
    for(var i=0;i<history.length;i++){
        ret[i+1]=ret[i]+history[i][2];
    }
    return ret;
}
// history = [[timestamp, formatted_timestamp, delta]]
function render(container, history, targetHeight, targetWidth, dangerLevel){
    
    var partial_scores=compute_partial_scores(history);
    //console.log(partial_scores);
    var minval=0,maxval=0;
    for(var i=0;i<=history.length;i++){
        minval=Math.min(minval,partial_scores[i]);
        maxval=Math.max(maxval,partial_scores[i]);
    }
    output=document.createElement('canvas');
    output.height=targetHeight;
    output.width=targetWidth;
    ctx=output.getContext("2d");
    //drawvertline(0.1,3);
    //drawvertline(0.9,1);
    //drawhorline(0.1,1);
    //drawhorline(0.9,1);
    //drawtext(0.085,0.1,maxval,20,'white','middle','end');
    //drawtext(0.085,0.9,minval,20,'white','middle','end');
    var xstep=Number(1);
    //console.log("xstep=",xstep);
    drawxlabel(0,0,history.length,1);
    drawxlabel(history.length,0,history.length,1);
    for(var i=0;i+xstep<=history.length;i+=xstep){
        drawxlabel(i,0,history.length);
    }
    var ystep=Math.max(50,Math.ceil((maxval-minval)/600)*50),curry;
    //console.log(ystep);
    drawylabel(0,minval,maxval,1);
    drawylabel(minval,minval,maxval,1);
    drawylabel(maxval,minval,maxval,1);
    curry=maxval-maxval%ystep;
    while(curry>minval){
        if(maxval-curry>=ystep && curry-minval>=ystep)
            drawylabel(curry,minval,maxval);
        curry-=ystep;
    }
    for(var i=0;i<history.length;i++){
        for(var j=0;j<n;j++){
            drawsegment2(i,partial_scores[i][j],i+1,partial_scores[i+1][j],0,history.length,minval,maxval,3,player_colors[j]);
            //drawdisk(i+1,partial_scores[i+1][j],0,history.length,minval,maxval,0.015,player_colors[j]);
        }
    }
    container.appendChild(output);
}