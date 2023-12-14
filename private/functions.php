<?php
  function heading($string = "") {
    return htmlspecialchars($string);
  }

  function redirect_to($location) {
    header("Location: ".$location);
    exit;
  }

  function is_post_request() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
  }

  function is_get_request() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
  }

  //HTTP and HTTPS
  function no_SSL() {
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
      header("Location: http://".$_SERVER['HTTP_HOST'].
        $_SERVER['REQUEST_URI']);
      echo "<p>No SSL</p>";
      exit();
    }
  }

  function require_SSL() {
    if($_SERVER['HTTPS'] != "on") {
      header("Location: https://".$_SERVER['HTTP_HOST'].
        $_SERVER['REQUEST_URI']);
      echo "<p>Running SSL</p>";
      exit();
    }
  }

  //is user logged in?
  function is_logged_in() {
    return isset($_SESSION['valid_user']);
  }

  //is the product already in the watchlist?
  function is_in_watchlist($code) {
    global $db;
    if(isset($_SESSION['valid_user'])) {
      $query = "SELECT COUNT(*) FROM watchlist WHERE listing_id=? AND username=?";
      $stmt = $db->prepare($query);
      $stmt->bind_param('ss', $code, $_SESSION['valid_user']);
      $stmt->execute();
      $stmt->bind_result($count);
      return ($stmt->fetch() && $count > 0);
    }
    return false;
  }

  //top of index header
  function table_header() {
    echo "<table class=\"main-table\">\n";
    echo "<tr style=\"background-color:#b40b0b\">
      <th class=\"table-header\" style=\"color:#FFFFFF\"><p>Image</p></th>
      <th class=\"table-header\" style=\"color:#FFFFFF\"><p>Name</p></th>
      <th class=\"table-header\" style=\"color:#FFFFFF\"><p>Neighbourhood</p></th>
      <th class=\"table-header\" style=\"color:#FFFFFF\"><p>Price</p></th>
      </tr>";
  }

  function table_contents($id, $image, $name, $neighbourhood, $price) {
    static $even = true;
    echo "<tr class=\"table-contents\"";
    if($even)
      echo " style=\"background-color:#e3a9a3\""; //change colour for odd rows
    echo "><td>
      <img class=listing-img src=\"$image\"></td><td>
      <a href=\"listingdetails.php?id=$id\">$name</a></td><td>$neighbourhood</td><td>$price</td>
      </tr>";
    $even = !$even; //alternate rows for colour changing
  }

  function table_end() {
    echo "</table>";
  }

  function start_cards_container() {
    echo '<div class="cards-container">';
  }

  // Function to display each listing as a card
  function display_listing_card($id, $image, $name, $neighbourhood, $price) {
    echo '<a href="listingdetails.php?id='.$id.'" class="card">';
    echo '<img class="listing-img" src="'.$image.'" alt="Listing Image">';
    echo '<h2 class="card-heading">'.$name.'</h2>';
    echo '<p>Neighbourhood: '.$neighbourhood.'</p>';
    echo '<p><strong>Price: '.$price.'</strong></p>';
    echo '</a>';
  }

  // Function to end the cards container
  function end_cards_container() {
    echo '</div>';
  }

  //function to generate all the products in the users watchlist
  function model_link($code, $name, $page)
  {
      echo "<a href=\"$page?id=$code\">$name</a>";
  }

  function watchlist_action($code, $name, $page)
  {
      echo "<a class=\"action\" href=\"$page?id=$code\">$name</a>";
  }

  // Function to display each listing as a card
  function display_watchlist_card($id, $image, $name, $neighbourhood, $price)
  {
      echo '<a href="listingdetails.php?id=' . $id . '" class="card">';
      echo '<img class="listing-img" src="' . $image . '" alt="Listing Image">';
      echo '<h2 class="card-heading">' . $name . '</h2>';
      echo '<p>Neighbourhood: ' . $neighbourhood . '</p>';
      echo '<p><strong>Price: ' . $price . '</strong></p>';
      echo '</a>';
  }
?>