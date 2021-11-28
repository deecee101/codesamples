# codesamples
samples of various web development tools and techniques<br>
These demos were built on a windows machine using xampp, so in order to run them, 
it is best to use a similar setup, or modify the code as needed.

1) Authorization<br>
	This is a demo of authorization with persistence. The application uses apache authentication,
	a mySQL database, and indexedDB. There is a token stored in indexedDB for the user so revisiting
	the page does not require entering in credentials again (if you save the apache authentication, or remove it from htaccess)
	 until another browser logs in to replace the current token.<br>

2) Background Slideshow<br>
	A demo of a simple background slideshow that dynamically loads and fades through images<br>

3) Database<br>
	Three demos of different storage management methods. Each example utilizes a Create, Read, Update, & Delete routine.<br>
	Each demo loads a list of videos, and generates a set of forms to edit information attributing to each video<br>
	a) indexedDB - this is client side storage<br>
	b) mySQL - the web standard. It is necessary to edit the mySQL_AUTH.php file (in the root directory) to use your own mySQL root credentials<br>
	c) sqlite - very easy to use because it requires no credential authentication. <br>

4) FFMPEG<br>
	An example of using FFMPEG to parse a video and generate thumbnails of the video.<br>
	This demo was built using windows, so it will not work on linux without modification<br>

5) Geocoding<br>
	Demo combining the browser location api, an address location service, a reverse geocoding service, and google maps.<br>
	enter in a destination address, get the current device location, and then use google maps to generate driving directions between the two<br>

6) LazyLoad Images<br>
	Demo of a technique which helps load times on pages which require high resolution images.<br>
	First the application scans a folder and generates low resolution images. Then when listing the photos
	it will first load the low-resolution images. As the user scrolls down, each low-resolution image is swapped out for a high resolution images 
	when the image appears on screen.<br>

7) Main Menu<br>
	A general all-purpose responsive main menu. There is a large size format, a medium size format, and a compact size format.<br>
	There is also a function to scroll to each section based on which menu item is clicked.<br>

8) PHP Metadata<br>
	PHP has the ability to grab metadata from files which may have it. In this case an mp3 with various information and an image is exhibited.<br>

9) Service Worker<br>
	Demo of using a serviceworker. Simply 3 links to 3 bits of information and 7 images. Once it is loaded into the browser initially, the information
	and images are storedin the cache via serviceworker. It is then possible to go offline and still browse + view the info and images.<br>

10) SVG<br>
	Demo of displaying SVG images in the browser instead of images. This demo also shows a nifty animation of SVG using tweener and SnapSVG<br>

11) Text Animation<br>
	Text which animates in character by character.<br>

12) Uploader<br>
	Demo of a file uploader which uses progress bars to show status to the user<br>

13) Weather<br>
	Demo of using the OpenWeatherMap API and geolocation to get and display weather. This demo gets the current weather, an hourly forecast, and a 5 day forecast<br>

14) Web Push<br>
	A demo of web push notifications. It is required for this to be on a live server connected to the www. It is also required to acquire and enter in your own 
	unique api keys in order for this to work. A simple notification example with an image icon uploader for the notification.<br>

15) WebComponents<br>
	This is a TODO list packaged in a web component with a SQL database. Items can be added, completed, and removed.
	Each is stored in the database unless they are deleted by the user.<br>

16) Web Worker<br>
	This is a demo of a process which would normally interfere with web page interaction. It is a dynamic color fade for a background layer and a text layer.<br>
	Since the color fade processes are done with web workers, the computations are in te separate thread it would not effect anything the main web page would be doing.<br>