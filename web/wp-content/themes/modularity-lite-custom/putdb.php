<!-- PHP to put Form Data From Contact Form 7 into a MySQL Database (written by David Barratt) -->
<?php

// Connect to mySQL Database
$kyxinfo = new mysqli(localhost, turner_kyxinfo, kyxofficers09, turner_kyxinfo);

//Only Do Something after the submit button is hit
if ($_POST['_wpcf7']) {

	// Get the Form ID
	$form_id = $_POST['_wpcf7'];
	
	// mySQL Query to select look for the name of the form
	$findname = "SELECT title FROM wp_contact_form_7 WHERE cf7_unit_id='".$form_id."'";
	$form_name_query = $wpdb->query($findname);
	
	// Query is converted to an Array
	while ($form_name = $form_name_query -> fetch_array(MYSQLI_ASSOC))
	
	// Array is converted to a String
	$form_name_string = implode("", $form_name_array);
	
	// Make Lowercase
	$form_name_raw = strtolower($form_name_string);
	
	// Replace Spaces with _
	$space = "";
	$under = "_";
	$form_name = str_replace ($space, $under, $form_name_raw);

	if($mysqli->error) {
		print ($mysqli->error);
	} 

	
	// Remove the unnessary keys from the arrays key
	unset($_POST['submit']);
	unset($_POST['_wpcf7']);
	unset($_POST['_wpcf7_version']);
	unset($_POST['_wpcf7_unit_tag']);
	
	// Create an Array of all of the keys used
	$keys_array = array_keys($_POST);
	
	// Put all of the keys into a String
	$table_keys = implode(" VARCHAR(10000), ", $keys_array);
	
	// Conditional to Create a Table if one is not already in existence
	if ($table_found==false) {
	
		$create_table = "CREATE TABLE IF NOT EXISTS ".$form_name." (id INT AUTO_INCREMENT, date_time DATETIME, ".$table_keys.", PRIMARY KEY (id))";
		$kyxinfo->query($create_table);
	}
	
	// Put all of the keys into a String
	$keys = implode(", ", $keys_array);
	
	// Escape out any single quotes
	$sin_quote = "'";
	$sin = "\'";
	$no_quote = str_replace ($sin_quote, $sin, $_POST);
	
	// Put all of the form data into a string
	$data = implode("', '", $no_quote);
	
	// Insert the Form Data into the Table
	$insert_form = "INSERT INTO ".$form_name." (id, date_time, ".$keys.") VALUES (id, CURRENT_TIMESTAMP, '".$data."')";
	$kyxinfo->query($insert_form);
}

	
// Close the mySQL connection
$kyxinfo->close();
?>