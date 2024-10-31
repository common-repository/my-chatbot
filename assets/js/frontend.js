// When ready :)
jQuery(document).ready(function() {
	
	let defaultFontSize = true;

	// Delete conversation history btn
	jQuery(".myc-clear").click(function(event){
		event.preventDefault();
		localStorage.removeItem('conversation');
		jQuery(".myc-conversation-area").empty();
	});

	// Increase of decrease font size
	jQuery(".myc-font-size").click(function(event){
		event.preventDefault();
		if (defaultFontSize) { // increase to 120%
			defaultFontSize = false;
			jQuery(".myc-content-overlay-container, .myc-content-overlay-bottom").css("font-size", "125%");
		} else { // decrease back to original
			defaultFontSize = true;
			jQuery(".myc-content-overlay-container, .myc-content-overlay-bottom").css("font-size", "100%");
		}
	});

	if (myc_script_vars.keep_conversation_history && jQuery(".myc-conversation-area").length > 0) {
		loadFromLocalStorage();

	    // Added mutation to store the html when there is a change on chat box.
	    var chat = document.querySelectorAll(".myc-conversation-area")[0];
	    var mutationObserver = new MutationObserver(function(mutations) {
	        mutations.forEach(function(mutation) {
	            saveOnLocalStorage(document.querySelector(".myc-conversation-area").innerHTML);
	        });
	    });

	    mutationObserver.observe(chat, {
	        childList: true,
	    });
	}

    /**
     * Storage all html of chatbot in localstorage
     */
    function saveOnLocalStorage(messages) {
        localStorage.setItem("conversation", messages);
    }

    /**
     * Get all html content from localstorage when loading the page.
     */
    function loadFromLocalStorage() {
        document.querySelector(".myc-conversation-area").innerHTML = localStorage.getItem("conversation");
    }

	/*
	 * When the user enters text in the text input text field and then the presses Enter key
	 */
	jQuery("input.myc-text").keypress(function(event) {
		if (event.which == 13) {
			event.preventDefault();
			jQuery(".myc-conversation-area .myc-conversation-request").removeClass("myc-is-active");

			var text = jQuery(this).val();
			if (text.trim().length === 0) {
				return;
			}
			var date = new Date();

			var containerId = jQuery(this).closest(".myc-container").attr('id');
			var parts = containerId.split("-");
			var sequence = parts[2];

			var innerHTML = "<div class=\"myc-conversation-bubble-container myc-conversation-bubble-container-request\"><div class=\"myc-conversation-bubble myc-conversation-request myc-is-active\">" + escapeTextInput(text) + "</div>";
			if (myc_script_vars.show_time) {
				innerHTML += "<div class=\"myc-datetime\">" + date.toLocaleTimeString() + "</div>";
			}
			innerHTML += "</div>";
			if (myc_script_vars.show_loading) {
				innerHTML += "<div class=\"myc-loading\"><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /></div>";
			}
			jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
			jQuery("#myc-container-" + sequence + " input.myc-text").val("");
			jQuery("#myc-container-" + sequence + " .myc-conversation-area")
					.scrollTop(jQuery("#myc-container-" + sequence + " .myc-conversation-area")
					.prop("scrollHeight"));
			detectIntent( { text : text } , sequence);
		}
	});

	/*
	 * Welcome
	 */
	if (myc_script_vars.enable_welcome_event && jQuery(".myc-container").find(".myc-conversation-bubble-container").length == 0) {

		// show welcome intent on first chatbot only
		if ( jQuery(".myc-container").length > 0 ) {

			// check if toggled...
			jQuery(".myc-container").each( function( index, value ) {

				// Do not show welcome intent if overlay has not been opened yet
				if (jQuery(this).closest(".myc-content-overlay").length > 0) {
					if (jQuery(this).closest(".myc-content-overlay").hasClass("myc-overlay-closed")) {
						return true; // skip, same as continue
					}
				}

				var containerId = jQuery(this).attr('id');
				var parts = containerId.split("-");
				var sequence = parts[2];

				if (myc_script_vars.show_loading) {
					innerHTML = "<div class=\"myc-loading\"><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /></div>";
					jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
				}

				detectIntent( { event : 'WELCOME' }, sequence, false);

			});

		}
	}

	/* Overlay slide toggle */
	jQuery(".myc-content-overlay-toggle .myc-content-overlay-header").click(function(event){

		if (jQuery(this).find(".myc-icon-toggle-up").css("display") !== "none") { // toggle open

			document.cookie = "myc_overlay_open=true;max-age=1200"; // expires in 20 minutes

			var container = jQuery(this).siblings(".myc-content-overlay-container").find(".myc-container");

			// if welcome intent enabled and no conversation exists yet
			if (myc_script_vars.enable_welcome_event && jQuery(container).find(".myc-conversation-bubble-container").length == 0) {

				var containerId = jQuery(container).attr('id');
				var parts = containerId.split("-");
				var sequence = parts[2];

				if (myc_script_vars.show_loading) {
					innerHTML = "<div class=\"myc-loading\"><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /></div>";
					jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
				}

				detectIntent( { event : 'WELCOME' }, sequence, false);
			}

			jQuery(this).parent().removeClass("myc-overlay-closed");
			jQuery(this).parent().addClass("myc-overlay-open");
			
			jQuery(this).siblings(".myc-content-overlay-container, .myc-content-overlay-bottom, .myc-content-overlay-powered-by, .myc-content-overlay-bottom").slideToggle("slow", function() {});
			
			jQuery(this).find(".myc-icon-toggle-up").hide();
			jQuery(this).find(".myc-icon-toggle-down").show();

		} else { // toggle close
			document.cookie = "myc_overlay_open=false";

			jQuery(this).parent().removeClass("myc-overlay-open");
			jQuery(this).parent().addClass("myc-overlay-closed");
			
			jQuery(this).siblings(".myc-content-overlay-container, .myc-content-overlay-bottom, .myc-content-overlay-powered-by, .myc-content-overlay-bottom").slideToggle("slow", function() {});
			
			jQuery(this).find(".myc-icon-toggle-down").hide();
			jQuery(this).find(".myc-icon-toggle-up").show();
		}
	});

	
/* Premium Code Stripped by Freemius */


});


/**
 * Send Dialogflow query
 *
 * @param text
 * @param sequence
 * @returns
 */
function detectIntent(data, sequence, delay = true) {

	data['lang'] = myc_script_vars.language;
	data['sessionId'] = myc_script_vars.session_id;

	jQuery.ajax( {
		url: myc_script_vars.wpApiSettings.root + 'myc/v1/detectIntent',
		method: 'POST',
		beforeSend: function ( xhr ) {
		    xhr.setRequestHeader( 'X-WP-Nonce', myc_script_vars.wpApiSettings.nonce );
		},
		data: data,
		success : function(response) {
			if (delay) {
				setTimeout(function(){
					if (myc_script_vars.show_loading) {
						jQuery("#myc-container-" + sequence + " .myc-loading").remove();
					}
					prepareResponse(response, sequence, delay);
				}, myc_script_vars.response_delay);
			} else {
				if (myc_script_vars.show_loading) {
					jQuery("#myc-container-" + sequence + " .myc-loading").remove();
				}
				prepareResponse(response, sequence, delay);
			}
		},
		error : function(response) {
			if (myc_script_vars.show_loading) {
				jQuery("#myc-container-" + sequence + " .myc-loading").remove();
			}
			textResponse(myc_script_vars.messages.internal_error, sequence);
			jQuery("#myc-container-" + sequence + " .myc-conversation-area")
					.scrollTop(jQuery("#myc-container-" + sequence + " .myc-conversation-area")
					.prop("scrollHeight"));
		}
	} ).done( function ( response ) {
	    //console.log( response );
	} );

}

/**
 * Handle Dialogflow response
 *
 * @param response
 * @param response
 * @delay delay for multiple messages - default true
 */
async function prepareResponse(response, sequence, delay = true) {

	if (response) {

		jQuery(window).trigger("myc_response_success", response);

		jQuery("#myc-container-" + sequence + " .myc-conversation-area .myc-conversation-response").removeClass("myc-is-active");

		var messages = response.messages;
		var numMessages = messages.length;
		var index = 0;
		var isPromiseSupported = typeof Promise !== "undefined";

		for (index; index<numMessages; index++) {

			if (isPromiseSupported && delay && index > 0 && numMessages > 1 && myc_script_vars.show_loading) {
				jQuery("#myc-container-" + sequence + " .myc-loading").remove();
			}

			var message = messages[index];
				
			if (message.text) {
				textResponse(message.text.text, sequence);
			} else if (message.quickReplies) {
				quickRepliesResponse(message.quickReplies.title, message.quickReplies.quickReplies, sequence);
			} else if (message.image) {
				imageResponse(message.image.imageUri, sequence);
			} else if (message.card) {
				cardResponse(message.card.title, message.card.subtitle, message.card.buttons, message.card.imageUri, sequence);
			}

			// TODO custom payload

			if (isPromiseSupported && delay && index < (numMessages-1)) {
				if (myc_script_vars.show_loading) {
					var innerHTML = "<div class=\"myc-loading\"><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /></div>";
					jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
					jQuery("#myc-container-" + sequence + " .myc-conversation-area").scrollTop(jQuery("#myc-container-" + sequence + " .myc-conversation-area")[0].scrollHeight);
				}
				
				await new Promise(done => setTimeout(() => done(), myc_script_vars.response_delay));
				
				if (myc_script_vars.show_loading) {
					jQuery("#myc-container-" + sequence + " .myc-loading").remove();
				}
			}
			setTimeout(function() { // small timeout to wait for image to render with height
				jQuery("#myc-container-" + sequence + " .myc-conversation-area").scrollTop(jQuery("#myc-container-" + sequence + " .myc-conversation-area")[0].scrollHeight);
			}, 200);	

		}

	} else {
		textResponse(myc_script_vars.messages.internal_error, sequence);
	}

	jQuery("#myc-container-" + sequence + " .myc-conversation-area")
			.scrollTop(jQuery("#myc-container-" + sequence + " .myc-conversation-area")
			.prop("scrollHeight"));

	if (jQuery("#myc-container-" + sequence + " + .myc-debug #myc-debug-data").length) {
		var debugData = JSON.stringify(response, undefined, 2);
		jQuery("#myc-container-" + sequence + " + .myc-debug #myc-debug-data").text(debugData);
	}
}


/**
 * Displays a text response
 *
 * @param text
 * @param sequence
 * @returns
 */
function textResponse(text, sequence) {
	if (text === "") {
		text = myc_script_vars.messages.internal_error;
	} else {
		var date = new Date();
		var innerHTML = "<div class=\"myc-conversation-bubble-container myc-conversation-bubble-container-response\">";

		
/* Premium Code Stripped by Freemius */


		innerHTML += "<div class=\"myc-conversation-bubble myc-conversation-response myc-is-active myc-text-response\" style=\"margin-bottom: 5px;\">" + text + "</div>";
		
		
/* Premium Code Stripped by Freemius */


		if (myc_script_vars.show_time) {
			innerHTML += "<div class=\"myc-datetime\">" + date.toLocaleTimeString() + "</div>";
		}
		innerHTML += "</div>";

		jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
	}
}

/**
 * Displays a image response
 *
 * @param imageUrl
 * @param sequence
 * @returns
 */
function imageResponse(imageUrl, sequence) {
	if (imageUrl === "") {
		textResponse(myc_script_vars.messages.internal_error, sequence)
	} else {
		// FIXME wait for image to load by creating HTML first
		var date = new Date();
		
		var innerHTML = '<div class=\"myc-conversation-bubble-container myc-conversation-bubble-container-response\">';

		
/* Premium Code Stripped by Freemius */


		innerHTML += "<div class=\"myc-conversation-bubble myc-conversation-response myc-is-active myc-image-response\"><img src=\"" + imageUrl + "\"/></div>";
		
		
/* Premium Code Stripped by Freemius */


		if (myc_script_vars.show_time) {
			innerHTML += "<div class=\"myc-datetime\">" + date.toLocaleTimeString() + "</div>";
		}

		innerHTML += "</div>";
		
		jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
	}
}

/**
 * Card response
 *
 * @param title
 * @param subtitle
 * @param buttons
 * @param text
 * @param postback
 * @param sequence
 */
function cardResponse(title, subtitle, buttons, imageUri, sequence) {
	if (title === "") {
		textResponse(myc_script_vars.messages.internal_error, sequence)
	} else {
		var date = new Date();
		
		var innerHTML = "<div class=\"myc-conversation-bubble-container myc-conversation-bubble-container-response\">";

		
/* Premium Code Stripped by Freemius */

		
		innerHTML += "<div class=\"myc-conversation-bubble myc-conversation-response myc-is-active myc-card-response\">";
		if (imageUri) {
			innerHTML += "<img class=\"myc-card-image\" src=\"" + imageUri + "\" />";
		}
		
		innerHTML += "<div class=\"myc-card-title-wrapper\">";
		innerHTML += "<div class=\"myc-card-title\">" + title + "</div>";
		if (subtitle) {
			innerHTML += "<div class=\"myc-card-subtitle\">" + subtitle + "</div>";
		}
		innerHTML += '</div>';
		
		if (buttons) {
			var index = 0;
			for (index; index<buttons.length; index++) {
				if (buttons[index].postback && buttons[index].text) {
					innerHTML += "<a href=\"" + buttons[index].postback + "\" class=\"myc-card-button\">"  + buttons[index].text + "</a>";
				}
			}
		}

	 	innerHTML += "</div>";

	 	
/* Premium Code Stripped by Freemius */

		
		if (myc_script_vars.show_time) {
			innerHTML += "<div class=\"myc-datetime\">" + date.toLocaleTimeString() + "</div>";
		}

		innerHTML += "</div>";
		jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
	}
}

/**
 * Quick replies response
 *
 * @param title
 * @param replies
 * @param sequence
 */
function quickRepliesResponse(title, replies, sequence) {

	var date = new Date();
	var innerHTML = "<div class=\"myc-conversation-bubble-container myc-conversation-bubble-container-response\">";

	
/* Premium Code Stripped by Freemius */

	
	var html = "<div class=\"myc-quick-replies-title\">" + title + "</div>";

	var index = 0;
	for (index; index<replies.length; index++) {
		html += "<input type=\"button\" class=\"myc-quick-reply\" value=\"" + replies[index] + "\" />";
	}
	innerHTML += "<div class=\"myc-conversation-bubble myc-conversation-response myc-is-active myc-quick-replies-response\">" + html + "</div>";
	
	
/* Premium Code Stripped by Freemius */


	if (myc_script_vars.show_time) {
		innerHTML += "<div class=\"myc-datetime\">" + date.toLocaleTimeString() + "</div>";
	}
	innerHTML += "</div>";
	jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);

	jQuery("#myc-container-" + sequence + " .myc-conversation-area .myc-is-active .myc-quick-reply").click(function(event) {
		event.preventDefault();
		jQuery("#myc-container-" + sequence + " .myc-conversation-area .myc-conversation-request").removeClass("myc-is-active");
		var text = jQuery(this).val()
		if (text.trim().length === 0) {
			return;
		}
		var date = new Date();
		var innerHTML = "<div class=\"myc-conversation-bubble-container myc-conversation-bubble-container-request\"><div class=\"myc-conversation-bubble myc-conversation-request myc-is-active\">" + escapeTextInput(text) + "</div>";
		if (myc_script_vars.show_time) {
			innerHTML += "<div class=\"myc-datetime\">" + date.toLocaleTimeString() + "</div>";
		}
		if (myc_script_vars.show_loading) {
			innerHTML += "<div class=\"myc-loading\"><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /><i class=\"myc-icon-loading-dot\" /></div>";
		}
		innerHTML += "</div>";
		jQuery("#myc-container-" + sequence + " .myc-conversation-area").append(innerHTML);
		detectIntent( { text : text }, sequence);
	});

}

/**
 * Custom payload
 *
 * @param payload
 */
function customPayload(payload, sequence) {
}


var entityMap = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#39;',
  '/': '&#x2F;',
  '`': '&#x60;',
  '=': '&#x3D;'
};

/**
 * Escapes HTML in text input
 */
function escapeTextInput(text) {
  return String(text).replace(/[&<>"'`=\/]/g, function (s) {
    return entityMap[s];
  });
}
