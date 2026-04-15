<?php 
    /**
     * This template part handles the logic and data handling for the different header layouts.
     */

    $defaults = array(
        'post_id' => get_the_ID(),
        'type' => 'page'
    );
    $args = wp_parse_args( $args, $defaults );
    extract( $args );

    $field_list = array(
        'title',
        'text',
        'header_ctas',
        'header_video',
        '_thumnail_id',
        'intro'
    );

    $field_values = array();
    $field_values['post_type'] = get_post_type();
    $field_values['post_id'] = $args['post_id'];
    $field_values['image'] = get_post_thumbnail_id($post_id );

    // Get all the field_list from the field list
    foreach($field_list as $field) {
        $field_values[$field] = get_field($field, $post_id);
    }

    $header_ctas = $field_values['header_ctas'];
    $field_values['video'] = !empty($field_values['header_video']) ? $field_values['header_video'] : array();
    $field_values['has_video'] = !empty($field_values['video']) && !empty($field_values['video']['id']);
    
    $field_values['title'] = !empty($field_values['title']) ? $field_values['title'] : '';
    $field_values['text'] = !empty($field_values['text']) ? $field_values['text'] : '';
    $field_values['link'] = !empty($header_ctas['link']) ? $header_ctas['link'] : array();
    $field_values['link_secondary'] = !empty($header_ctas['link_secondary']) ? $header_ctas['link_secondary'] : array();

    // If type is somehow set to null everything will die.
    extract($field_values);    
?>

<header class="entry-header page-header has-overlay" data-scroll data-scroll-call="sectionScroll" id="header"  data-scroll-repeat data-section-order="1">
    <div class="page-header__content">
        <div class="grid container has-gutter">
            <div class="span-full md:span-full title-wrap align-center md:start-auto">

                <h1 class="text-5xl text-center"  data-scroll data-scroll-speed="0.05">
                    <span class="font-instrument"><?= $title; ?></span><br />
                    <span class="font-poppins font-bold"><?= $text; ?></span>
                </h1>

                <?php if(!empty($intro)): ?>
                    <p class="text-center wrap:narrow"  data-scroll data-scroll-speed="0.075">
                        <?= $intro ?>
                    </p>
                <?php endif; ?>

                <div class="flex-row button-row justify-center" data-scroll data-scroll-speed="0.075">
                    <?= the_cta($link); ?> 
                    <?= the_cta($link_secondary); ?> 
                </div>

                <div class="star-wrap" data-scroll data-scroll-speed="-0.3">
                    <svg class="star spin" width="523" height="523" viewBox="0 0 523 523" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.4227 343.511L27.3846 379.632L510.574 179.489L495.612 143.368L12.4227 343.511Z" fill="url(#paint0_linear_2358_170)"/>
                        <path d="M62.7656 432.584L90.4117 460.23L460.229 90.4133L432.582 62.7672L62.7656 432.584Z" fill="url(#paint1_linear_2358_170)"/>
                        <path d="M143.37 495.614L179.491 510.576L379.634 27.3866L343.513 12.4247L143.37 495.614Z" fill="url(#paint2_linear_2358_170)"/>
                        <path d="M241.951 523H281.049L281.049 0H241.951L241.951 523Z" fill="url(#paint3_linear_2358_170)"/>
                        <path d="M343.511 510.577L379.632 495.615L179.489 12.4265L143.368 27.3884L343.511 510.577Z" fill="url(#paint4_linear_2358_170)"/>
                        <path d="M432.584 460.234L460.23 432.588L90.4133 62.7715L62.7672 90.4175L432.584 460.234Z" fill="url(#paint5_linear_2358_170)"/>
                        <path d="M495.614 379.63L510.576 343.509L27.3865 143.366L12.4246 179.487L495.614 379.63Z" fill="url(#paint6_linear_2358_170)"/>
                        <path d="M0 241.951L0 281.049L523 281.049V241.951L0 241.951Z" fill="#E42312"/>
                        <defs>
                        <linearGradient id="paint0_linear_2358_170" x1="35.4273" y1="357.566" x2="487.349" y2="165.532" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#41B36E"/>
                        <stop offset="0.05" stop-color="#41B583"/>
                        <stop offset="0.13" stop-color="#42BAA5"/>
                        <stop offset="0.22" stop-color="#43BDBF"/>
                        <stop offset="0.31" stop-color="#44C0D2"/>
                        <stop offset="0.41" stop-color="#44C1DE"/>
                        <stop offset="0.51" stop-color="#45C2E2"/>
                        <stop offset="1" stop-color="#FBA5E4"/>
                        </linearGradient>
                        <linearGradient id="paint1_linear_2358_170" x1="87.2358" y1="438.651" x2="434.516" y2="85.6198" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#E4BC81"/>
                        <stop offset="0.08" stop-color="#E6A56E"/>
                        <stop offset="0.19" stop-color="#E98B58"/>
                        <stop offset="0.31" stop-color="#EB7848"/>
                        <stop offset="0.43" stop-color="#EC6C3F"/>
                        <stop offset="0.56" stop-color="#ED693C"/>
                        <stop offset="1" stop-color="#F8A91F"/>
                        </linearGradient>
                        <linearGradient id="paint2_linear_2358_170" x1="164.766" y1="493.777" x2="356.8" y2="32.6574" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#FBA5E4"/>
                        <stop offset="0.09" stop-color="#DC95DE"/>
                        <stop offset="0.24" stop-color="#B280D6"/>
                        <stop offset="0.37" stop-color="#9370D0"/>
                        <stop offset="0.48" stop-color="#8067CD"/>
                        <stop offset="0.56" stop-color="#7A64CC"/>
                        <stop offset="0.64" stop-color="#6580AA"/>
                        <stop offset="0.73" stop-color="#559690"/>
                        <stop offset="0.81" stop-color="#4AA67D"/>
                        <stop offset="0.9" stop-color="#43AF71"/>
                        <stop offset="1" stop-color="#41B36E"/>
                        </linearGradient>
                        <linearGradient id="paint3_linear_2358_170" x1="261.497" y1="523" x2="261.497" y2="0" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#6D5DCA"/>
                        <stop offset="0.11" stop-color="#6D53B5"/>
                        <stop offset="0.25" stop-color="#6E4AA3"/>
                        <stop offset="0.4" stop-color="#6E4597"/>
                        <stop offset="0.55" stop-color="#6F4494"/>
                        <stop offset="1" stop-color="#ED693C"/>
                        </linearGradient>
                        <linearGradient id="paint4_linear_2358_170" x1="358.62" y1="499.363" x2="166.586" y2="29.0454" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#D6D6D6"/>
                        <stop offset="0.01" stop-color="#D1D5D6"/>
                        <stop offset="0.13" stop-color="#9FCEDA"/>
                        <stop offset="0.25" stop-color="#78C9DD"/>
                        <stop offset="0.35" stop-color="#5CC5E0"/>
                        <stop offset="0.44" stop-color="#4BC2E1"/>
                        <stop offset="0.51" stop-color="#45C2E2"/>
                        <stop offset="0.55" stop-color="#5BC1D4"/>
                        <stop offset="0.62" stop-color="#84BFBB"/>
                        <stop offset="0.69" stop-color="#A7BEA6"/>
                        <stop offset="0.76" stop-color="#C1BD95"/>
                        <stop offset="0.84" stop-color="#D4BC8A"/>
                        <stop offset="0.92" stop-color="#E0BC83"/>
                        <stop offset="1" stop-color="#E4BC81"/>
                        </linearGradient>
                        <linearGradient id="paint5_linear_2358_170" x1="442.124" y1="437.512" x2="82.1978" y2="86.7848" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#E4BC81"/>
                        <stop offset="0.02" stop-color="#DDB684"/>
                        <stop offset="0.19" stop-color="#B2949F"/>
                        <stop offset="0.33" stop-color="#927BB2"/>
                        <stop offset="0.46" stop-color="#7F6CBE"/>
                        <stop offset="0.54" stop-color="#7967C3"/>
                        <stop offset="0.61" stop-color="#765DB5"/>
                        <stop offset="0.73" stop-color="#724FA3"/>
                        <stop offset="0.86" stop-color="#6F4697"/>
                        <stop offset="1" stop-color="#6F4494"/>
                        </linearGradient>
                        <linearGradient id="paint6_linear_2358_170" x1="497.281" y1="357.689" x2="35.0127" y2="169.103" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#ED693C"/>
                        <stop offset="0.52" stop-color="#41B36E"/>
                        <stop offset="0.56" stop-color="#5BB471"/>
                        <stop offset="0.63" stop-color="#84B675"/>
                        <stop offset="0.7" stop-color="#A7B879"/>
                        <stop offset="0.77" stop-color="#C1BA7D"/>
                        <stop offset="0.84" stop-color="#D4BB7F"/>
                        <stop offset="0.92" stop-color="#E0BB80"/>
                        <stop offset="1" stop-color="#E4BC81"/>
                        </linearGradient>
                        </defs>
                    </svg>
                    </div>
            </div>
        </div>
    </div>
    <?php if(!empty($image)): ?>
    <div class="page-header__image-wrap has-overlay <?= $has_video ? ' has-video' : ''; ?>">
        <?php 
        if($has_video) {
            echo the_video($video['id'], array('type' => $video['type'], 'style' => $video['style'], 'image' => $image, 'classes' => 'page-header__video' ));
        } else {
            echo the_image($image, array('classes' => 'page-header__image'  )); 
        }
        ?>
    </div>
    <?php endif; ?>
    
</header>