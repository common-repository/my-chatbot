=== My Chatbot ===
Contributors: dpowney
Tags: google, chatbot, dialogflow, AI, asssistant
Requires at least: 4.0
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create your own branded chatbot for your WordPress website, powered by Google Dialogflow.

== Description ==

Create your own branded chatbot for your WordPress website, powered by Google Dialogflow.

Clean, simple and easy to use. [View Demo](https://mychatbot.xyz/demo?utm_source=view-demo&utm_medium=plugin&utm_campaign=readme).

=== Key Features ===

* **Conversation History** - Save to local storage. Persist across pages. Allow visitors to clear history.
* **Rich Message Responses** - Quick Replies, Cards and Images. Add oEmbed codes (e.g. Youtube video) and hyperlinks in text responses.
* **Chatbot Overlay** - Add a chatbot overlay in the bottom right corner which toggles up or down.
* **Welcome Intent** - Greet your users with a welcome message when they open the chatbot.
* **Custom Style Options** - Settings with color pickers for backgrounds and fonts and opacity for old conversation bubbles.
* **Response Message Delays** - Optionally add a delay before displaying response messages.
* **Custom Branding** - Custom text for heading, powered by and input placeholder.
* **Mobile Support** - Displays fine on mobile devices. Optionally disable chatbot overlay on mobile devices.
* **Display Control** - Choose which page types (e.g. home, single, search etc...) post types (e.g. post & page) or specific posts where the overlay can be shown.
* **Message Alignment** - Options to change request and response bubbles to be aligned either left or right.
* **Text Size** - Visitors can click an icon to increase or decrease the chatbot text size.
* **Overlay State** - Default open option. Remember open state when navigating website.
* **Shortcode & Widget** - Add chatbot in your post content using the [my_chatbot] shortcode. Add a chatbot widget to your page layouts.
* **Secure** - No credentials exposed in the browser. Google API authentication is server-side using OAuth 2.
* **Well Built** - Developer friendly. In-built template system. 60+ stargazers on Github. REST API. Lightweight.

=== Upgrade to Pro ===

Looking for some more advanced functionality? The My Chatbot Pro version provides a significant additional feature set, including:

* **Popup Overlay** - Extra chatbot overlay style. Icon bottom right corner with a popup.
* **Right Side Overlay** - Extra chatbot overlay style. Right side slide across.
* **Menu** - Add a menu button at the bottom of the chatbot which displays a menu response message.
* **System Avatar** - Display a system user avatar alongside chatbot response messages.
* **Active Schedule** - Enable the overlay to be displayed between a start and end time each day.

[Find out more](https://mychatbot.xyz/?utm_source=view-demo&utm_medium=plugin&utm_campaign=readme).

== Installation ==

See [Getting Started](https://mychatbot.xyz/getting-started?utm_source=installation&utm_medium=plugin&utm_campaign=readme) documentation.

== Screenshots ==

1. Chatbot overlay example with rich message content
2. [my_chatbot] shortcode example
3. Plugin options
4. Plugin options continued

== Upgrade Notice ==
= 1.0 =	
Dialogflow v1 API is shutting down soon. This upgrade will enable Dialogflow v2 APIs. You will need to re-configure the plugin settings to use a new service account key file for authentication.

== Changelog ==

= 1.1 (11/04/2020) =
* Fix: Incorrect locale used for German intent
* New: Added card response rich message support
* New: Added icon for visitors to delete conversation history from local storage
* New: Added oEmbed support for text responses
* New: Added admin option to scale the conversation font size
* New: Added icon for visitors to increase or decrease default font-size
* New: If chatbot is still open when navigating to a different page, it will now re-open
* Fix: loading div HTML was not removed
* New: Added option to disable overlay on mobile devices
* New: Added multiple messages delay loading
* New: Added request and response bubble alignment settings: left or right
* Tweak: Modified CSS3 media queries to improve overlay display on mobile devices
* New: Added option to allow overlay on specific page types
* New: Added option to allow overlay on specific post types
* Tweak: Removed credits screen
* Fix: Validate empty string requests
* Fix: Widget chatbot conversation area width
[//]: # fs_premium_only_begin
* New: Added new overlay theme with popup icon
* New: Added new overlay theme with right side slide across
* New: Added new chatbot avatar option
* New: Added menu and custom event option in chatbot overlay
* New: Added active schedule option
[//]: # fs_premium_only_end

= 1.0 (19/09/2020) =
* New: Upgraded to Dialogflow v2 APIs. Dialogflow API integration is now server side with OAuth 2.0 using a service account key file.
* Tweak: Removed Skype, Kik and Viber messaging platform support
* New: Conversation history saved in local storage
* Fix: Shortcode debug textarea

= 0.6 (15/03/2018) =
* New: Now supports multiple chatbots on the same page
* Bug: Fixed loading welcome intent when overlay is initially closed
* Tweak: Updated JavaScript and generated HTML to use classes instead of ids for some div elements as there can be more than one chatbot on the same page
* Bug: Escape text input to prevent XSS
* New: Added support for different languages https://dialogflow.com/docs/reference/language

= 0.5 (18/11/2017) =
* New: Added show loading option which is implemted using a local icon font and CSS animations
* New: Added loading dots color option
* New: Added response delay option 0 - 5000ms
* Bug: Moved setting the sesison cookie to before any HTML appears to fix headers already set warning
* Bug: Replaced Dashicon ico fonts for toggle up/down with a local icon font. It you have customized the chatbot-overlay.php template file, you will need to update it.

= 0.4 (11/09/2017) =
* New: Added myc_protocol_version filter for API query requests
* New: Added option in Edit Post screen to override displaying the chatbot overlay on specific posts
* Tweak: Added HTML5 placeholder to input text settings
* Tweak: Dialogflow rebranding required updates to various text (formerly API.AI) and changed base_url to https://api.dialogflow.com/v1/
* Tweak: Updated readme and welcome page
* New: Add input text (e.g. Ask something...) as an option
* New: Added unique session id for Dialogflow conversations using a cookie which expires after 24 hours
* New: Added myc_widget_before_conversation_area action hook to chatbot widget template
* New: Added option to show time underneath conversation bubbles
* New: Added filters to modify the access token, enable welcome event, messaging platform, session id and show time options. This allows you to have different chatbots on different pages for example.
* New: Added HTML5 required validation for required plugin settings

= 0.3 (03/08/2017) =
* Tweak: Added obverlay for mobile using CSS3 media queries for different small screens with portrait and lanscape orientations
* New: Added toggle CSS class to overlay for open/closed
* New: Added JavaScript event in frontend for extra response handling
* New: Added myc_shortcode_before_conversation_area action hook to [my_chatbot] shortcode template

= 0.2 (24/07/2017) =
* New: Added an overlay of the chatbot at the bottom right of every page which is enabled by default
* Tweak: Refined styles of conversation area
* New: Added default language translation files for US English
* New: Added opacity option for old conversation bubbles

= 0.1 (11/07/2017) =
 * Initial
