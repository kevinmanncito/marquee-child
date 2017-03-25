<?php

  /* Template Name: Team Registration Page */

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
        if (strlen($_POST["teamName"]) > 0 &&
            strlen($_POST["teamCaptain"]) > 0 &&
            strlen($_POST["email"]) > 0 &&
            strlen($_POST["contactPhone"]) > 0 && 
            strlen($_POST["signature"]) > 0 && 
            strlen($_POST["accept"]) > 0) {

          $teamName = htmlentities($_POST["teamName"]);
          $teamCaptain = htmlentities($_POST["teamCaptain"]);
          $email = htmlentities($_POST["email"]);
          $contactPhone = htmlentities($_POST["contactPhone"]);
          $teamCaptain2 = htmlentities($_POST["teamCaptain2"]);
          $email2 = htmlentities($_POST["email2"]);
          $contactPhone2 = htmlentities($_POST["contactPhone2"]);
          $bio = htmlentities($_POST["bio"]);
          $league = htmlentities($_POST["league"]);
          $season = htmlentities($_POST["season"]);

          $postBody = "Team Captain:<br>".
                      $teamCaptain ."<br>".
                      $email ."<br>".
                      $contactPhone ."<br><br>".
                      "Team Captain2:<br>".
                      $teamCaptain2 ."<br>".
                      $email2 ."<br>".
                      $contactPhone2 ."<br><br>".
                      "Additional Team Info:<br>".
                      $bio;

          // First create the team (post) and get the id
          $post = array(
            'post_title'      => $teamName,
            'post_content'    => $postBody,
            'post_type'       => 'sp_team',
            'post_status'     => 'publish',
          );
          $new_team_id = wp_insert_post($post);

          // Add to the league and season
          wp_set_object_terms($new_team_id, intval($league), 'sp_league');
          wp_set_object_terms($new_team_id, intval($season), 'sp_season');

          // Show the thank you message
          echo '<div>Thank you for registering! We look forward to seeing you on the field this summer!</div>';

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
        <p>Team info:</p>
        <p><input size=40 type="text" placeholder="Team name*" id="teamName" name="teamName" value="<?php if (isset($_POST["teamName"])){echo $_POST['teamName'];} ?>"></p>

        <p><input size=40 type="text" placeholder="Team captain*" id="teamCaptain" name="teamCaptain" value="<?php if (isset($_POST["teamCaptain"])){echo $_POST['teamCaptain'];} ?>"></p>

        <p><input size=40 type="text" placeholder="Contact email*" id="email" name="email" value="<?php if (isset($_POST["email"])){echo $_POST['email'];} ?>"></p>

        <p><input size=40 type="text" placeholder="Contact phone*" id="contactPhone" name="contactPhone" value="<?php if (isset($_POST["contactPhone"])){echo $_POST['contactPhone'];} ?>"></p>

        <p><input size=40 type="text" id="teamCaptain2" placeholder="Team Captain 2" name="teamCaptain2" value="<?php if (isset($_POST["teamCaptain2"])){echo $_POST['teamCaptain2'];} ?>"></p>

        <p><input size=40 type="text" id="email2" placeholder="Contact email 2" name="email2" value="<?php if (isset($_POST["email2"])){echo $_POST['email2'];} ?>"></p>

        <p><input size=40 type="text" placeholder="Contact phone 2" id="contactPhone2" name="contactPhone2" value="<?php if (isset($_POST["contactPhone2"])){echo $_POST['contactPhone2'];} ?>"></p>
        
        <p>
          Additional team info<br>
          <textarea rows="5" cols="50" name="bio" id="bio"><?php if (isset($_POST["bio"])){echo $_POST['bio'];} ?></textarea>
        </p>        

        <p>
          League: <br>
          <select name="league" id="league">
        <?php  // Getting the list of positions
          $league_term_ids = $wpdb->get_results( "
            SELECT term_id
            FROM wp_term_taxonomy
            WHERE taxonomy = 'sp_league'
          ");

          foreach ($league_term_ids as $term_id) {
            $league = $wpdb->get_results( "
              SELECT *
              FROM wp_terms
              WHERE term_id = ".$term_id->term_id
            );
            // var_dump($position);
            if ($_POST["league"] === $league[0]->term_id) {
              echo '<option value="'.$league[0]->term_id.'" selected>'.$league[0]->name.'</option>';
            } else {
              echo '<option value="'.$league[0]->term_id.'">'.$league[0]->name.'</option>';
            }
          } ?>
          </select>
        </p>

        <p>
          Season: <br>
          <select name="season" id="season">
        <?php
          $season_term_ids = $wpdb->get_results( "
            SELECT term_id
            FROM wp_term_taxonomy
            WHERE taxonomy = 'sp_season'
            ORDER BY term_id desc
          ");

          foreach ($season_term_ids as $term_id) {
            $season = $wpdb->get_results( "
              SELECT *
              FROM wp_terms
              WHERE term_id = ".$term_id->term_id
            );
            // var_dump($season);
            if ($_POST["season"] === $season[0]->term_id) {
              echo '<option value="'.$season[0]->term_id.'" selected>'.$season[0]->name.'</option>';
            } else {
              echo '<option value="'.$season[0]->term_id.'">'.$season[0]->name.'</option>';
            }
            break;
          } ?>
          </select>
        </p>

        <p>
          <strong>Waiver: </strong>You are only eligible to play if you accept the following Waiver:  "In consideration of my membership in US Lacrosse, and my participation in US Lacrosse sanctioned events, I agree to the following: 1. I will only participate in those US Lacrosse competitions for which I believe I am physically and psychologically prepared to compete. 2. I am fully aware of and appreciate the risks, including the risk of catastrophic injury, paralysis and even death, as well as other damages and losses associated with participation in a lacrosse event. I further agree on behalf of myself, my heirs, and personal representatives, that US Lacrosse, the host organization, and sponsors of any US Lacrosse sanctioned event, along with the coaches, volunteers, employees, agents, officers, and directors of these organizations, shall not be liable for any injury, loss of life, or other loss or damage occurring as a result of my participation in the event. US LACROSSE CODE OF CONDUCT Players, coaches, officials, parents and spectators are to conduct themselves in a manner that "Honors the Game" and demonstrates respect to other players, coaches, officials, parents and fans. In becoming a member of the lacrosse community an individual assumes certain obligations and responsibilities to the game of lacrosse and its participants. The essential elements in this "Code of Conduct" are HONESTY and INTEGRITY. Those who conduct themselves in a manner that reflects these elements will bring credit to the sport of lacrosse, themselves, their team and their organization. It is only through such conduct that our sport can continue to earn and maintain a positive image and make its full contribution to amateur sports in the United States and around the world. US Lacrosse supports the following behaviors for those who participate in the sport or are involved in any way with US Lacrosse. The following essential elements of the "Code of Conduct" must be followed: • Sportsmanship and teaching the concepts of fair play are essential to the game and must be taught at all levels and developed both at home and on the field during practices and games. • The value of good sportsmanship, the concepts of fair play, and the skills of the game should always be placed above winning. • The safety and welfare of the players are of primary importance. • Coaches must always be aware of the tremendous influence they have on their players. They are to strive to be positive role models in dealing with young people, as well as adults. • Coaches should always demonstrate positive behaviors and reinforce them to players, parents, officials and spectators alike. Players should be specifically encouraged and positively reinforced by coaches to demonstrate respect for teammates, opponents, officials and spectators. • Players should always demonstrate positive behavior and respect toward teammates, opponents, coaches, officials, parents and spectators. • Coaches, players, parents and spectators are expected to demonstrate the utmost respect for officials and reinforce that respect to players/teammates. Coaches are also expected to educate their players as to the important role of lacrosse officials and reinforce the ideal of respect for the official to players/teammates. • Grievances or misunderstandings between coaches, officials or any other parties involved with the sport should be communicated through the proper channels and procedures, never on or about the field of play in view of spectators or participants. • Officials are professionals and are therefore expected to conduct themselves as such and in a manner that demonstrates total impartiality, courtesy and fairness to all parties. • Spectators involved with the game must never permit anyone to openly or maliciously criticize badger, harass or threaten an official, coach, player or opponent. • Coaches must be able to demonstrate a solid knowledge of the rules of lacrosse, and should adhere to the rules in both the letter and the spirit of the game. • Coaches should provide a basic knowledge of the rules to both players and spectators within his/her program. Attempts to manipulate rules in an effort to take unfair advantage of an opponent, or to teach deliberate unsportsmanlike conduct, is considered unacceptable conduct. • Eligibility requirements, at all levels of the game, must be followed. Rules and requirements such as age, previous level of participation, team transfers, etc, have been established to encourage and maximize participation, fair play and to promote safety." -US Lacrosse. This waiver applies to the Northern Utah Lacrosse League. By checking the box below, you agree to that written above, and that you are playing at your own risk and that you are solely responsible for any injuries you incur.
          <br>
          <br>
          Accept: 
          <input type="radio" id="accept" name="accept" value="accept">
        </p>

        <p>Electronic Signature:
          <input type="text" id="signature" name="signature" placeholder="Full name" value="<?php if (isset($_POST["signature"])){echo $_POST['signature'];} ?>">
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