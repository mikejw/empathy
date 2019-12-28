<footer>

{if $error eq ''}
<nav id="links">
<h4>Links</h4>
<ul>
{if $mode neq 'site'}
<li><a id="site" href="http://empathyphp.co.uk"><span>empathyphp.co.uk</span></a></li>
{/if}
<li><a id="github" href="http://github.com/mikejw/empathy"><span>GitHub</span></a></li>
<li><a id="github" href="https://packagist.org/packages/mikejw"><span>Packagist</span></a></li>
</ul>
</nav>
{/if}

<address>
<a href="https://www.empathyphp.co.uk">Empathy MVC Framework</a> version {$MVC_VERSION} &copy; 2008-2019 <a href="mailto:mikejw3@gmail.com">Mike Whiting</a>.

</address>
</footer>


</div>
</body>
</html>