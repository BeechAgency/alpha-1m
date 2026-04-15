<?php
    $post_ids = get_posts([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'fields'         => 'ids',
    ]);

    $rows = array();
    $currentRow = array();
    $currentLength = 0;

    if(!empty($post_ids)): 

        if(is_countable( $post_ids ) && count($post_ids) < 32) {

            $post_ids = array_merge($post_ids, $post_ids);

            shuffle($post_ids);
        }

        foreach($post_ids as $post_id):

            $fields = array(
                'name', 'church', 'preview'
            );

            foreach($fields as $field) {
                $$field = get_field($field, $post_id);
            }

            $length = strlen($church);

            if ($currentLength > 50) {
                $rows[] = $currentRow;
                $currentRow = array();
                $currentLength = 0;
            }

            $church_item = array();

            $church_item['church'] = $church;
            $church_item['preview'] = $preview;
            $church_item['name'] = $name;
            $church_item['post_id'] = $post_id;

            $currentRow[] = $church_item;

            $currentLength += $length + (count($currentRow) > 1 ? 2 : 0);

        endforeach;

        // Add the final row if needed
        if (!empty($currentRow)) {

            // Always ensure the final row has two items and is full
            if(count($currentRow) !== 0 && count($currentRow) < 2) {
                $currentRow[] = $rows[0][0];
                $currentRow[] = $rows[1][0];
            }
            
            $rows[] = $currentRow;
        }
    endif;
?>
<div class="church-awesomeness">
    <div class="church-awesomeness__names">
    <?php 
    if(!empty($rows)): 
        foreach($rows as $row): 
            $row_count = is_countable($row) ? count($row) : 0;
            $row_index = 0;
        ?>
            <div class="church-awesomeness__row text-7xl font-instrument" data-scroll data-scroll-position="middle,middle" data-scroll-event-progress="churchAwesomenessTrack">
                <div class="church-awesomeness__track has-gutter" data-church-row="<?= $row_index ?>" >
        <?php
            foreach($row as $church_item):
                $church = $church_item['church'];
                $id = $church_item['post_id'];
                $name = $church_item['name'];
                $preview = $church_item['preview'];

                $row_index++; ?>
                    <div class="church-awesomeness__item text-gradient-<?= rand(1, 7); ?>" 
                        data-church-id="<?= $id ?>"
                        data-church-name="<?= $name ?>"
                        data-church-preview="<?= $preview ?>"
                        data-church-church="<?= $church ?>"
                        >
                        <?= $church; ?>
                    </div>
                <?php
                if($row_index < $row_count):
                    echo '<div class="church-awesomeness__divider">•</div>';
                endif;
            endforeach;
        ?>
                </div>
            </div>
    <?php 
        endforeach;
    endif; ?>
    </div>
    
    <div class="church-awesomeness__cards-track">
        <div class="church-awesomeness__cards">
            <!--
            <div class="church-awesomeness__card" style="--_card-index : 1;" data-church-card-index="1">
                <div class="church-awesomeness__card--inner">
                    <h4 class="preview text-2xl bold font-poppins">Tridiv's only regret is not coming to Jesus sooner!</h4>

                    <div class="name text-red font-instrument text-2xl">Samuel</div>
                    <div class="church bold text-red">Horizon City Church</div>
                </div>
            </div>
            <div class="church-awesomeness__card" style="--_card-index : 2;"  data-church-card-index="2">
                <div class="church-awesomeness__card--inner">
                    <h4 class="preview text-2xl bold font-poppins">Tridiv's only regret is not coming to Jesus sooner!</h4>

                    <div class="name text-red font-instrument text-2xl">Samuel</div>
                    <div class="church bold text-red">Horizon City Church</div>
                </div>
            </div>
            <div class="church-awesomeness__card" style="--_card-index : 3;"  data-church-card-index="3">
                <div class="church-awesomeness__card--inner">
                    <h4 class="preview text-2xl bold font-poppins">Tridiv's only regret is not coming to Jesus sooner!</h4>

                    <div class="name text-red font-instrument text-2xl">Samuel</div>
                    <div class="church bold text-red">Horizon City Church</div>
                </div>
            </div>
            <div class="church-awesomeness__card" style="--_card-index : 4;"  data-church-card-index="3">
                <div class="church-awesomeness__card--inner">
                    <h4 class="preview text-2xl bold font-poppins">Tridiv's only regret is not coming to Jesus sooner!</h4>

                    <div class="name text-red font-instrument text-2xl">Samuel</div>
                    <div class="church bold text-red">Horizon City Church</div>
                </div>
            </div>
            <div class="church-awesomeness__card" style="--_card-index : 5;"  data-church-card-index="3">
                <div class="church-awesomeness__card--inner">
                    <h4 class="preview text-2xl bold font-poppins">Tridiv's only regret is not coming to Jesus sooner!</h4>

                    <div class="name text-red font-instrument text-2xl">Samuel</div>
                    <div class="church bold text-red">Horizon City Church</div>
                </div>
            </div>
        </div>
        -->
    </div>
</div>

<template id="church-awesomeness__card">
    <div class="church-awesomeness__card" style="--_card-index : 1;"  data-church-card-index="1">
        <div class="church-awesomeness__card--inner">
            <h4 class="preview text-2xl bold font-poppins">Tridiv's only regret is not coming to Jesus sooner!</h4>

            <div class="name text-red font-instrument text-2xl">Samuel</div>
            <div class="church bold text-red">Horizon City Church</div>
        </div>
    </div>
</template>

<template id="church-awesomeness__dialoig">
    
</template>