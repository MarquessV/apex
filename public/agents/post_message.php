<?php

  require_once('../../private/initialize.php');

if(isset($_POST['submit']) && isset($_POST['plain_text'])) {
    
    if(!isset($_GET['id'])) {
      redirect_to('index.php');
    }
    
    $sender_result = find_agent_by_id($current_user['id']);
    $sender = db_fetch_assoc($sender_result);
    $private_key = $sender['private_key'];

    $id = $_GET['id'];
    $recipient_result = find_agent_by_id($id);
    $recipient = db_fetch_assoc($recipient_result);
    
    $plain_text = $_POST['plain_text'];
    $encrypted_text = pkey_encrypt($plain_text, $recipient['public_key']);

    $signature = create_signature($encrypted_text, $private_key);
    
    $message = [
      'sender_id' => $sender['id'],
      'recipient_id' => $recipient['id'],
      'cipher_text' => $encrypted_text,
      'signature' => $signature
    ];
    
    $result = insert_message($message);
    if($result === true) {
      // Just show the HTML below.
    } else {
      $errors = $result;
    }
    
  } else {
    redirect_to('index.php');
  }

?>

<!doctype html>

<html lang="en">
  <head>
    <title>Message Dropbox</title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <link rel="stylesheet" media="all" href="<?php echo DOC_ROOT . '/includes/styles.css'; ?>" />
  </head>
  <body>
    
    <a href="<?php echo url_for('/agents/index.php'); ?>">Back to List</a>
    <br/>

    <h1>Message Dropbox</h1>
    
    <div>      
      <p><strong>The message was successfully encrypted and saved.</strong></p>
        
      <div class="result">
        Message:<br />
        <?php echo h($encrypted_text); ?><br />
        <br />
        Signature:<br />
        <?php echo h($signature); ?>
      </div>
    </div>
    
  </body>
</html>
