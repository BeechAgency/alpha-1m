<?php 
    // This template is always used within another author component.
    $social = isset($args['social']) ? $args['social'] : array();
    
    if( !empty($social) ): 
?>
<div class="social-row flex-row">
    <?php 
        foreach($social as $network) {
            if(!empty($network)) {
                extract($network);
                echo "<a href='$link' target='_blank' title='$name' class='social-icon $name'>";
                echo the_image($image);
                echo '</a>';
            }
        }
    ?>
</div>
<?php endif; ?>