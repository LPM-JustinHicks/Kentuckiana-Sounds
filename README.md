## Kentuckiana Sounds Map
*February 22, 2024*\
*by Justin Hicks, Data Reporter*

Louisville Public Media has a podcast, [Kentuckiana Sounds]([www.lpm.org](https://podcasts.apple.com/us/podcast/kentuckiana-sounds/id1470684796)), where any user can record ambient sound in the region and submit it for others to hear. Audio producers then interview some users about their interest in the sound and produce an episode.

To support this project, we need a way to display and collect audio files from users. When sounds are displayed, they should include the location and basic information about when and where it was collected. When sounds are collected, users should submit basic information for producers to contact them, along with the audio file, the location where the sound was recorded, and how public they wish their name to be. We will also need a sleek way for producers to moderate the submissions easily, so that "bad actors" can't submit offensive audio and have it automatically display.

***
### Code

The **index.html** file contains the code to display an interactive map of collected sounds. It also has a "form" element which collects user submission data and files. Much of this relies heavily on the Google Maps API for mapping display and marker creation and handling.

The **style.css** file is the stylesheet to format our HTML and make it look pretty.

The **script.php** file does a lot of important things. It takes in the user form entries and creates an array of key-value pairs that get stored in a JSON file for safekeeping. It includes information like the timestamp for the submission and renames the submitted files so they're always unique. The PHP file also sends a message to a Slack channel to notify producers when a submission is ready for review.

The **messages.json** file stores the user information, passed via PHP, into JSON objects for the submissions along with a path to the files in the upload folders for referencing. This JSON is what feeds the index file information to place markers on the map. 

The **uploads folder** is a collection of the audio files users have submitted.


