<?php
/*
Plugin Name: digiHut
Description: Inserts custom JavaScript code to the footer of each page using a control panel in the WordPress editor.
Version: 1.0
Author: Your Name
*/

// Enqueue the JavaScript file and add the related <div> to the footer
function digiHut_custom_javascript_footer() {
    wp_enqueue_script('digiHut-custom-js', plugin_dir_url(__FILE__) . 'digiHut-custom.js', array(), '1.0', true);
    $fname = dirname(__FILE__).'/digihut.js';
    $digiScript =  file_get_contents($fname);
    echo $digiScript;
}
add_action('wp_footer', 'digiHut_custom_javascript_footer');

// Add the custom control panel in the WordPress editor
function digiHut_add_custom_panel() {
    add_meta_box('digiHut_custom_panel', 'digiHut Custom JavaScript', 'digiHut_render_custom_panel', 'post', 'normal', 'high');
    add_meta_box('digiHut_custom_panel', 'digiHut Custom JavaScript', 'digiHut_render_custom_panel', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'digiHut_add_custom_panel');

// Render the custom control panel in the WordPress editor
function digiHut_render_custom_panel($post) {
    wp_nonce_field('digiHut_save_custom_js', 'digiHut_custom_js_nonce');
    $fname = dirname(__FILE__).'/digihut.js';
    $digiScript =  file_get_contents($fname);
    ?>
    <label for="digiHut_custom_js">Enter Your Digihut JavaScript Code:</label>
    <textarea id="digiHutCode" name="digiHut_custom_js" style="width:100%;" rows="8"><?php echo $digiScript;?></textarea>
    <button id="digiHut_save_button">Save</button>
    <script>
    (function() {
        var saveButton = document.getElementById('digiHut_save_button');
        var customField = document.getElementById('digiHutCode');
        
        saveButton.addEventListener('click', function(e) {
          e.preventDefault();
            
          // Retrieve the input value
          var customFieldValue = customField.value;


          // Create a new XMLHttpRequest object
          var xhr = new XMLHttpRequest();
            
          // Define the request URL and method
          xhr.open('POST', '<?php echo content_url('plugins/digihut/digihutUpdate.php'); ?>', true);
            
          // Set the request header
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            
          // Set up the data to be sent
          var data = 'digihutJS=' + encodeURIComponent(customFieldValue) + '&nonce=<?php echo wp_create_nonce('my_custom_ajax_nonce'); ?>';
            
          // Define the callback function
          xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
              if (xhr.status === 200) {
                // Request succeeded
	        console.log(xhr.responseText);
		alert(xhr.responseText);
              } 
	      else {
                // Request failed
                console.error('AJAX request failed.');
              }
            }
          };
            
          // Send the request
          xhr.send(data);
        });
    })();
    </script>
    <?php
}
?>
