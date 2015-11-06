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
<li><a id="twitter" href="http://twitter.com/empathyphp"><span>twitter</span></a></li>
</ul>
</nav>
{/if}

<address>
<a href="http://empathyphp.co.uk">Empathy MVC Framework</a> version {$MVC_VERSION} &copy; 2015 <a href="http://ai-em.net">Mike Whiting</a>.

</address>
</footer>


</div>
</body>
</html>