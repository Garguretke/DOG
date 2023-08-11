@extends('layouts.app')

@section('title', 'MercjaPlayer')

@section('content')
    <div id="episodeButtons">
        <button id="previousButton" onclick="loadPreviousEpisode()">Poprzedni odcinek</button>
        <button id="nextButton" onclick="loadNextEpisode()">Następny odcinek</button>
    </div>

    <br />

    <div id="epselect">
        <select id="episodeSelect" class="select2" onchange="changeEpisode()">
            @foreach ($episodes as $episode)
                <option value="{{ $episode->url }}">{{ $episode->name }}</option>
            @endforeach
        </select>
    </div>

    <br />

    <div id="video">
        <iframe id="videoFrame" src="{{ $episodes[0]->url }}" width="640" height="406" allowfullscreen></iframe>
    </div>

    <div id="contentplayer">
        <p>Odtwarzacz nie ładuje się? Kliknij przycisk poniżej.</p>
    </div>

    <div id="linkDiv">
        <a id="linkButton" href="{{ $episodes[0]->url }}" target="_blank">Przejdź do odcinka</a>
    </div>

	<script>
		var episodes = <?php echo json_encode($episodes); ?>;
		var currentEpisode = 0;
		var videoFrame = document.getElementById("videoFrame");
		var episodeSelect = document.getElementById("episodeSelect");
		var linkButton = document.getElementById("linkButton");
		var $j = jQuery.noConflict();

		function loadPreviousEpisode() {
			if (currentEpisode > 0) {
				currentEpisode--;
				updateVideoFrame();
				updateEpisodeSelect();
				updateLinkButton();
			}
		}

		function loadNextEpisode() {
			if (currentEpisode < episodes.length - 1) {
				currentEpisode++;
				updateVideoFrame();
				updateEpisodeSelect();
				updateLinkButton();
				updateSelect2(); // Dodane wywołanie funkcji aktualizującej Select2
			}
		}

		function updateSelect2() {
			$j('#episodeSelect').val(episodes[currentEpisode].url).trigger('change');
		}

		function changeEpisode() {
			currentEpisode = episodeSelect.selectedIndex;
			updateVideoFrame();
			updateLinkButton();
		}

		function updateVideoFrame() {
			var episode = episodes[currentEpisode];
			videoFrame.src = episode.url;
		}

		function updateEpisodeSelect() {
			episodeSelect.selectedIndex = currentEpisode;
		}

		function updateLinkButton() {
			var episode = episodes[currentEpisode];
			linkButton.href = episode.url;
		}

		$j(document).ready(function() {
			$j('#episodeSelect').select2();
		});
	</script>
@endsection