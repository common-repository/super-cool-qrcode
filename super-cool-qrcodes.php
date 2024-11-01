<?php
/*
Plugin Name: Super Cool QRCode
Plugin URI: http://www.incerteza.org/blog/projetos/super-cool-qrcode/
Description: Insert a <a href="http://en.wikipedia.org/wiki/QR_Code" target="_blank">QR code</a> with the current article or blog url, or any other text.
Author: Matias S.
Author URI: matias@incerteza.org
Version: 0.0.7
Author URI: http://www.incerteza.org/blog/
Text Domain: scqrcode
Domain Path: /languages
*/
$scqrcode_domain = "scqrcode";
$scqrcode_dir = PLUGINDIR . '/' . basename(dirname(__FILE__));

/* Plugin Admin Localization */
if( !load_plugin_textdomain($scqrcode_domain, '/wp-content/languages') AND is_admin() )
    load_plugin_textdomain($scqrcode_domain, $scqrcode_dir . '/languages');

/* Functions */
function scqrcode_url() {
    return is_single() ? get_permalink() : get_settings('siteurl');
}

/* Widget */
add_action( 'widgets_init', 'scqrcode_init' );
function scqrcode_init() {
    register_widget( 'scqrcode_widget' );
}

class scqrcode_widget extends WP_Widget {
    function scqrcode_widget() {
        global $scqrcode_domain, $scqrcode_dir;
        $widget_ops = array( 'classname' => 'scqrcode', 'description' => __('A QR Code with the current url, or any other content', $scqrcode_domain) );
        // $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'scqrcode-widget' );
        $this->WP_Widget( 'scqrcode-widget', 'Super Cool QRCode', $widget_ops, $control_ops );
    }
    
    function widget($args, $instance){
        extract($args);
        if ( $instance['title'] ) $title =  apply_filters('widget_title', $instance['title']);
        $content = empty($instance['content']) ? scqrcode_url() : $instance['content'];
        $before_content = $instance['before_content'];
        $after_content = $instance['after_content'];
        $size = empty($instance['size']) ? '125' : $instance['size'];
        $encoding = empty($instance['encoding']) ? 'UTF-8' : $instance['encoding'];
        $error = empty($instance['error']) ? 'L' : $instance['error'];
        $margin = empty($instance['margin']) ? '2' : $instance['margin'];
        $border = empty($instance['border']) ? '0' : $instance['border'];
        ?>
            <?php echo $before_widget; ?>
                <?php if ( $title ) echo $before_title . $title . $after_title; ?>
                <?php echo $before_content ?><img src="http://chart.apis.google.com/chart?cht=qr&chs=<?php echo $size ?>x<?php echo $size ?>&chl=<?php echo urlencode($content) ?>&choe=<?php echo $encoding ?>&chld=<?php echo $error ?>|<?php echo $margin ?>" width="<?php echo $size ?>" height="<?php echo $size ?>" border="<?php echo $border ?>" /><?php echo $after_content ?>
            <?php echo $after_widget; ?>
        <?php
    }

    function form($instance){
        global $scqrcode_domain, $scqrcode_dir;
        $instance = wp_parse_args( (array) $instance, array( 'title' => '',
                                                             'content' => '',
                                                             'before_content' => '<div align="center">',
                                                             'after_content' => '</div>',
                                                             'size' => '125',
                                                             'encoding' => 'UTF-8',
                                                             'error' => 'L',
                                                             'margin' => '2',
                                                             'border' => '0',
                                                           ) );
        $title = htmlspecialchars($instance['title']);
        $content = htmlspecialchars($instance['content']);
        $before_content = htmlspecialchars($instance['before_content']);
        $after_content = htmlspecialchars($instance['after_content']);
        $size = htmlspecialchars($instance['size']);
        $encoding = htmlspecialchars($instance['encoding']);
        $error = htmlspecialchars($instance['error']);
        $margin = htmlspecialchars($instance['margin']);
        $border = htmlspecialchars($instance['border']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', $scqrcode_domain) ?>:
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('content'); ?>"><?php echo __('Content', $scqrcode_domain) ?>:
            <input class="widefat" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" type="text" value="<?php echo attribute_escape($content); ?>" />
        </label><br /><?php __('If you leave <strong>Content</strong> field blank the current page url will be used', $scqrcode_domain) ?></p>
        <h3><?php echo __('Content wrapping', $scqrcode_domain) ?></h3>
        <p><label for="<?php echo $this->get_field_id('before_content'); ?>"><?php echo __('Before content', $scqrcode_domain) ?>:
            <input class="widefat" id="<?php echo $this->get_field_id('before_content'); ?>" name="<?php echo $this->get_field_name('before_content'); ?>" type="text" value="<?php echo attribute_escape($before_content); ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('after_content'); ?>"><?php echo __('After content', $scqrcode_domain) ?>:
            <input class="widefat" id="<?php echo $this->get_field_id('after_content'); ?>" name="<?php echo $this->get_field_name('after_content'); ?>" type="text" value="<?php echo attribute_escape($after_content); ?>" />
        </label></p>
        <h3><?php echo __('Advanced options', $scqrcode_domain) ?></h3>
        <p><label for="<?php echo $this->get_field_id('size'); ?>"><?php echo __('Size', $scqrcode_domain) ?>:
            <input id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" type="text" value="<?php echo attribute_escape($size); ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('encoding'); ?>"><?php echo __('Encoding', $scqrcode_domain) ?>:
            <select id="<?php echo $this->get_field_id('encoding'); ?>" name="<?php echo $this->get_field_name('encoding'); ?>">
                <option value="ISO-8859-1" <?php if ( $encoding == 'ISO-8859-1' ) echo 'selected="selected"'; ?> />ISO-8859-1</option>
                <option value="Shift_JIS" <?php if ( $encoding == 'Shift_JIS' ) echo 'selected="selected"'; ?> />Shift_JIS</option>
                <option value="UTF-8" <?php if ( $encoding == 'UTF-8' ) echo 'selected="selected"'; ?> />UTF-8</option>
            </select>
        </label></p>
        <p><label for="<?php echo $this->get_field_id('error'); ?>"><?php echo __('Error correction', $scqrcode_domain) ?>:
            <select id="<?php echo $this->get_field_id('error'); ?>" name="<?php echo $this->get_field_name('error'); ?>">
                <option value="L" <?php if ( $error == 'L' ) echo 'selected="selected"'; ?> />L</option>
                <option value="M" <?php if ( $error == 'M' ) echo 'selected="selected"'; ?> />M</option>
                <option value="Q" <?php if ( $error == 'Q' ) echo 'selected="selected"'; ?> />Q</option>
                <option value="H" <?php if ( $error == 'H' ) echo 'selected="selected"'; ?> />H</option>
            </select>
        </label></p>
        <p><label for="<?php echo $this->get_field_id('margin'); ?>"><?php echo __('Margin', $scqrcode_domain) ?>:
            <input id="<?php echo $this->get_field_id('margin'); ?>" name="<?php echo $this->get_field_name('margin'); ?>" type="text" value="<?php echo attribute_escape($margin); ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('border'); ?>"><?php echo __('Border', $scqrcode_domain) ?>:
            <input id="<?php echo $this->get_field_id('border'); ?>" name="<?php echo $this->get_field_name('border'); ?>" type="text" value="<?php echo attribute_escape($border); ?>" />
        </label></p>
        <?php
    }

    function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['content'] = strip_tags(stripslashes($new_instance['content']));
        $instance['before_content'] = stripslashes($new_instance['before_content']);
        $instance['after_content'] = stripslashes($new_instance['after_content']);
        $instance['size'] = strip_tags(stripslashes($new_instance['size']));
        $instance['encoding'] = strip_tags(stripslashes($new_instance['encoding']));
        $instance['error'] = strip_tags(stripslashes($new_instance['error']));
        $instance['margin'] = strip_tags(stripslashes($new_instance['margin']));
        $instance['border'] = strip_tags(stripslashes($new_instance['border']));
        return $instance;
    }
}

/* Shortcode */
add_shortcode('qrcode', 'scqrcode_shortcode');
function scqrcode_shortcode($atts, $content = null) {
    if ( $content == null ) $content = scqrcode_url();
    extract( shortcode_atts( array(
        'size' => '125',
        'encoding' => 'UTF-8',
        'error' => 'L',
        'margin' => '2',
        'border' => '0',
    ), $atts ) );
    return '<img src="http://chart.apis.google.com/chart?cht=qr&chs=' . $size . 'x' . $size . '&chl=' . urlencode($content) . '&choe=' . $encoding . '&chld=' . $error . '|' . $margin . '" width="' . $size . '" height="' . $size . '" border="' . $border . '" />';
}

/* Options Page */
if ( is_admin() ) { add_action('admin_menu', 'scqrcode_menu'); }
function scqrcode_menu() {
    add_options_page( 'Super Cool QRCode', 'Super Cool QRCode', 8, __FILE__, 'scqrcode_options' );
}
function scqrcode_options() {
    global $scqrcode_domain, $scqrcode_dir;
    $scqrcode_data = get_plugin_data(__FILE__);
    ?>
    <div class="wrap">
        <h2>Super Cool QRCode - Lifehacker Edition</h2>
            <h3><?php echo __('Version Note', $scqrcode_domain) ?></h3>
                <p><?php echo __('Since a mention from <a rel="nofollow" href="http://lifehacker.com/#!5777173/add-qr-codes-to-your-wordpress-blog" target="_blank">lifehacker.com</a> made me stop been lazy and fix some bugs, actually, rewrite almost all code and add multi-widget support, i decided to call version 0.0.7 "<strong>Lifehacker Edition</strong>", in honor of the blog that made me get out of chair and do something!', $scqrcode_domain) ?></p>
            <h3><?php echo __('Widget', $scqrcode_domain) ?></h3>
                <p><?php echo __('Multi-widgets is supported, so you can add as many as you want, and since they are generated using <a rel="nofollow" href="http://code.google.com/apis/chart/" target="_blank">Google Chart API</a> none significant load is added to your server.', $scqrcode_domain) ?></p>
                <p><?php echo __('There are several options you que configure in the widgets, they are:', $scqrcode_domain) ?></p>
                <table id="inactive-plugins-table" class="widefat">
                    <thead><tr>
                        <th style="width:130px;"><?php echo __('Option', $scqrcode_domain); ?></th>
                        <th><?php echo __('Description', $scqrcode_domain); ?></th>
                    </tr></thead>
                    <tr>
                        <td><?php echo __('Title', $scqrcode_domain) ?></td>
                        <td><?php echo __('The title that will be showed in the widget block in your blog', $scqrcode_domain); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Content', $scqrcode_domain) ?></td>
                        <td><?php echo __('The content that will be encoded in the QR code, if you leave it blank the current blog or article url will be used', $scqrcode_domain); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Content wrapping', $scqrcode_domain) ?></td>
                        <td><?php echo __('This is the code the will go before and after the QR code, you can use it to add any code you want around it', $scqrcode_domain); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Advanced options', $scqrcode_domain) ?></td>
                        <td><?php echo __('Here you choose the QR code encoding options, the same way at the shortcode', $scqrcode_domain); ?></td>
                    </tr>
                </table>
            <h3><?php echo __('Shortcode', $scqrcode_domain) ?></h3>
                <p><?php echo __('If you simply use <strong>[qrcode]</strong> a QR code with a link to the current URL will be rendered, if you use <strong>[qrcode]</strong><em>content</em><strong>[/qrcode]</strong> a QR code with <em>content</em> will be rendered.', $scqrcode_domain) ?></p>
                <p><?php echo __('You can overwrite any default value passing it as a parameter in the shortcode, ex: <strong>[sqrcode size="150" link="true"]</strong><em>content</em><strong>[/sqrcode]</strong>, will render a QR code with 150px of side, possible options are:', $scqrcode_domain) ?></p>
                <table id="inactive-plugins-table" class="widefat">
                    <thead><tr>
                        <th style="width:100px;"><?php echo __('Parameter', $scqrcode_domain); ?></th>
                        <th><?php echo __('Description', $scqrcode_domain); ?></th>
                    </tr></thead>
                    <tr>
                        <td>size</td>
                        <td><?php echo __('The size of the generated QR code image. it is always a square, so you only need to set one side', $scqrcode_domain); ?></td>
                    </tr>
                    <tr>
                        <td>encoding</td>
                        <td><?php echo __('Specifies how the output is encoded. Available encodings are Shift_JIS, UTF-8, or ISO-8859-1', $scqrcode_domain); ?></td>
                    </tr>
                    <tr>
                        <td>error</td>
                        <td><?php echo __('Error correction level:<br/><strong>L</strong> allows 7% of a QR code to be restored<br/><strong>M</strong> allows 15% of a QR code to be restored<br/><strong>Q</strong> allows 25% of a QR code to be restored<br/><strong>H</strong> allows 30% of a QR code to be restored', $scqrcode_domain); ?></td>
                    </tr>
                    <tr>
                        <td>margin</td>
                        <td><?php echo __('Defines the margin (or blank space) around the QR code', $scqrcode_domain); ?></td>
                    </tr>
                    <tr>
                        <td>border</td>
                        <td><?php echo __('The border thickness of the image', $scqrcode_domain); ?></td>
                    </tr>
                </table>
            <h3><?php echo __('Usefull Links', $scqrcode_domain) ?></h3>
                <p><?php echo __('<a rel="nofollow" href="http://zxing.org/w/decode.jspx" target="_blank">ZXing Decoder Online</a>: decode your QR code in a online interface<br /><a rel="nofollow" href="http://code.google.com/apis/chart/" target="_blank">Google Chart API</a>: the API used by this plugin to generate the codes', $scqrcode_domain) ?></p>
        <hr />
        <p><?php echo __('<strong>Note</strong>: i\'m learning PHP & Wordpress coding and using this plugin to study, so if you have any idea or any kind of suggestion please contact me.', $scqrcode_domain) ?></p>
        <p><?php printf(__('<a href="%1$s">%2$s v%3$s</a> by <a href="mailto:"%4$s?Subject=Super%20Cool%20QRCode">%5$s</a>', $scqrcode_domain), $scqrcode_data['PluginURI'], $scqrcode_data['Name'], $scqrcode_data['Version'], $scqrcode_data['AuthorURI'], $scqrcode_data['Author']); ?></p>
    </div>
    <?php
}

?>