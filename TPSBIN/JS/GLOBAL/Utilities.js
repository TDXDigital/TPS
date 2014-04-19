
/*
Function: Get Arg from URI
Source: http://www.bloggingdeveloper.com/post/JavaScript-QueryString-ParseGet-QueryString-with-Client-Side-JavaScript.aspx
*/
function getQuerystring(key, default_)
{
  if (default_==null) default_=""; 
  key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
  var qs = regex.exec(window.location.href);
  if(qs == null)
    return default_;
  else
    return qs[1];
}

function startClock(target)
{
var today=new Date();
var h=today.getHours();
var m=today.getMinutes();
var s=today.getSeconds();
// add a zero in front of numbers<10
m=checkTime(m);
s=checkTime(s);
document.getElementById('txt').innerHTML=h+":"+m+":"+s;
t=setTimeout(function(){startTime()},500);
}


function startTimer(target)
{
var today=new Date();
var h = "00";
var m = "00";
var s = "00";
// add a zero in front of numbers<10
m=checkTime(m);
s=checkTime(s);
document.getElementById(target).innerHTML=h+":"+m+":"+s;
t=setTimeout(function(){startTime()},500);
}

function checkTime(i)
{
if (i<10)
  {
  i="0" + i;
  }
return i;
}