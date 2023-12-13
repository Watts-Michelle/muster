<nav class="navbar navbar-default">
	<div class="container-fluid">
        <div class="navbar-header page-scroll">
            <%--<a class="navbar-brand" href="$BaseHref">--%>
				<%--$SiteConfig.MenuLogo.setWidth(120)--%>
            <%--</a>--%>
            <ul>
				<% loop $Menu(1) %>
                    <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a></li>
				<% end_loop %>
            </ul>
        </div>
	</div>
</nav>
