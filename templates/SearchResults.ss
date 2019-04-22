<% if $SearchResults %>
	<ol class="results">
		<% loop $SearchResults %>
			<li>
				<a href="$URL">
					<h3>$Name</h3>
					<div class="url">$URL</div>
					<p>$Description</p>
				</a>
			</li>
		<% end_loop %>
	</ol>
	<div class="loader">Loading...</div>
<%--<% else %>--%>
	<%--<p>Die Suche ergab keine Ergebnisse.</p>--%>
<% end_if %>