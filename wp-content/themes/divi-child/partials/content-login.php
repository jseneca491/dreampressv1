<a href="#login-modal" class="trigger-custom dp-html">Login</a>
<div id="login-modal" style="display: none;">
    <div class="ds-login-container clearfix">
        <button data-iziModal-close class="icon-close">x</button>
        <form id="login" action="login" method="post">
            <p class="status"></p>
                
                <input id="username" type="text" name="username" placeholder="<?php esc_attr_e('Username','yourtheme') ?>">
                <input id="password" type="password" name="password" placeholder="<?php esc_attr_e('Password','yourtheme') ?>">
            
            <div class="forgotten_box">
                <a class="lost" href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_attr_e('Lost your password?','yourtheme') ?></a>
            </div>
            
            <input class="submit_button" type="submit" value="Login" name="submit">
            <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
        </form>
    </div>
</div>