<?php

  /* Template Name: Registration Page */

if (!isset($_GET['id']) && !isset($_POST['paypalName'])) {
  header('Location: https://utahlacrosseleague.com/signup/step-1-payment/');
  exit;
}

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
        if (strlen($_POST["playerName"]) > 0 &&
            strlen($_POST["birthday"]) > 0 &&
            strlen($_POST["email"]) > 0 &&
            strlen($_POST["phone"]) > 0 &&
            strlen($_POST["jerseyNumber"]) > 0 &&
            strlen($_POST["usLacrosseNumber"]) > 0 &&
            strlen($_POST["usLacrosseExpiration"]) > 0 &&
            strlen($_POST["usLacrosseZip"]) > 0 &&
            strlen($_POST["position"]) > 0 &&
            strlen($_POST["league"]) > 0 &&
            strlen($_POST["season"]) > 0 &&
            strlen($_POST["team"]) > 0 &&
            strlen($_POST["signature"]) > 0 &&
            strlen($_POST["bio"]) > 0 &&
            strlen($_POST["jerseyNumber"]) > 0 &&
            strlen($_POST["accept"]) > 0) {

          $paypalName = htmlentities($_POST["paypalName"]);
          $playerName = htmlentities($_POST["playerName"]);
          $birthday = htmlentities($_POST["birthday"]);
          $email = htmlentities($_POST["email"]);
          $phone = htmlentities($_POST["phone"]);
          $usLacrosseNumber = htmlentities($_POST["usLacrosseNumber"]);
          $usLacrosseExpiration = htmlentities($_POST["usLacrosseExpiration"]);
          $usLacrosseZip = htmlentities($_POST["usLacrosseZip"]);
          $bio = htmlentities($_POST["bio"]);
          $jerseyNumber = htmlentities($_POST["jerseyNumber"]);
          $position = htmlentities($_POST["position"]);
          $league = htmlentities($_POST["league"]);
          $season = htmlentities($_POST["season"]);
          $team = htmlentities($_POST["team"]);
          $signature = htmlentities($_POST["signature"]);
          $password = htmlentities($_POST["teamPassword"]);
          
          // Team validation
          // The Bad Astronauts: 32, badastros
          // Treebeards: 35, beards
          // Toads: 34, ribbit
          // Free Agents Men: 41
          // The Revengers: 80
          // Tropic Thunder: 86

          // $failed_validation = false;

          // if (intval($team) == 32) {
          //   if ($password != "badastros") {
          //     $failed_validation = true;
          //   }
          // }
          // if (intval($team) == 35) {
          //   if ($password != "beards") {
          //     $failed_validation = true;
          //   }
          // }
          // if (intval($team) == 34) {
          //   if ($password != "ribbit") {
          //     $failed_validation = true;
          //   }
          // }
          // if (intval($team) == 86) {
          //   if ($password != "psych") {
          //     $failed_validation = true;
          //   }
          // }
          // if (intval($team) == 80) {
          //   if ($password != "belax") {
          //     $failed_validation = true;
          //   }
          // }

          // if ($failed_validation) {
          //   $bio = $bio.'. Player attemped to register for team id: '.$team.' and entered: '.$password;
          //   $team = "41";
          // }

          // First create the player (post) and get the id
          $post = array(
            'post_title'      => $playerName,
            'post_content'    => $bio,
            'post_type'       => 'sp_player',
            'comment_status'  => 'closed'
          );
          $new_player_id = wp_insert_post($post);

          // Add the position, league, and season
          wp_set_object_terms($new_player_id, intval($position), 'sp_position');
          wp_set_object_terms($new_player_id, intval($league), 'sp_league');
          wp_set_object_terms($new_player_id, intval($season), 'sp_season');

          // Add all the meta options
          add_post_meta(intval($new_player_id), 'sp_team', intval($team));
          add_post_meta(intval($new_player_id), 'sp_current_team', intval($team));
          add_post_meta(intval($new_player_id), 'sp_number', $jerseyNumber);
          add_post_meta(intval($new_player_id), 'sp_birthday', $birthday);
          add_post_meta(intval($new_player_id), 'sp_phone', $phone);
          add_post_meta(intval($new_player_id), 'sp_email', $email);
          add_post_meta(intval($new_player_id), 'sp_paypal_name', $paypalName);
          add_post_meta(intval($new_player_id), 'sp_us_lacrosse_number', $usLacrosseNumber);
          add_post_meta(intval($new_player_id), 'sp_us_lacrosse_expiration', $usLacrosseExpiration);
          add_post_meta(intval($new_player_id), 'sp_us_lacrosse_zip', intval($usLacrosseZip));

          // Subscribe the player to the email list
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
            'name' => $playerName,
            'description' => 'player')
          );
          $res = curl_exec($ch);

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
        <p>
          <input size=40 type="hidden" placeholder="PayPal name" id="paypalName" name="paypalName" value="<?php echo $_GET['id'].'-'.$_GET['email']; ?>">
        </p>

        <p>Player info:</p>
        <p><input size=40 type="text" placeholder="First and last name" id="name" name="playerName" value="<?php if (isset($_POST["playerName"])){echo $_POST['playerName'];} ?>"></p>
        <p><input size=40 type="text" id="datepicker1" placeholder="Birthdate ie. mm/dd/yyyy" name="birthday" value="<?php if (isset($_POST["birthday"])){echo $_POST['birthday'];} ?>"></p>
        <p><input size=40 type="text" placeholder="Email" id="email" name="email" value="<?php if (isset($_POST["email"])){echo $_POST['email'];} ?>"></p>
        <p><input size=40 type="text" placeholder="Phone number" id="phone" name="phone" value="<?php if (isset($_POST["phone"])){echo $_POST['phone'];} ?>"></p>
        <p><input size=40 type="text" placeholder="US Lacrosse Number" id="usLacrosseNumber" name="usLacrosseNumber" value="<?php if (isset($_POST["usLacrosseNumber"])){echo $_POST['usLacrosseNumber'];} ?>"></p>
        <p><input size=40 type="text" id="datepicker2" placeholder="US Lacrosse Expiration" name="usLacrosseExpiration" value="<?php if (isset($_POST["usLacrosseExpiration"])){echo $_POST['usLacrosseExpiration'];} ?>"></p>
        <p><input size=40 type="text" placeholder="Zip Code for US Lacrosse number" id="usLacrosseZip" name="usLacrosseZip" value="<?php if (isset($_POST["usLacrosseZip"])){echo $_POST['usLacrosseZip'];} ?>"></p>
        <p>*If you don't know your us lacrosse number you can look it up <a href="https://usl.ebiz.uapps.net/PersonifyEbusiness/Default.aspx?TabID=266&_ga=1.167454270.1443606580.1463102145" target="_blank">here.</a></p>
        
        <p>
          Jersey number: <strong>all players need a jersey number</strong>. If you don't know what your jersey number is yet just choose something and it can be changed later.<br>
          <input size=5 type="text" placeholder="99" id="jerseyNumber" name="jerseyNumber" value="<?php if (isset($_POST["jerseyNumber"])){echo $_POST['jerseyNumber'];} ?>">
        </p>
        
        <p>
          Please write a short player bio<br>
          <textarea rows="5" cols="50" name="bio" id="bio"><?php if (isset($_POST["bio"])){echo $_POST['bio'];} ?></textarea>
        </p>

        
        <p>
          Position: <br>
          <select name="position" id="position">
        <?php  // Getting the list of positions
          $positions_term_ids = $wpdb->get_results( "
            SELECT term_id
            FROM wp_term_taxonomy
            WHERE taxonomy = 'sp_position'
          ");

          echo '<option>--Select one--</option>';

          foreach ($positions_term_ids as $term_id) {
            $position = $wpdb->get_results( "
              SELECT *
              FROM wp_terms
              WHERE term_id = ".$term_id->term_id
            );
            // var_dump($position);
            if ($_POST["position"] === $position[0]->term_id) {
              echo '<option value="'.$position[0]->term_id.'" selected>'.$position[0]->name.'</option>';
            } else {
              echo '<option value="'.$position[0]->term_id.'">'.$position[0]->name.'</option>';
            }
          } ?>
          </select>
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
          Team: <br>
          <select name="team" id="team">
        <?php  // Getting the list of positions
          $teams = $wpdb->get_results( "
            SELECT *
            FROM wp_posts
            WHERE post_type = 'sp_team' AND post_status = 'publish'
          ");

          echo '<option>--Select one--</option>';

          foreach ($teams as $team) {
            
            if ($_POST["team"] === $team->ID) {
              echo '<option value="'.$team->ID.'" selected>'.$team->post_title.'</option>';
            } else {
              echo '<option value="'.$team->ID.'">'.$team->post_title.'</option>';
            }
          } ?>
          </select>
        </p>

        <p>
          Team Password (<strong>Optional</strong>, only some teams have passwords. Speak with your team captain to find out if you need one.) <br>
          <input type="password" id="teamPassword" name="teamPassword" placeholder="Team Password" value="<?php if (isset($_POST['teamPassword'])){echo $_POST['teamPassword'];} ?>">
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
  </main>
</div>

<?php get_footer(); ?>

<script type="text/javascript">
  new Pikaday({ 
    field: document.getElementById('datepicker1'),
    format: 'MM/DD/YYYY'
  });
  new Pikaday({ 
    field: document.getElementById('datepicker2'),
    format: 'MM/DD/YYYY'
  });
</script>
