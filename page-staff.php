<?php

  /* Template Name: Staff Registration Page */

?>

<?php get_header(); ?>

<div id="primary" class="content-area content-area-full-width">
  <main id="main" class="site-main" role="main">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <div class="entry-header">
      <h1 class="entry-title"><?php the_title(); ?></h1>
    </div>

    <div id="content-area" class="entry-content">

    <?php 
      $show_form = true;
      $show_error = false;
      if (isset($_POST["submit"])) {
        if (strlen($_POST["staffName"]) > 0 &&
            strlen($_POST["email"]) > 0 &&
            strlen($_POST["phone"]) > 0 &&
            $_POST["role"] !== "unselected" &&
            $_POST["availability"] !== "unselected" &&
            $_POST["experience"] !== "unselected" &&
            $_POST["location"] !== "unselected") {

          $staffName = htmlentities($_POST["staffName"]);
          $email = htmlentities($_POST["email"]);
          $phone = htmlentities($_POST["phone"]);
          $bio = htmlentities($_POST["bio"]);
          $role = htmlentities($_POST["role"]);
          $availability = htmlentities($_POST["availability"]);
          $location = htmlentities($_POST["location"]);
          $experience = htmlentities($_POST["experience"]);

          // Format the bio with additional info
          $bio = 'Availability: ' . ucwords($availability) . '<br>' .
                 'Location availability: ' . $location . '<br><br>' .
                 'Experience: ' . $experience . '<br><br>' .
                 $bio;
          // First create the player (post) and get the id
          $post = array(
            'post_title'      => $staffName,
            'post_content'    => $bio,
            'post_type'       => 'sp_staff',
            'comment_status'  => 'closed'
          );
          $new_staff_id = wp_insert_post($post);

          // Add all the meta options
          add_post_meta(intval($new_staff_id), 'sp_phone', $phone);
          add_post_meta(intval($new_staff_id), 'sp_email', $email);
          add_post_meta(intval($new_staff_id), 'sp_role', ucwords($role));

          // Subscribe the staff member to the email list
          $ch = curl_init(); 
          $mailgun_key = json_decode(file_get_contents(get_stylesheet_directory_uri() .'/config.json'))->mailgun_key;
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
          curl_setopt($ch, CURLOPT_USERPWD, 'api:'.$mailgun_key);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
          curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/lists/info@mg.nu-lacrosse.com/members");
          curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'subscribed' => true,
            'address' => $email,
            'name' => $staffName,
            'description' => 'staff')
          );
          $res = curl_exec($ch);

          // Show the thank you message
          echo '<div>Thank you for registering! We will email you with your staff schedule soon.</div>';

          $show_form = false;
        } else {
          $show_error = true;
        }
      }
    ?>
    <?php if($show_form) { ?>
      <?php the_content(); ?>
      <?php if($show_error) { 
        echo '<p style="color: red;">Please fill out all items</p>';
      }?>
      <form method="POST">

        <p>Staff member info:</p>
        <p><input size=40 type="text" placeholder="First and last name" id="name" name="staffName" value="<?php if (isset($_POST["staffName"])){echo $_POST['staffName'];} ?>"></p>
        <p><input size=40 type="text" placeholder="Email" id="email" name="email" value="<?php if (isset($_POST["email"])){echo $_POST['email'];} ?>"></p>
        <p><input size=40 type="text" placeholder="Cell phone number" id="phone" name="phone" value="<?php if (isset($_POST["phone"])){echo $_POST['phone'];} ?>"></p>

        <p>
        <?php 
          $roles = array('referee', 'staff', 'coach', 'any');
        ?>
          Desired role: <br>
          <select name="role" id="role">
            <option value="unselected">--Select one--</option>
          <?php 
            foreach ($roles as $role) {
              if ($_POST["role"] === $role) {
                echo '<option value="'.$role.'" selected>'.ucwords($role).'</option>';
              } else {
                echo '<option value="'.$role.'">'.ucwords($role).'</option>';
              }
            }
          ?>
          </select>
        </p>

        <p>
        <?php 
          $options = array('mondays', 'tuesdays', 'wednesdays', 'thursdays', 'tuesday and thursday', 'monday and wednesday', 'all', 'other');
        ?>
          Availability: <br>
          <select name="availability" id="availability">
            <option value="unselected">--Select one--</option>
          <?php 
            foreach ($options as $option) {
              if ($_POST["availability"] === $option) {
                echo '<option value="'.$option.'" selected>'.ucwords($option).'</option>';
              } else {
                echo '<option value="'.$option.'">'.ucwords($option).'</option>';
              }
            }
          ?>
          </select>
        </p>

        <p>
          Location availability: <br>
          <select name="location" id="location">
        <?php  // Getting the list of positions
          $location_term_ids = $wpdb->get_results( "
            SELECT term_id
            FROM wp_term_taxonomy
            WHERE taxonomy = 'sp_venue'
          ");

          echo '<option value="unselected">--Select one--</option>';

          foreach ($location_term_ids as $location_id) {
            $location = $wpdb->get_results( "
              SELECT *
              FROM wp_terms
              WHERE term_id = ".$location_id->term_id
            );
            // var_dump($location);
            if ($_POST["location"] === $location[0]->name) {
              echo '<option value="'.$location[0]->name.'" selected>'.$location[0]->name.'</option>';
            } else {
              echo '<option value="'.$location[0]->name.'">'.$location[0]->name.'</option>';
            }
          } 

          echo '<option value="all">All</option>';
          ?>

          </select>
        </p>

        <p>
        <?php 
          $options = array('0', '1', '2', '3', '4', '5+');
        ?>
          Years involved with lacrosse: <br>
          <select name="experience" id="experience">
            <option value="unselected">--Select one--</option>
          <?php 
            foreach ($options as $option) {
              if ($_POST["experience"] === $option) {
                echo '<option value="'.$option.'" selected>'.ucwords($option).'</option>';
              } else {
                echo '<option value="'.$option.'">'.ucwords($option).'</option>';
              }
            }
          ?>
          </select>
        </p>

        <p>
          Please describe your experience with lacrosse, officiating and/or helping at the table.<br>
          <textarea rows="5" cols="50" name="bio" id="bio"><?php if (isset($_POST["bio"])){echo $_POST['bio'];} ?></textarea>
        </p>

        <p>
          <input type="submit" id="submit" name="submit">
        </p>

      </form>

    <?php  } ?>

    </div><!--content-area-full-->
    <?php endwhile; endif; ?>
  </main>
</div>

<?php get_footer(); ?>