@extends('layouts.app')

@section('title', 'MercjaPlayer')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Player') }}</div>

                <div class="card-body mb-3">
					<div id="epselect episodeButtons">
						<div class="mb-3">
							<button id="previousButton" class="btn btn-primary btn-sm" onclick="loadPreviousEpisode()"><i class="fal fa-backward"></i></button>
							<select id="episodeSelect" class="select2" onchange="changeEpisode()">
							@foreach ($episodes as $episode)
							<option value="{{ $episode->url }}">{{ $episode->name }}</option>
							@endforeach
							</select>
							<button id="nextButton" class="btn btn-primary btn-sm" onclick="loadNextEpisode()"><i class="fal fa-forward"></i></button>
						</div>
					</div>

					<div id="video">
						<iframe id="videoFrame" src="{{ $episodes[0]->url }}" width="640" height="406" allowfullscreen></iframe>
					</div>

					<div id="contentplayer">
						@if (app()->getLocale() === 'en')
							<p>Player not loading? Click the button below.</p>
						@elseif (app()->getLocale() === 'pl')
							<p>Odtwarzacz nie ładuje się? Kliknij przycisk poniżej.</p>
						@endif
					</div>

					<div id="linkDiv">
						@if (app()->getLocale() === 'en')
							<a id="linkButton" class="btn btn-primary btn-sm" href="{{ $episodes[0]->url }}" target="_blank">Go to the episode</a>
						@elseif (app()->getLocale() === 'pl')
							<a id="linkButton" class="btn btn-primary btn-sm" href="{{ $episodes[0]->url }}" target="_blank">Przejdź do odcinka</a>
						@endif
					</div>
                </div>
            </div>
        </div>
    </div>
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
			updateSelect2();
		}
	}

	function loadNextEpisode() {
		if (currentEpisode < episodes.length - 1) {
			currentEpisode++;
			updateVideoFrame();
			updateEpisodeSelect();
			updateLinkButton();
			updateSelect2();
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