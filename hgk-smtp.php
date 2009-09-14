<?php
/*
Plugin Name: HGK SMTP
Plugin URI: http://www.ihagaki.com/wordpress/hgk-smtp-plugin
Description: Reconfigure WordPress email to use secure SMTP such as Gmail. 
Author: ihagaki.com
Author URI: http://www.ihagaki.com
Version: 1.1
*/

if (!class_exists("Hgk_Smtp")) {
    class Hgk_Smtp {
        private $hgk_smtpsrv;
        private $hgk_smtpssl;
        private $hgk_smtpport;
        private $hgk_smtpuser;
        private $hgk_smtppswd;
        private $hgk_mail;
        private $hgk_name;
        
        function init() {
            //  set defaults
            add_option('hgk_smtpsrv',  __('smtp.gmail.com', 'hgksmtp'));
            add_option('hgk_smtpssl',  true);
            add_option('hgk_smtpport', 465);
            add_option('hgk_smtpuser', __('johndoe@gmail.com', 'hgksmtp'));
            add_option('hgk_smtppswd', __('password', 'hgksmtp'));
            add_option('hgk_mail',     '');
            add_option('hgk_name',     __('John Doe', 'hgksmtp'));
            
            $this->updateState();
            
            add_action('admin_menu',     array(&$this, 'adminMenu'), 1);
            add_action('phpmailer_init', array(&$this, 'initSmtp'), 1);
        }
        
        function adminMenu() {
            add_options_page(__('HGK SMTP Options', 'hgksmtp'), __('HGK SMTP', 'hgksmtp'), 8, __FILE__, array(&$this, 'displayOptions'));
        }
        
        function displayOptions() {
            $hgk_action_url = admin_url('options-general.php?page=' . plugin_basename(__FILE__));

            if (isset($_POST['hgkaction'])) 
            {
                //  update options
                if ($_POST['hgkaction'] == 'update') 
                {
                    update_option('hgk_smtpsrv',   $_POST['hgk_smtpsrv']);
                    if (isset($_POST['hgk_smtpssl'])) { 
                        update_option('hgk_smtpssl', true); 
                    } else { 
                        update_option('hgk_smtpssl', false); 
                    }
                    update_option('hgk_smtpport',   trim($_POST['hgk_smtpport']));
                    update_option('hgk_smtpuser',   trim($_POST['hgk_smtpuser']));
                    update_option('hgk_smtppswd',   trim($_POST['hgk_smtppswd']));
                    update_option('hgk_mail',       trim($_POST['hgk_mail']));
                    update_option('hgk_name',       trim($_POST['hgk_name']));
                } 
                //  test email
                else if ($_POST['hgkaction'] == 'test') 
                {
                    echo '<div class="wrap"><br/><h2>' . __('Test Result', 'hgksmtp') . '</h2>';

                    $mail = $_POST['testemail'];
                    if (empty($mail)) {
                        $mail = form_option('admin_email');
                    }
                    
                    $msg = __('This is a test mail sent using HGK SMTP Plugin', 'hgksmtp');
                    if (wp_mail($mail, 'HGK SMTP Plugin Test', $msg)) {
                        echo '<p><strong>' . __('Test email sent. Please check your mailbox (and spam folder)', 'hgksmtp') . '<br/></strong></p><br/>';
                    }
                    echo '<form method="post" action="' . $hgk_action_url . '">';
                    echo '<input class="button" type="submit" value="' . __('Return to Options Page', 'hgksmtp') . '"/><br/>';
                    echo '</form></div>';
                    return;
                }
            }

            $this->updateState();
    ?>
            <div class="wrap">
                <?php screen_icon(); ?>
                <h2><?php _e('HGK SMTP Options', 'hgksmtp') ?></h2>
                <form method="post" action="<?php echo $hgk_action_url ?>&amp;updated=true">
                <input type="hidden" name="hgkaction" value="update" />

                <table>
                    <tr valign="top"><td colspan="3">
                    <h3><?php _e('SMTP Account', 'hgksmtp') ?></h3>
                    </td></tr>
                    <tr valign="top">
                        <th scope="row" align="right"><?php _e('SMTP server address:', 'hgksmtp') ?></th>
                        <td>&nbsp;</td>
                        <td><input name="hgk_smtpsrv" type="text" id="hgk_smtpsrv" value="<?php echo $this->hgk_smtpsrv; ?>" size="30" />
                        <br />
                        <font size="-2">&nbsp;<i><?php _e('Example: smtp.gmail.com', 'hgksmtp') ?></i></font><br/>&nbsp;</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" align="right"><?php _e('Use SSL connection:', 'hgksmtp') ?></th>
                        <td>&nbsp;</td>
                        <td>
                            <input name="hgk_smtpssl" type="checkbox" id="hgk_smtpssl" value="hgk_smtpssl"
                            <?php if($this->hgk_smtpssl == true) {?> checked="checked" <?php } ?> />
                        <font size="-2">&nbsp;<i><?php _e('Leave checked for gmail', 'hgksmtp') ?></i></font><br/>&nbsp;</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" align="right"><?php _e('SMTP server port:', 'hgksmtp') ?></th>
                        <td>&nbsp;</td>
                        <td><input name="hgk_smtpport" type="text" id="hgk_smtpport" value="<?php echo $this->hgk_smtpport; ?>" size="4" />
                        <font size="-2">&nbsp;<i><?php _e('Default for gmail is \'465\'', 'hgksmtp') ?></i></font><br/>&nbsp;</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" align="right"><?php _e('SMTP username:', 'hgksmtp') ?></th>
                        <td>&nbsp;</td>
                        <td><input name="hgk_smtpuser" type="text" id="hgk_smtpuser" value="<?php echo $this->hgk_smtpuser; ?>" size="30" />
                        <br />
                        <font size="-2">&nbsp;<i><?php _e('Example: johndoe@gmail.com', 'hgksmtp') ?></i></font><br/>&nbsp;</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" align="right"><?php _e('SMTP password:', 'hgksmtp') ?></th>
                        <td>&nbsp;</td>
                        <td><input name="hgk_smtppswd" type="text" id="hgk_smtppswd" value="<?php echo $this->hgk_smtppswd; ?>" size="30" /><br/>&nbsp;</td>
                    </tr>
                    <tr valign="top"><td colspan="3">
                        <h3><?php _e('Advanced Email Options', 'hgksmtp') ?></h3>
                    </td></tr>
                    <tr valign="top">
                        <th scope="row" align="right"><?php _e('Sender e-mail:', 'hgksmtp') ?></th>
                        <td>&nbsp;</td>
                        <td><input name="hgk_mail" type="text" id="hgk_mail" value="<?php echo $this->hgk_mail; ?>" size="30" />
                        <br />
                        <font size="-2">&nbsp;<i><?php _e('Sets From: address for outgoing messages. Overrides SMTP username (the default)', 'hgksmtp') ?></i></font><br/>
                        <font size="-2">&nbsp;<i><?php _e('Hint: in most cases you want to leave this entry blank', 'hgksmtp') ?></i></font><br/>&nbsp;</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" align="right"><?php _e('Sender name:', 'hgksmtp') ?></th>
                        <td>&nbsp;</td>
                        <td><input name="hgk_name" type="text" id="hgk_name" value="<?php echo $this->hgk_name; ?>" size="30" />
                        <br />
                        <font size="-2">&nbsp;<i><?php _e('Sets sender\'s Name for outgoing messages', 'hgksmtp') ?></i></font><br/>&nbsp;</td>
                    </tr>
                </table>

                <p class="submit">
                <input class="button-primary" type="submit" name="submit" value="<?php _e('Save Changes', 'hgksmtp') ?>" />
                </p>
                </form>

                <h2><?php _e('Send Test Email', 'hgksmtp') ?></h2>
                <p><font size="-2"><i><?php _e('Once the options are saved, you can test the connection by attemping to send an email.', 'hgksmtp'); ?></i></form></p>
                <form method="post" action="<?php echo $hgk_action_url ?>">
                <input type="hidden" name="hgkaction" value="test" />
                    <label><?php _e('Send test email to this address:', 'hgksmtp'); ?>
                        <input type="text" name="testemail" size="25" />
                        <input class="button" type="submit" value="<?php _e('Send Test', 'hgksmtp'); ?>" /></label><br />
                </form>
            </div>
    <?php
        }

        function initSmtp($pm) {
            $pm->IsSMTP();
            $pm->Host = $this->hgk_smtpsrv;
            $pm->Port = intval($this->hgk_smtpport);
            if ($this->hgk_smtpssl == true) {
                $pm->SMTPSecure = "ssl";
            }
            if (!empty($this->hgk_smtpuser) && !empty($this->hgk_smtppswd)) {
                $pm->SMTPAuth = true;
                $pm->Username = $this->hgk_smtpuser;
                $pm->Password = $this->hgk_smtppswd;
            }
            $pm->AddReplyTo($pm->From, $pm->FromName);
            if (!empty($this->hgk_mail)) {
                $pm->From = $this->hgk_mail;
            } elseif (!empty($this->hgk_smtpuser)) {
                $pm->From = $this->hgk_smtpuser;
            }
            if (!empty($this->hgk_name)) {
                $pm->FromName = $this->hgk_name;
            }
        }
        
        function updateState() {
            $this->hgk_smtpsrv    = stripslashes(get_option('hgk_smtpsrv'));
            $this->hgk_smtpssl    = stripslashes(get_option('hgk_smtpssl'));
            $this->hgk_smtpport   = stripslashes(get_option('hgk_smtpport'));
            $this->hgk_smtpuser   = stripslashes(get_option('hgk_smtpuser'));
            $this->hgk_smtppswd   = stripslashes(get_option('hgk_smtppswd'));
            $this->hgk_mail       = stripslashes(get_option('hgk_mail'));
            $this->hgk_name       = stripslashes(get_option('hgk_name'));
        }
    }
}

load_plugin_textdomain('hgksmtp', false, basename(dirname(__FILE__)) . '/langs');
        
if (class_exists("Hgk_Smtp")) {
    $hgk_smtp_instance = new Hgk_Smtp();
}

if (isset($hgk_smtp_instance)) {
    $hgk_smtp_instance->init();
}
?>
