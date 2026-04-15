<?php 
    $args = wp_parse_args( 
        $args, 
        array('post_id' => get_the_ID())
    );

    extract($args);

    $fields = array(
        'marquee_text',
        'marquee_link',
        'title_footer',
        'gallery'
    );

    foreach($fields as $field) {
        $$field = get_field($field, $post_id);
    }
?>
<section class="section gallery" id="gallery" data-scroll data-scroll-call="sectionScroll"  data-scroll-repeat data-section-order="6">

    <hr data-scroll />
    <div class="marquee__wrap"> 
        <div class="marquee__track">
            <div class="marquee">
                <div class="marquee__group">
                    <div class="marquee__item flex-row">
                        <span class="marquee__item-text text-lg"><?= $marquee_text ?></span>
                        <a href="<?= $marquee_link['url']; ?>" class="marquee__item-link" target="<?= $marquee_link['target']; ?>" title="<?= $marquee_link['title']; ?>"><span><?= $marquee_link['title']; ?></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr data-scroll />

    <div class="container has-gutter title-wrap">
        <h2 class="section-title text-5xl text-center text-gradient-5" id="gallery-title">
            <span><?= $title_footer; ?></span><br />
        </h2>
    </div>

    <div class="gallery flex-row justify-center z:mg rel" data-scroll data-scroll-repeat data-scroll-position="end,start">
        <?php  
        
            if(!empty($gallery)):

                foreach($gallery as $item):
                    echo '<div class="gallery-item">';
                    if(!empty($item['video_url'])) {
                        echo the_video($item['video_url'], array('classes' => 'aspect:1/1', 'image' => $item['image']));
                    } else {
                        echo the_image($item['image'], array('classes' => 'aspect:1/1'));
                    }
                    echo '</div>';
                endforeach;

            endif;
        
        ?>
    </div>
</section>