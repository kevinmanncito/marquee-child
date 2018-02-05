<?php
/**
 *
 * Template Name: Payment Page
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

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_footer(); ?>

<script type="text/javascript">

//Client token
var clientToken = "<?php echo($clientToken = $gateway->clientToken()->generate()); ?>";

// Fetch the button you are using to initiate the PayPal flow
var paypalButton = document.getElementById('paypal-button');

var AMOUNT = 1.0;

// Create a Client component
braintree.client.create({
    authorization: clientToken
}, function (clientErr, clientInstance) {

    // Create PayPal component
    braintree.paypal.create({
        client: clientInstance
    }, function (err, paypalInstance) {

        paypalButton.addEventListener('click', function () {
            // Tokenize here!
            paypalInstance.tokenize({
                flow: 'checkout', // Required
                amount: AMOUNT, // Required
                currency: 'USD', // Required
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
                            window.location.href = "/signup/step-2-player-info?q=&id="+json.transaction_id+"&email="+tokenizationPayload.details.email;
                        }
                    }
                    var data = JSON.stringify({
                        "amount": AMOUNT,
                        "nonce": tokenizationPayload.nonce
                    });
                    xhr.send(data);

                } else {
                    console.log('sorry something went wrong');
                }
            });
        });
    });
});

</script>
