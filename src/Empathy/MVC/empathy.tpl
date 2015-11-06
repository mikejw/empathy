<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{if $error neq ''}Empathy Application Has Encountered an Error{else}Empathy Application{/if}</title>
<style type="text/css" media="all">
{literal}
/* eric's reset code */
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
margin: 0;
padding: 0;
border: 0;
outline: 0;
font-size: 100%;
vertical-align: baseline;
background: transparent;
}
body {
line-height: 1;
}
ol, ul {
list-style: none;
}
blockquote, q {
quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
content: '';
content: none;
}
/* remember to define focus styles! */
:focus {
outline: 0;
}
/* remember to highlight inserts somehow! */
ins {
text-decoration: none;
}
del {
text-decoration: line-through;
}
/* tables still need 'cellspacing="0"' in the markup */
table {
border-collapse: collapse;
border-spacing: 0;
}

html, body { height: 100%;
background-color: #111;
position: relative; }
#page { width: 600px; height: 460px;
  -moz-border-radius: 25px;
  -webkit-border-radius: 25px;
  background-color: #222;
  position: absolute; top: 50%; left: 50%;
  margin: -230px 0 0 -300px; }

h1 { margin: 20px 0 0 0;
  border-bottom: 2px solid #003131; text-align: center; }


#messages { padding: 20px 10px 200px 10px; background-color: #000;
overflow-y: scroll; height: 100px; }

/* typography */
h1 { color: #00cdcd; font-size: 24px;
  font-family: futura, arial, sans-serif; }
#messages h2 { font-family: verdana, arial, sans-serif;
font-size: 12px; font-weight: normal; line-height: 1.5em; }
#messages { color: #ccc; }
#messages a { color: #ccc; }

{/literal}
</style>
</head>
<body>
<!-- MVC Version: {$MVC_VERSION} -->
<div id="page">
<h1>Empathy {$MVC_VERSION}</h1>
<div id="messages">

{if $error neq ''}
<h2>{$error}</h2>

{elseif $about}
<h2>Congratulaions...</h2>
<p>You have successfully set up an empathy app.</p>

<p>http://empathyphp.co.uk</p>



{/if}


<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>


</div>
</div>

</body>
</html>
