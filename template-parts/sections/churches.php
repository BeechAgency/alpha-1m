<?php 
    $fields = array(
        'title_churches',
        'secound_title_churches',
        'description_churches'
    );

    foreach($fields as $field) {
        $$field = get_field($field);
    }
?>
<section class="section churches" id="churches" data-scroll data-scroll-call="sectionScroll"  data-scroll-repeat data-section-order="3">

    <div class="container has-gutter title-wrap">
        <h2 class="section-title text-5xl text-center text-gradient-3" data-scroll data-scroll-speed="-0.05" id="churches-title">
            <span><?= $title_churches; ?></span><br />
            <span class="font-poppins font-bold"><?= $secound_title_churches; ?></span>
        </h2>
        <p class="section-description text-center wrap:narrow"  data-scroll data-scroll-speed="-0.05"><?= $description_churches; ?></p>
    </div>

    <?php get_template_part( 'template-parts/components/church', 'awesomeness', array() ); ?>

</section>