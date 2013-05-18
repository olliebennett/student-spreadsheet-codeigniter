
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta property="og:site_name" content="The Student Spreadsheet" />
  <meta property="og:type" content="website" />
  <!--<meta property="og:image" content="...logo.png" />-->
  <meta property="fb:admins" content="504850777" />
  <!--<meta property="fb:app_id" content="<?php // TODO - echo 'APP_ID'; // TODO ?>" />-->
  <meta name="keywords" content="student spreadsheet university rented house accommodation bills share purchase receipt roommate room mate flat mate" /> 
<?php
// Determine meta description ($md), based on current page

switch($this->uri->uri_string()) {
  case 'purchases':
    $md = 'Purchases allow you to keep track of everything that\'s been bought by you and your housemates, and see what you all owe each other.';
  break;
  case 'items':
    $md = 'Items help you remember anything you\'ve borrowed or leant to friends or roommates. The perfect complement to the Purchases feature!';
  break;
  case 'new purchase':
    $md = 'Add a new purchase at The Student Spreadsheet, and notify all your room mates what they owe you for the latest utility bill or supermarket shop.';
  break;
  case 'new item':
    $md = 'Enter a new item, and remember what your housemates owe you. Stop worrying about not getting your things back!';
  break;
  case 'help':
    $md = 'Get all the guidance you need to effectively manage your finances, and make use of all the money-organising features this site offers!';
  break;
  case 'register':
    $md = 'Sign up to put a stop to the complicated money-lending situation in your university house or halls of residence now!';
  break;
    default; // including "Home"...
        $md = 'A free, easy-to-use online expenses manager to share bills, sort out your household finances, and remove the hassle of remembering who owes what!';
    //    This line represents the total number of characters (150) that Google will display from the meta description of a page. Right up to here: - 1234567890
    //    A free, easy-to-use online expenses manager to share bills, sort out your household finances, and remove the hassle of remembering who owes what!
    //    The Student Spreadsheet is a free, easy-to-use online expenses manager to share bills, sort out your household finances, and remove the hassle of ...
    break;
}
?>
  <meta name="description" content="<?php echo $md; ?>" />
