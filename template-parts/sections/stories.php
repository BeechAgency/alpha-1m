<?php 
    $fields = array(
        'title_stories',
        'description_stories',
        'items_stories'
    );

    foreach($fields as $field) {
        $$field = get_field($field);
    }

    $scroll_speeds = array(
        0 => '-0.15', // feature
        1 => '0.1', // sam
        2 => '-0.01', // cait
        3 => '-0.07', // josie
        4 => '0.07', // linda
        5 => '-0.15', // paige / Nick
        6 => '-0.35', // simon / evelyn
        7 => '0.07', // kate / Elke
        8 => '0.01', // laura
    );
?>
<section class="section stories" id="stories" data-scroll data-scroll-event-progress="storiesSectionScroll" data-scroll-call="sectionScroll"  data-scroll-repeat data-section-order="2">
    <div class="stories__inner">
        <div class="container has-gutter title-wrap" data-scroll data-scroll-speed="-0.2" data-scroll-position="start,end">
            <h2 class="section-title text-7xl wrap:content text-center" id="stories-title"><?= $title_stories; ?></h2>
            <p class="section-description wrap:narrow text-center"><?= $description_stories; ?></p>
        </div>

        <div class="container has-gutter stories-wrap grid">
            <?php 
                $i = 0;
                foreach($items_stories as $item):
                    $fields = array(
                        'name', 'church', 'image', 'video_preview', 'video_url'
                    );

                    foreach($fields as $field) {
                        $$field = $item[$field];
                    }
            ?>
            <div class="alpha-story__wrap" data-story-index="<?= $i; ?>">
                <div class="alpha-story z:mg rel"  
                    data-story-index="<?= $i; ?>" 
                    data-scroll data-scroll-speed="<?= $scroll_speeds[$i]; ?>" 
                    data-scroll-position="start,end"
                    data-video-id="<?= !empty($video_url) ?get_youtube_id($video_url) : ''; ?>"
                    data-video-name="<?= $name; ?>"
                    data-video-church="<?= $church; ?>"
                    >
                    <?php echo the_video($video_preview, array('image' => $image)); ?>
                    <div class="alpha-story__inner flex-row align-end space-between">
                        <div class="alpha-story__content">
                            <h3 class="alpha-story__name <?= $i === 0 ? 'text-4xl' : 'text-3xl' ?>"><?= $name; ?></h3>
                            <p class="alpha-story__church <?= $i === 0 ? 'text-xl' : 'text-base' ?>"><?= $church; ?></p>
                        </div>
                        <button class="alpha-story__cta cta">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" class="icon">
                                    <path d="M16 6.56881L15.319 4.48692L10.8849 5.91803L13.6253 2.17128L11.8424 0.884537L9.102 4.63128V0H6.89818V4.63128L4.15759 0.884537L2.37466 2.17128L5.11525 5.91803L0.681015 4.48692L0 6.56881L4.43406 7.99991L0 9.43119L0.681015 11.5131L5.11525 10.082L2.37466 13.8287L4.15759 15.1155L6.89818 11.3687V16H9.102V11.3687L11.8424 15.1155L13.6253 13.8287L10.8849 10.082L15.319 11.5131L16 9.43119L11.5659 7.99991L16 6.56881Z" fill="white"/>
                                </svg>
                            </span>
                            <span class="text">WATCH</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php
                    $i++;
                endforeach;
            ?>

        </div>
        <?php get_template_part( 'template-parts/components/church', 'names', array() ); ?>
    </div>
    <div class="gradient-overlay bg-gradient-4 fade-in"></div>
    <?php get_template_part( 'template-parts/components/gradient-picker', null, array() ); ?>
</section>