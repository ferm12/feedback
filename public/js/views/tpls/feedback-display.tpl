<script type="text/template" id="feedback-display-template">
	<p class="feedback-user"><%= user %> says: <a href="#edit-note" class="edit-note">edit</a></p>
	<% for (var i = 0, j = paragraphs.length; i < j; i += 1) { %>
		<p><%= paragraphs[i] %></p>
	<% } %>
</script>