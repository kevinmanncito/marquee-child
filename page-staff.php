<?php

  /* Template Name: Staff Registration Page */

?>

<?php get_header(); ?>

<div id="main" class="full">
  <div id="post-area" class="full">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div class="breadcrumb">
      <?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
    </div><!--breadcrumb-->
    <h1 class="headline"><?php the_title(); ?></h1>
    <div id="content-area" class="full">
    
    <?php 
      $show_form = true;
      $show_error = false;
      if (isset($_POST["submit"])) {
        if (strlen($_POST["staffName"]) > 0 &&
            strlen($_POST["email"]) > 0 &&
            strlen($_POST["phone"]) > 0 &&
            $_POST["role"] !== "unselected" &&
            $_POST["availability"] !== "unselected" &&
            $_POST["experience"] !== "unselected") {

          $staffName = htmlentities($_POST["staffName"]);
          $email = htmlentities($_POST["email"]);
          $phone = htmlentities($_POST["phone"]);
          $bio = htmlentities($_POST["bio"]);
          $role = htmlentities($_POST["role"]);
          $availability = htmlentities($_POST["availability"]);
          $experience = htmlentities($_POST["experience"]);

          // Format the bio with additional info
          $bio = 'Availability: ' . ucwords($availability) . '<br>' .
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
          $roles = array('referee', 'staff', 'either');
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
  </div><!--post-area-full-->
</div><!--main-full-->

<?php get_footer(); ?>