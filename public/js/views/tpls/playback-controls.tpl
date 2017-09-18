<script type="text/template" id="playback-controls-template">
	<nav class="cue-point-nav">
		<ul>
			<li><a href="#prev" class="prev<% if (prev_cue_point === null) { %> inactive<% } %>">&laquo;</a></li>
			<li class="cue-point-icons"><ul class="cue-points"></ul></li>
			<li><a href="#next" class="next<% if (next_cue_point === null) { %> inactive<% } %>">&raquo;</a></li>
		</ul>
	</nav>
	<p>
		<a href="#add-note" class="add-note">Add Note</a>
	</p>
</script>