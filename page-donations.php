<?php
/**
 *
 * Template Name: Donations Page
 *
 * @package Rookie
 */

require __DIR__ . '/vendor/autoload.php';
$access_token = json_decode(file_get_contents(get_stylesheet_directory_uri() .'/config.json'))->access_token;
$gateway = new Braintree_Gateway(array(
  'accessToken' => $access_token,
));

get_header(); ?>

    <div id="primary" class="content-area content-area-full-width">
        <main id="main" class="site-main" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php
                if ( in_array( get_post_type(), array( 'sp_player', 'sp_staff', 'sp_team' ) ) ) {
                    get_template_part( 'content', 'nothumb' );
                } else {
                    get_template_part( 'content', 'page' );
                }
                ?>

            <?php endwhile; // end of the loop. ?>

            <form id='donation-form'>
              <p>
                Please enter the player's name who you would like to make the donation for (optional)<br>
                <input type="text" id="player-name" name="playerName" placeholder="First Last">
              </p>
              <p>
                Team (optional): <br>
                <select name="team" id="team">
                <?php  // Getting the list of teams
                  $teams = $wpdb->get_results( "
                    SELECT *
                    FROM wp_posts
                    WHERE post_type = 'sp_team' AND post_status = 'publish'
                  ");

                  echo '<option>--Select one--</option>';

                  foreach ($teams as $team) {
                    echo '<option value="'.$team->ID.'">'.$team->post_title.'</option>';
                  }
                ?>
                </select>
              </p>
              <p>
                Please select your sponsorship package<br>
                <select name="package" id="package">
                  <option value="">--Select--</option>
                  <option value="7400">League - $7,400</option>
                  <option value="2500">Gold - $2,500</option>
                  <option value="1000">Silver - $1,000</option>
                  <option value="500">Bronze - $500</option>
                </select>
              </p>
              <p>
                Or
              </p>
              <p>
                Manually enter the amount you would like to donate<br>
                <span>$<input type="number" id="donation-amount" name="donationAmount" placeholder="20.00" required></span>
              </p>
              <script src="https://www.paypalobjects.com/api/button.js?" data-merchant="braintree" data-id="paypal-button" data-button="paynow" data-size="medium" data-button_type="submit" data-button_disabled="false" ></script>
            </form>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_footer(); ?>

<script type="text/javascript">

document.getElementById('package').addEventListener('change', function(e) {
  try {
    var packageAmount = parseFloat(e.target.value);
    document.getElementById('donation-amount').value = packageAmount;
  } catch {}
});

document.getElementById('donation-amount').addEventListener('change', function(e) {
  document.getElementById('package').selectedIndex = 0;
});

//Client token
var clientToken = "<?php echo($clientToken = $gateway->clientToken()->generate()); ?>";

// Fetch the button you are using to initiate the PayPal flow
var paypalButton = document.getElementById('paypal-button');

var AMOUNT = 0.0;


// Create a Client component
braintree.client.create({
    authorization: clientToken
}, function (clientErr, clientInstance) {

  // Create PayPal component
  braintree.paypal.create({
      client: clientInstance
  }, function (err, paypalInstance) {

    document.getElementById('donation-form').addEventListener('submit', function(e) {
      e.preventDefault();
      try {
        AMOUNT = parseFloat(e.target.donationAmount.value);
      } catch {
        return;
      }

      // Tokenize here!
      paypalInstance.tokenize({
        flow: 'checkout', // Required
        amount: AMOUNT, // Required
        currency: 'USD', // Required
        useraction: 'commit',
        locale: 'en_US'
      }, function (err, tokenizationPayload) {
        if (!err) {
            var xhr = new XMLHttpRequest();
            var url = "/wp-content/themes/marquee-child/braintree.php";
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-type", "application/json");
            xhr.onreadystatechange = function () { 
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var json = JSON.parse(xhr.responseText);
                    // Send an email with payment info

                    // Display success page
                    var container = document.getElementById('main');
                    container.innerHTML = "<div class='entry-content'>Thank you for your donation! You will recieve a confirmation email from PayPal.</div>";
                    // window.location.href = "/signup/step-2-player-info?q=&id="+json.transaction_id+"&email="+tokenizationPayload.details.email;
                }
            }
            var data = JSON.stringify({
                "amount": AMOUNT,
                "nonce": tokenizationPayload.nonce
            });
            xhr.send(data);

        } else {
          console.log('something went wrong');
        }
      });
    });
  });
});

</script>
