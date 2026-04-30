<!-- Jquery Library File -->
<script src="{{ asset('rosta/js/jquery-3.7.1.min.js') }}"></script>
<!-- Bootstrap js file -->
<script src="{{ asset('rosta/js/bootstrap.min.js') }}"></script>
<!-- Validator js file -->
<script src="{{ asset('rosta/js/validator.min.js') }}"></script>
<!-- SlickNav js file -->
<script src="{{ asset('rosta/js/jquery.slicknav.js') }}"></script>
<!-- Swiper js file -->
<script src="{{ asset('rosta/js/swiper-bundle.min.js') }}"></script>
<!-- Counter js file -->
<script src="{{ asset('rosta/js/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('rosta/js/jquery.counterup.min.js') }}"></script>
<!-- Isotop js file -->
<script src="{{ asset('rosta/js/isotope.min.js') }}"></script>
<!-- Magnific js file -->
<script src="{{ asset('rosta/js/jquery.magnific-popup.min.js') }}"></script>
<!-- SmoothScroll -->
<script src="{{ asset('rosta/js/SmoothScroll.js') }}"></script>
<!-- Parallax js -->
<script src="{{ asset('rosta/js/parallaxie.js') }}"></script>
<!-- MagicCursor js file -->
<script src="{{ asset('rosta/js/gsap.min.js') }}"></script>
<script src="{{ asset('rosta/js/magiccursor.js') }}"></script>
<!-- Text Effect js file -->
<script src="{{ asset('rosta/js/SplitText.js') }}"></script>
<script src="{{ asset('rosta/js/ScrollTrigger.min.js') }}"></script>
<!-- YTPlayer js File -->
<script src="{{ asset('rosta/js/jquery.mb.YTPlayer.min.js') }}"></script>
<!-- Wow js file -->
<script src="{{ asset('rosta/js/wow.min.js') }}"></script>
<!-- Main Custom js file -->
<script src="{{ asset('rosta/js/function.js') }}"></script>
<script src="https://www.youtube.com/iframe_api"></script>
<script>
    (function () {
        var trigger = document.querySelector('.listening-trigger');
        if (!trigger) {
            return;
        }
        var artists = Array.from(document.querySelectorAll('.the-ticker .tick-left .ticker-text-wrapper:not(.dup) strong')).map(function (item) {
            return item.textContent.trim();
        }).filter(Boolean);
        var allArtistNodes = Array.from(document.querySelectorAll('.the-ticker .tick-left .ticker-text-wrapper strong'));
        var tracks = [
            { artist: 'Nick Mulvey', videoId: 'Ds0nA5LMf4M' },
            { artist: 'Tom Petty and the Heartbreakers', videoId: 'h0JvF9vpqx8' },
            { artist: 'Jean Dawson', videoId: 'Gf95M9JQJUk' },
            { artist: 'Thundercat', videoId: 'ormQQG2UhtQ' },
            { artist: 'Joy Division', videoId: 'zuuObGsB0No' },
            { artist: 'Neil Diamond', videoId: '4F_RCWVoL4s' },
            { artist: 'Harry Styles', videoId: 'qN4ooNx77u0' },
            { artist: 'Noah Gundersen', videoId: '89cT6Nf4NfI' },
            { artist: 'Bruno Mars', videoId: 'OPf0YbXqDm0' },
            { artist: 'thebandfriday', videoId: 'D6nxCqQrb4M' },
            { artist: 'Jeremy Passion', videoId: '8xN4ULfZHf4' },
            { artist: 'J. Cole', videoId: 'WILNIXZr2oc' },
            { artist: 'Solon Holt', videoId: 'fV6gP6_Wj4s' },
            { artist: 'Lily Meola', videoId: 'UPs3ALfyM4g' },
            { artist: 'Timmy Skelly', videoId: 'w6RH2f2fT3I' }
        ];
        var orderedTracks = artists.map(function (artistName) {
            return tracks.find(function (track) {
                return track.artist === artistName;
            });
        }).filter(Boolean);
        if (!orderedTracks.length) {
            orderedTracks = tracks;
        }
        var trackIndexByArtist = orderedTracks.reduce(function (acc, track, index) {
            acc[track.artist] = index;
            return acc;
        }, {});
        var playerContainer = document.createElement('div');
        playerContainer.id = 'listening-now-player';
        playerContainer.setAttribute('aria-hidden', 'true');
        playerContainer.style.position = 'absolute';
        playerContainer.style.width = '1px';
        playerContainer.style.height = '1px';
        playerContainer.style.overflow = 'hidden';
        playerContainer.style.left = '-9999px';
        document.body.appendChild(playerContainer);
        var player = null;
        var apiReady = false;
        var currentTrackIndex = 0;
        var pendingPlay = false;
        var loadTrack = function (index, autoplay) {
            if (!player || !orderedTracks.length) {
                return;
            }
            currentTrackIndex = index % orderedTracks.length;
            if (autoplay) {
                player.loadVideoById(orderedTracks[currentTrackIndex].videoId);
            } else {
                player.cueVideoById(orderedTracks[currentTrackIndex].videoId);
            }
        };
        var startPlayback = function () {
            pendingPlay = true;
            if (player && typeof player.playVideo === 'function') {
                loadTrack(currentTrackIndex, true);
                player.unMute();
                player.setVolume(100);
                return;
            }
            if (!apiReady) {
                var fallbackTrack = orderedTracks[currentTrackIndex] || orderedTracks[0];
                if (fallbackTrack) {
                    window.open('https://www.youtube.com/watch?v=' + fallbackTrack.videoId, '_blank', 'noopener');
                }
            }
        };
        var playArtistByIndex = function (index) {
            currentTrackIndex = index;
            startPlayback();
        };
        allArtistNodes.forEach(function (artistNode) {
            var artistName = artistNode.textContent.trim();
            var artistTrackIndex = trackIndexByArtist[artistName];
            if (artistTrackIndex === undefined) {
                return;
            }
            artistNode.classList.add('listening-artist');
            artistNode.setAttribute('role', 'button');
            artistNode.setAttribute('tabindex', '0');
            artistNode.setAttribute('aria-label', 'Play ' + artistName);
            artistNode.addEventListener('click', function () {
                playArtistByIndex(artistTrackIndex);
            });
            artistNode.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    playArtistByIndex(artistTrackIndex);
                }
            });
        });
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            startPlayback();
        });
        trigger.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                startPlayback();
            }
        });
        window.onYouTubeIframeAPIReady = function () {
            apiReady = true;
            player = new YT.Player('listening-now-player', {
                height: '1',
                width: '1',
                videoId: orderedTracks[0].videoId,
                playerVars: {
                    autoplay: 0,
                    controls: 0,
                    rel: 0,
                    modestbranding: 1
                },
                events: {
                    onReady: function () {
                        if (pendingPlay) {
                            loadTrack(currentTrackIndex, true);
                            player.unMute();
                            player.setVolume(100);
                            pendingPlay = false;
                        } else {
                            loadTrack(currentTrackIndex, false);
                        }
                    },
                    onStateChange: function (event) {
                        if (event.data === YT.PlayerState.ENDED) {
                            currentTrackIndex = (currentTrackIndex + 1) % orderedTracks.length;
                            loadTrack(currentTrackIndex, true);
                        }
                    },
                    onError: function () {
                        currentTrackIndex = (currentTrackIndex + 1) % orderedTracks.length;
                        loadTrack(currentTrackIndex, true);
                    }
                }
            });
        };
    })();
</script>