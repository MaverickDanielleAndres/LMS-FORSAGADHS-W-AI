	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!-- Favicon -->
	<link rel="shortcut icon" href="../assets/favicon/sagadlogo.png" type="image/x-icon" />
	<!-- Map CSS -->
	<!--	<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css" />-->
	<!-- Libs CSS -->
	<link rel="stylesheet" href="../assets/css/libs.bundle.css" />
	<!-- Theme CSS -->
	<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
	<link rel="stylesheet" id="theme" href="../assets/css/theme.bundle.css" />
	<!-- <link rel="text/html" href="/institute_dashboard/context.html" id="context-sel"> -->
	<!-- Title -->
	<title>Sagad High School LMS</title>
	<style>
		.card-img-top {
			width: 100%;
			height: 15vw;
			object-fit: cover;
		}

		body {
			scrollbar-width: thin;
			/* "auto" or "thin" */
			scrollbar-color: blue orange;
			/* scroll thumb and track */
		}

		* {
			scrollbar-width: thin;
			scrollbar-color: white #eaeaea;
			/* Scrollbar thumb and track colors */
		}

		/* For Webkit-based browsers (e.g., Chrome, Edge, Safari) */
		*::-webkit-scrollbar {
			width: 8px;
			/* Adjust scrollbar width */
		}

		*::-webkit-scrollbar-thumb {
			background-color: white;
			/* Scrollbar thumb color */
			border-radius: 4px;
			/* Rounded corners */
		}

		*::-webkit-scrollbar-track {
			background-color: #eaeaea;
			/* Scrollbar track color */
		}


		.vertical {
			border-left: 5px solid black;
			height: 200px;
		}

		.btn-back-to-top {
			position: fixed;
			bottom: 20px;
			right: 20px;
			display: block;
			z-index: 999;
		}


		#sidebar {
			background-color: #DDA853;
			color: white;
		}

		.nav-item>a i,
		.nav-link i {
			color: white;
		}

		.nav-item {
			position: relative;
		}

		.nav-item::after {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: gray;
			opacity: 0.2;
			/* Adjust opacity here */
			transform: scaleX(0);
			transform-origin: bottom right;
			transition: transform 0.3s ease, opacity 0.3s ease;
			z-index: -1;
		}

		.nav-item:hover::after {
			transform: scaleX(1);
			transform-origin: bottom left;
			opacity: 0.6;
			/* Adjust opacity on hover */
		}

		.nav-item.active::after {
			transform: scaleX(1);
			transform-origin: bottom left;
			opacity: 0.6;
			/* Adjust opacity when active */
		}

		body {
			background: linear-gradient(to top,
					rgba(194, 171, 117, 0.8),
					/* Main shade with less opacity */
					rgba(194, 171, 117, 0.4)
					/* Secondary shade with less opacity */
				),
				url('rhsopacity.png') center/cover no-repeat;
			background-size: cover;
			background-attachment: fixed;
		}



		.header-body {
			border-bottom: 2px solid white;
		}

		.header-pretitle {
			color: #558ce4;
		}
	</style>
	<?php
	error_reporting(E_ALL ^ E_ALL);
	?>
	<script>
		// check for saved 'darkMode' in localStorage
		let darkMode = localStorage.getItem('darkMode');

		const darkModeToggle = document.querySelector('#btntheme');

		const enableDarkMode = () => {
			theme.setAttribute('href', '../assets/css/theme-dark.bundle.css');
			let btn = document.getElementById('btntheme');
			btn.className = 'btn btn-info btn-floating btn-lg btn-back-to-top';
			btn.innerHTML = '<i class="fe uil-sun"></i>';
			// 2. Update darkMode in localStorage
			localStorage.setItem('darkMode', 'enabled');
			// context-sel.setAttribute('href', 'context-dark.html');
		}

		const disableDarkMode = () => {
			theme.setAttribute('href', '../assets/css/theme.bundle.css');
			let btn = document.getElementById('btntheme');
			btn.className = 'btn btn-dark btn-floating btn-lg btn-back-to-top';
			btn.innerHTML = '<i class="fe uil-moon"></i>';
			// 2. Update darkMode in localStorage
			localStorage.setItem('darkMode', null);
			// context-sel.setAttribute('href', 'context.html');

		}

		// If the user already visited and enabled darkMode
		// start things off with it on
		if (darkMode === 'enabled') {
			enableDarkMode();
		}
		// When someone clicks the button
		btntheme.addEventListener('click', () => {
			// get their darkMode setting
			darkMode = localStorage.getItem('darkMode');
			// if it not current enabled, enable it
			if (darkMode != 'enabled') {
				enableDarkMode();
				// if it has been enabled, turn it off
			} else {
				disableDarkMode();
			}
		});
	</script>
	<?php include_once 'context.html'; ?>