<?php 
    $fields = array(
        'title_play',
        'items_cards',
        'description_play'
    );

    foreach($fields as $field) {
        $$field = get_field($field);
    }
?>
<section class="section cards" id="cards" data-scroll data-scroll-call="sectionScroll" data-scroll-repeat data-section-order="5">
    <h2 class="section-title text-7xl text-center text-gradient-6 wrap:content" id="cards-title">
        <?= $title_play; ?>
    </h2>
    <?= !empty($description_play) ? '<p class="section-description text-center wrap:narrow">' . $description_play . '</p>' : ''; ?>
    <div class="has-gutter">
        <div class="cards-wrap grid cols-2 wrap:content">
            <?php 
            $item_index = 0;
            foreach($items_cards as $item): 
            
                ?>
                <div class="card bg-white text-black" style="--_card-index: <?= $item_index; ?>" data-scroll data-scroll-position="end,start">
                    <div class="card__image">
                        <?php echo the_image($item['image'], array('classes' => 'card__image aspect:16/9' )); ?>
                    </div>
                    <div class="card__content">
                        <h3 class="card__title text-3xl text-center">
                            <?= $item['title']; ?>
                        </h3>
                        <p class="card__description text-center">
                            <?= $item['description']; ?>
                        </p>


                        <?php 
                        if(!empty($item['buttons'])):
                            echo '<div class="button-row flex-row justify-center">';
                            foreach($item['buttons'] as $item) {
                                $button = $item['button'];

                                if(empty($button)) {
                                    continue;
                                }

                                $button['classes'] = ' cta__card';

                                echo the_cta($button);
                            }
                            echo '</div>';
                        endif;
                            
                        ?>
                    </div>
                </div>
            <?php 
                $item_index++;
            endforeach; ?>
        </div>
    </div>
</section>