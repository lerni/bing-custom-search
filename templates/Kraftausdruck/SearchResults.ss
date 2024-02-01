<% if $SearchResults.Count() %>
	<ol class="results">
		<% loop $SearchResults %>
			<li>
				<a href="$URL">
					<div class="result-txt">
						<h3>$Name</h3>
						<div class="url">$URL</div>
						<p>$Description</p>
					</div>
					<figure>
						<% if $OpenGraphImageURL %><img src="$OpenGraphImageURL" alt="$Name" loading="lazy"><% end_if %>
					</figure>
				</a>
			</li>
		<% end_loop %>
	</ol>
<% else %>
	<p><%t Kraftausdruck\Extensions\BingSearchExtender.NORESULTS 'Die Suche ergab keine Ergebnisse.' %></p>
<% end_if %>
<div class="loader search"><%t Kraftausdruck\Extensions\BingSearchExtender.LOADING 'Loading...' %></div>
