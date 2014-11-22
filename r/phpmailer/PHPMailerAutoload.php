<?php
/**
 * PHPMailer SPL autoloader.
 * PHP Version 5
 * @package PHPMailer
 * @link https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2012 - 2014 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * PHPMailer SPL autoloader.
 * @param string $classname The name of the class to load
 */
function PHPMailerAutoload($classname)
{
    //Can't use __DIR__ as it's only in PHP 5.3+
    $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'class.'.strtolower($classname).'.php';
    if (is_readable($filename)) {
        require $filename;
    }
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
    //SPL autoloading was introduced in PHP 5.1.2
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
        spl_autoload_register('PHPMailerAutoload', true, true);
    } else {
        spl_autoload_register('PHPMailerAutoload');
    }
} else {
    /**
     * Fall back to traditional autoload for old PHP versions
     * @param string $classname The name of the class to load
     */
    function __autoload($classname)
    {
        PHPMailerAutoload($classname);
    }
}

function email($to,$subject,$content){
	$myEmail = "hello@robertakarobin.com";
	
	require "phpmailer/PHPMailerAutoload.php";
	if(empty($to)) $to = $myEmail;
	if(!$content = sanitize($content)) throw new error("You didn't include a message!");
	if(strlen($content) < 20) throw new error("Could you make your message more than 20 characters?");
	if(!filter_var($to, FILTER_VALIDATE_EMAIL)) throw new error("This e-mail address seems odd.");
	
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = "secure139.inmotionhosting.com";
	$mail->Username = $myEmail;
	$mail->Password = "Bingoball1!";
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = "ssl";
	$mail->Port = "465";
	$mail->isHTML(false);
	$mail->ContentType = "text/plain";
	$mail->WordWrap = 0;
	$mail->From = $myEmail;
	$mail->addCC( $myEmail );
	$mail->FromName = "Robert AKA Robin Thomas";
	$mail->addAddress($to);
	$mail->Subject = $subject;
	$mail->Body = sanitize($content);
	
	if(!$mail->send()) throw new error("The message didn't go through. :( Try e-mailing me directly!");
    else return true;
}
